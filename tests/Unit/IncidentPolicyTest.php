<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use App\Policies\IncidentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private IncidentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new IncidentPolicy();
    }

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

    public function test_citoyen_and_admin_can_create(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $technicien = $this->makeUser('technicien');

        $this->assertTrue($this->policy->create($citoyen));
        $this->assertFalse($this->policy->create($technicien));
    }

    public function test_citoyen_can_update_only_pending_own_incident(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $pending = $this->makeIncident(['user_id' => $citoyen->id, 'status' => 'En attente']);
        $enCours = $this->makeIncident(['user_id' => $citoyen->id, 'status' => 'En cours de traitement']);

        $this->assertTrue($this->policy->update($citoyen, $pending));
        $this->assertFalse($this->policy->update($citoyen, $enCours));
    }

    public function test_only_technicien_and_admin_can_change_status(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $technicien = $this->makeUser('technicien');
        $incident = $this->makeIncident();

        $this->assertFalse($this->policy->changeStatus($citoyen, $incident));
        $this->assertTrue($this->policy->changeStatus($technicien, $incident));
    }

    public function test_only_admin_can_assign(): void
    {
        $technicien = $this->makeUser('technicien');
        $admin = $this->makeUser('administrateur');
        $incident = $this->makeIncident();

        $this->assertFalse($this->policy->assign($technicien, $incident));
        $this->assertTrue($this->policy->assign($admin, $incident));
    }
}