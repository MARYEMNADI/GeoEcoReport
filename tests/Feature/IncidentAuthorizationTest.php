<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        Role::firstOrCreate(['name' => $role]);
        $user = User::factory()->create();
        $user->addRole($role);
        return $user;
    }

    private function makeIncident(array $data = []): Incident
    {
        $category = Category::factory()->create();
        return Incident::factory()->create(array_merge(
            ['category_id' => $category->id],
            $data
        ));
    }

    public function test_guest_cannot_access_incidents(): void
    {
        $response = $this->get(route('incidents.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_citoyen_can_create_incident(): void
    {
        $citoyen = $this->makeUser('citoyen');

        $response = $this->actingAs($citoyen)->get(route('incidents.create'));

        $response->assertOk();
    }

    public function test_technicien_cannot_create_incident(): void
    {
        $technicien = $this->makeUser('technicien');

        $response = $this->actingAs($technicien)->get(route('incidents.create'));

        $response->assertForbidden();
    }

    public function test_citoyen_cannot_edit_others_incident(): void
    {
        $owner = $this->makeUser('citoyen');
        $other = $this->makeUser('citoyen');
        $incident = $this->makeIncident(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->get(route('incidents.edit', $incident));

        $response->assertForbidden();
    }

    public function test_admin_can_delete_incident(): void
    {
        $admin = $this->makeUser('administrateur');
        $incident = $this->makeIncident();

        $response = $this->actingAs($admin)->delete(route('incidents.destroy', $incident));

        $response->assertRedirect(route('incidents.index'));
        $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    }
}