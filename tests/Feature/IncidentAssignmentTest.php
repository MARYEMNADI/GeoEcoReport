<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        Role::firstOrCreate(['name' => $role]);
        $user = User::factory()->create();
        $user->addRole($role);
        return $user;
    }

    private function makeIncident(): Incident
    {
        $category = Category::factory()->create();
        return Incident::factory()->create(['category_id' => $category->id]);
    }

    public function test_admin_can_assign_technicien(): void
    {
        $admin = $this->makeUser('administrateur');
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident();

        $this->actingAs($admin)->post(
            route('incidents.assign', $incident),
            ['technicien_id' => $technicien->id]
        );

        $this->assertDatabaseHas('affectations', [
            'incident_id' => $incident->id,
            'technicien_id' => $technicien->id,
        ]);
    }

    public function test_assignment_sets_status_en_cours(): void
    {
        $admin = $this->makeUser('administrateur');
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident();

        $this->actingAs($admin)->post(
            route('incidents.assign', $incident),
            ['technicien_id' => $technicien->id]
        );

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'status' => 'En cours de traitement',
        ]);
    }

    public function test_citoyen_cannot_assign(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident();

        $response = $this->actingAs($citoyen)->post(
            route('incidents.assign', $incident),
            ['technicien_id' => $technicien->id]
        );

        $response->assertForbidden();
    }
}