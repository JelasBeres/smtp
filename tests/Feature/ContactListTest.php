<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_contact_list(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->post(route('contact-lists.store'), [
            'name' => 'Customers',
            'description' => 'Opt-in customers',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_lists', [
            'name' => 'Customers',
            'created_by' => $admin->id,
        ]);
    }

    public function test_contact_membership_is_idempotent_and_detachable(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $list = ContactList::query()->create(['name' => 'Customers', 'created_by' => $admin->id]);
        $contact = Contact::query()->create([
            'email' => 'member@example.com',
            'status' => Contact::STATUS_ACTIVE,
            'source' => 'test',
            'consent_type' => 'opt_in',
            'consent_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('contact-lists.contacts.attach', $list), ['contact_id' => $contact->id])->assertRedirect();
        $this->actingAs($admin)->post(route('contact-lists.contacts.attach', $list), ['contact_id' => $contact->id])->assertRedirect();

        $this->assertSame(1, $list->contacts()->count());

        $this->actingAs($admin)->delete(route('contact-lists.contacts.detach', [$list, $contact]))->assertRedirect();

        $this->assertSame(0, $list->contacts()->count());
    }

    public function test_viewer_cannot_manage_contact_lists(): void
    {
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        $this->actingAs($viewer)->post(route('contact-lists.store'), [
            'name' => 'Blocked',
        ])->assertForbidden();
    }
}
