<?php

use App\Console\Kernel;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use App\Providers\BroadcastServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\getJson;

it('can redirect unauthenticated user', function () {
    $response = getJson('/api/user');

    $response->assertStatus(401);
});

it('can have rate limiter', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = getJson('/api/user');

    $response->assertStatus(200)
        ->assertHeader('X-Ratelimit-Limit', 60)
        ->assertHeader('X-Ratelimit-Remaining', 59);
});

it('can have trusted hosts', function () {
    $trustedHosts = app('App\Http\Middleware\TrustHosts')->hosts();
    $this->assertNotEmpty($trustedHosts);
});

it('can AuthenticatedUserIsRedirected', function () {
    Route::get('/home', fn () => 'Home')->name('home');

    // Mock the Auth facade to simulate an authenticated user
    Auth::shouldReceive('guard')->once()->andReturnSelf();
    Auth::shouldReceive('check')->once()->andReturn(true);

    $request = Request::create('/test', 'GET');
    $middleware = new RedirectIfAuthenticated;
    /** @var RedirectResponse */
    $response = $middleware->handle($request, function () {
        // do nothing
    });

    $this->assertEquals($response->getTargetUrl(), url(RouteServiceProvider::HOME));
});

it('can UnauthenticatedUserIsNotRedirected', function () {
    Route::get('/home', fn () => 'Home')->name('home');

    // Mock the Auth facade to simulate an unauthenticated user
    Auth::shouldReceive('guard')->once()->andReturnSelf();
    Auth::shouldReceive('check')->once()->andReturn(false);

    $request = Request::create('/test', 'GET');
    $middleware = new RedirectIfAuthenticated;

    $response = $middleware->handle($request, fn () => new Response());

    $this->assertInstanceOf(Response::class, $response);
});

it('can BroadcastRoutesAreRegistered', function () {
    Broadcast::shouldReceive('routes')->once();
    Broadcast::shouldReceive('channel')->andReturnSelf();

    $provider = new BroadcastServiceProvider($this->app);
    $provider->boot();
});

it('can schedule method', function () {
    $app = new Application;
    $events = new \Illuminate\Events\Dispatcher($app);

    $kernel = new Kernel($app, $events);

    /**
     * @var \PHPUnit\Framework\TestCase $this
     * @var MockObject $schedule
     * @var InvokedCount $once
     */
    $schedule = $this->createMock(Schedule::class);
    $once = $this->once();
    $schedule->expects($once)->method('command')->with('inspire')->willReturnSelf();

    // Use reflection to call protected method
    $reflection = new \ReflectionClass($kernel);
    $method = $reflection->getMethod('schedule');
    $method->setAccessible(true);

    $method->invokeArgs($kernel, [$schedule]);
});
