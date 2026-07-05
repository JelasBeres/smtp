<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ContactImport;
use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_update_and_delete_user(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'role' => User::ROLE_OPERATOR,
            'password' => 'password123',
        ])->assertRedirect();

        $user = User::query()->where('email', 'operator@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created', 'auditable_id' => $user->id]);

        $this->actingAs($superAdmin)->put(route('users.update', $user), [
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'role' => User::ROLE_VIEWER,
            'password' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'viewer@example.com', 'role' => User::ROLE_VIEWER]);

        $this->actingAs($superAdmin)->delete(route('users.destroy', $user))->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_super_admin_cannot_manage_users(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('users.index'))->assertForbidden();
    }

    public function test_log_pages_render_real_records(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        AuditLog::query()->create(['user_id' => $admin->id, 'action' => 'user.created']);
        ContactImport::query()->create(['filename' => 'contacts.csv', 'source' => 'manual', 'consent_type' => 'opt_in', 'created_by' => $admin->id]);
        WebhookEvent::query()->create(['provider' => 'smtp', 'provider_event_id' => 'evt-1', 'event_type' => 'delivered', 'payload' => ['email' => 'a@example.com'], 'status' => 'processed']);

        $this->actingAs($admin)->get(route('audit-logs.index'))->assertOk()->assertSee('user.created');
        $this->actingAs($admin)->get(route('import-history.index'))->assertOk()->assertSee('contacts.csv');
        $this->actingAs($admin)->get(route('webhook-logs.index'))->assertOk()->assertSee('evt-1');
        $this->actingAs($admin)->get(route('settings.index'))->assertOk()->assertSee('Application');
    }
}
