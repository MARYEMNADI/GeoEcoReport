<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentStatusTest extends TestCase
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

    public function test_technicien_can_change_status(): void
    {
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident(['status' => 'En attente']);

        $this->actingAs($technicien)->patch(
            route('incidents.status.update', $incident),
            ['status' => 'En cours de traitement']
        );

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'status' => 'En cours de traitement',
        ]);
    }

    public function test_citoyen_cannot_change_status(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $incident = $this->makeIncident();

        $response = $this->actingAs($citoyen)->patch(
            route('incidents.status.update', $incident),
            ['status' => 'Résolu']
        );

        $response->assertForbidden();
    }

    public function test_invalid_status_is_rejected(): void
    {
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident();

        $response = $this->actingAs($technicien)->patch(
            route('incidents.status.update', $incident),
            ['status' => 'N importe quoi']
        );

        $response->assertSessionHasErrors('status');
    }
}