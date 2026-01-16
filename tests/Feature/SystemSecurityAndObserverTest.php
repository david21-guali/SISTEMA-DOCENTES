<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Innovation;
use App\Notifications\InnovationReviewRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class SystemSecurityAndObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test Attachment Preview Security
     */
    public function test_preview_blocks_unauthorized_paths(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Intentar acceder a un path que no está en el whitelist
        $response = $this->actingAs($user)->get(route('storage.preview', ['path' => 'secrets/config.php']));

        $response->assertForbidden();
        $response->assertSee('Acceso no permitido');
    }

    public function test_preview_allows_whitelisted_paths(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('admin');

        $path = 'attachments/test.pdf';
        Storage::disk('public')->put($path, 'dummy pdf content');

        $response = $this->actingAs($user)->get(route('storage.preview', ['path' => $path]));

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
    }

    /**
     * Test Observer Logic - Self Notification Prevention
     */
    public function test_innovation_observer_prevents_self_notification(): void
    {
        Notification::fake();
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('admin');

        $innovation = Innovation::factory()->create([
            'status' => 'en_proceso'
        ]);

        // El admin logueado solicita revisión
        $this->actingAs($admin)->post(route('innovations.request-review', $innovation));

        // Verificar que el admin que ejecutó la acción NO recibe notificación
        Notification::assertNotSentTo($admin, InnovationReviewRequested::class);
        
        // Verificar que el otro admin SÍ la recibe
        Notification::assertSentTo($otherAdmin, InnovationReviewRequested::class);
    }

    /**
     * Test UI Refinements
     */
    public function test_admin_show_view_has_simplified_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $innovation = Innovation::factory()->create([
            'status' => 'en_revision'
        ]);

        $response = $this->actingAs($admin)->get(route('innovations.show', $innovation));

        $response->assertStatus(200);
        
        // El botón cambió de nombre
        $response->assertSee('Enviar a Buenas Prácticas');
        
        // El campo review_notes fue eliminado de la vista (o al menos no debería estar como textarea con ese name)
        $response->assertDontSee('name="review_notes"', false);
        
        // El puntaje ya no usa display-4 (ahora usa h2)
        $response->assertSee('h2 fw-bold text-info', false);
        $response->assertDontSee('display-4 fw-bold text-info', false);
    }
}
