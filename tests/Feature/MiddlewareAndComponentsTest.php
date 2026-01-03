<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\View\Components\AppLayout;
use App\View\Components\GuestLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Http\Middleware\CheckRole;

class MiddlewareAndComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_role_middleware()
    {
        $user = User::factory()->create();
        $middleware = new CheckRole();
        
        // Test redirect to login if not authenticated
        $request = Request::create('/test', 'GET');
        $response = $middleware->handle($request, function() {}, 'admin');
        $this->assertEquals(302, $response->getStatusCode());

        // Test abort 403 if role not matched
        $this->actingAs($user);
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);
        
        try {
            $middleware->handle($request, function() {}, 'admin');
            $this->fail('Middleware should have aborted with 403');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }

        // Test pass if role matched
        $user->assignRole('admin');
        $response = $middleware->handle($request, function() {
            return response('passed');
        }, 'admin');
        $this->assertEquals('passed', $response->getContent());
    }

    public function test_view_components()
    {
        $appLayout = new AppLayout();
        $this->assertEquals('layouts.app', $appLayout->render()->name());

        $guestLayout = new GuestLayout();
        $this->assertEquals('layouts.guest', $guestLayout->render()->name());
    }
}
