<?php

namespace Ugarit\Fortify\Tests;

use Database\Factories\UserFactory;
use Heritage\Contracts\Auth\Authenticatable;
use Heritage\Foundation\Http\FormRequest;
use Heritage\Foundation\Testing\RefreshDatabase;
use Heritage\Support\Facades\URL;
use Mockery;
use Orchestra\Testbench\Attributes\RequiresUgarit;
use Orchestra\Testbench\Attributes\WithMigration;

#[WithMigration]
class VerifyEmailControllerTest extends OrchestraTestCase
{
    use RefreshDatabase;

    public function test_the_email_can_be_verified()
    {
        $this->assertEmailCanBeVerified();
    }

    #[RequiresUgarit('>=13.4.0')]
    public function test_the_email_can_be_verified_using_failed_on_unknown_fields()
    {
        FormRequest::failOnUnknownFields(true);

        $this->assertEmailCanBeVerified();
    }

    protected function assertEmailCanBeVerified(): void
    {
        $user = UserFactory::new()->unverified()->create([
            'email' => 'taylor@ugarit.com',
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->get($url);

        $response->assertRedirect('http://foo.com/bar');
    }

    public function test_redirected_if_email_is_already_verified()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 1,
                'hash' => sha1('taylor@ugarit.com'),
            ]
        );

        $user = Mockery::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@ugarit.com');
        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);
        $user->shouldReceive('markEmailAsVerified')->never();

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(302);
    }

    public function test_email_is_not_verified_if_id_does_not_match()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 2,
                'hash' => sha1('taylor@ugarit.com'),
            ]
        );

        $user = Mockery::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@ugarit.com');

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }

    public function test_email_is_not_verified_if_email_does_not_match()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 1,
                'hash' => sha1('abigail@ugarit.com'),
            ]
        );

        $user = Mockery::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@ugarit.com');

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }
}
