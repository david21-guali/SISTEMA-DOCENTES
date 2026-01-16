<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Innovation;
use App\Models\InnovationType;
use App\Notifications\InnovationVoted;
use App\Notifications\InnovationReviewCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class InnovationCommunityReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        
        InnovationType::factory()->create(['name' => 'Pedagógica']);
    }

    public function test_docente_can_vote_anonymously(): void
    {
        Notification::fake();
        
        $docente = User::factory()->create();
        $docente->assignRole('docente');

        $creator = User::factory()->create();
        $creator->assignRole('docente');
        
        $innovation = Innovation::factory()->create([
            'profile_id' => $creator->profile->id,
            'status' => 'en_revision',
            'review_deadline' => now()->addDays(3)
        ]);

        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Este es un comentario de prueba de mas de 20 caracteres.'
        ]);

        $response->assertRedirect(route('innovations.show', $innovation));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('innovation_reviews', [
            'innovation_id' => $innovation->id,
            'reviewer_id' => $docente->id,
            'vote' => 'approved'
        ]);

        $innovation->refresh();
        $this->assertEquals(100, $innovation->community_score);
        $this->assertEquals(1, $innovation->total_votes);

        Notification::assertSentTo($creator, InnovationVoted::class);
    }

    public function test_admin_cannot_vote(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $innovation = Innovation::factory()->create(['status' => 'en_revision']);

        $response = $this->actingAs($admin)->get(route('innovations.review', $innovation));
        $response->assertForbidden();

        $response = $this->actingAs($admin)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Comentario de prueba largo para pasar validacion.'
        ]);
        
        // El servicio lanza una excepción que se captura en el controlador regresando con error
        $response->assertSessionHas('error', 'Los administradores no pueden votar.');
    }

    public function test_creator_cannot_vote_own_innovation(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('docente');

        $innovation = Innovation::factory()->create([
            'profile_id' => $creator->profile->id,
            'status' => 'en_revision'
        ]);

        $response = $this->actingAs($creator)->get(route('innovations.review', $innovation));
        $response->assertSessionHas('error', 'No puedes votar tu propia innovación.');

        $response = $this->actingAs($creator)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Comentario de prueba largo para pasar validacion.'
        ]);
        
        $response->assertSessionHas('error', 'No puedes votar tu propia innovación.');
    }

    public function test_cannot_vote_after_deadline(): void
    {
        $docente = User::factory()->create();
        $docente->assignRole('docente');

        $innovation = Innovation::factory()->create([
            'status' => 'en_revision',
            'review_deadline' => now()->subDay() // Ya venció
        ]);

        $response = $this->actingAs($docente)->get(route('innovations.review', $innovation));
        $response->assertSessionHas('error', 'El período de votación ha finalizado.');

        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Comentario de prueba largo para pasar validacion.'
        ]);
        
        $response->assertSessionHas('error', 'El período de votación ha finalizado.');
    }

    public function test_cannot_vote_twice(): void
    {
        $docente = User::factory()->create();
        $docente->assignRole('docente');

        $innovation = Innovation::factory()->create(['status' => 'en_revision']);

        // Primer voto
        $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Primer comentario válido de prueba larga.'
        ]);

        // Segundo voto
        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'rejected',
            'comment' => 'Segundo comentario válido de prueba larga.'
        ]);
        
        $response->assertSessionHas('error', 'Ya has votado esta innovación.');
        $this->assertEquals(1, $innovation->reviews()->count());
    }

    public function test_vote_comment_length_validation(): void
    {
        $docente = User::factory()->create();
        $docente->assignRole('docente');

        $innovation = Innovation::factory()->create(['status' => 'en_revision']);

        // Demasiado corto (menos de 20)
        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => 'Muy corto'
        ]);
        $response->assertSessionHasErrors(['comment']);

        // Demasiado largo (mas de 70)
        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => str_repeat('a', 71)
        ]);
        $response->assertSessionHasErrors(['comment']);

        // Exacto 70 (debería pasar)
        $response = $this->actingAs($docente)->post(route('innovations.review.store', $innovation), [
            'vote' => 'approved',
            'comment' => str_repeat('a', 70)
        ]);
        $response->assertSessionHasNoErrors();
    }

    public function test_close_expired_reviews_command(): void
    {
        Notification::fake();
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $innovation = Innovation::factory()->create([
            'status' => 'en_revision',
            'review_deadline' => now()->subMinute(),
            'title' => 'Innovacion Expirada'
        ]);

        $this->artisan('innovations:close-expired-reviews')
             ->expectsOutputToContain('Procesada innovación ID #')
             ->assertExitCode(0);

        Notification::assertSentTo($admin, InnovationReviewCompleted::class);
    }
}
