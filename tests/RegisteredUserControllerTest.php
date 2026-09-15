<?php

namespace Ugarit\Fortify\Tests;

use Heritage\Contracts\Auth\Authenticatable;
use Heritage\Contracts\Auth\StatefulGuard;
use Ugarit\Fortify\Contracts\CreatesNewUsers;
use Ugarit\Fortify\Contracts\RegisterViewResponse;
use Mockery;

class RegisteredUserControllerTest extends OrchestraTestCase
{
    public function test_the_register_view_is_returned()
    {
        $this->mock(RegisterViewResponse::class)
                ->shouldReceive('toResponse')
                ->andReturn(response('hello world'));

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function test_users_can_be_created()
    {
        $this->mock(CreatesNewUsers::class)
                    ->shouldReceive('create')
                    ->andReturn(Mockery::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
                    ->shouldReceive('login')
                    ->once();

        $response = $this->post('/register', []);

        $response->assertRedirect('/home');
    }

    public function test_users_can_be_created_and_redirected_to_intended_url()
    {
        $this->mock(CreatesNewUsers::class)
                    ->shouldReceive('create')
                    ->andReturn(Mockery::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
                    ->shouldReceive('login')
                    ->once();

        $response = $this->withSession(['url.intended' => 'http://foo.com/bar'])
                        ->post('/register', []);

        $response->assertRedirect('http://foo.com/bar');
    }

    public function test_usernames_will_be_stored_case_insensitive()
    {
        app('config')->set('fortify.lowercase_usernames', true);

        $this->mock(CreatesNewUsers::class)
                    ->shouldReceive('create')
                    ->with([
                        'email' => 'taylor@ugarit.com',
                        'password' => 'password',
                    ])
                    ->once()
                    ->andReturn(Mockery::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
                    ->shouldReceive('login')
                    ->once();

        $response = $this->post('/register', [
            'email' => 'TAYLOR@UGARIT.COM',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
    }

    public function test_users_can_be_created_with_remember_option()
    {
        $this->mock(CreatesNewUsers::class)
                    ->shouldReceive('create')
                    ->once()
                    ->andReturn(Mockery::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
                    ->shouldReceive('login')
                    ->with(Mockery::type(Authenticatable::class), true)
                    ->once();

        $response = $this->post('/register', [
            'email' => 'taylor@ugarit.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect('/home');
    }
}
