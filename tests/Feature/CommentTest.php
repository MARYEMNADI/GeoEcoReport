<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
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

    public function test_citoyen_can_comment(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $incident = $this->makeIncident();

        $this->actingAs($citoyen)->post(
            route('comments.store', $incident),
            ['content' => 'Merci de traiter ce dossier.']
        );

        $this->assertDatabaseHas('comments', [
            'incident_id' => $incident->id,
            'user_id' => $citoyen->id,
        ]);
    }

    public function test_guest_cannot_comment(): void
    {
        $incident = $this->makeIncident();

        $response = $this->post(
            route('comments.store', $incident),
            ['content' => 'Test']
        );

        $response->assertRedirect(route('login'));
    }

    public function test_content_is_required(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $incident = $this->makeIncident();

        $response = $this->actingAs($citoyen)->post(
            route('comments.store', $incident),
            ['content' => '']
        );

        $response->assertSessionHasErrors('content');
    }

    public function test_comment_appears_on_incident_page(): void
    {
        $citoyen = $this->makeUser('citoyen');
        $incident = $this->makeIncident();

        $incident->comments()->create([
            'user_id' => $citoyen->id,
            'content' => 'Commentaire visible ici.',
        ]);

        $response = $this->actingAs($citoyen)->get(route('incidents.show', $incident));

        $response->assertSee('Commentaire visible ici.');
    }
}