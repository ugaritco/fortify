<?php

namespace Ugarit\Fortify\Tests;

use Heritage\Foundation\Auth\User;
use Heritage\Foundation\Testing\RefreshDatabase;
use Heritage\Support\Facades\Event;
use Ugarit\Fortify\Events\RecoveryCodesGenerated;

class RecoveryCodeControllerTest extends OrchestraTestCase
{
    use RefreshDatabase;

    public function test_new_recovery_codes_can_be_generated()
    {
        Event::fake();

        $user = TestTwoFactorRecoveryCodeUser::forceCreate([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@ugarit.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($user)->postJson(
            '/user/two-factor-recovery-codes'
        );

        $response->assertStatus(200);

        Event::assertDispatched(RecoveryCodesGenerated::class);

        $user->fresh();

        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertIsArray(json_decode(decrypt($user->two_factor_recovery_codes), true));
    }
}

class TestTwoFactorRecoveryCodeUser extends User
{
    protected $table = 'users';
}
