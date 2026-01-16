<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Gate;

it('loads the dashboard page with cached data', function () {

    Gate::before(fn () => true); 

    $user = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('pos.dashboard'));

    $response->assertStatus(200);
});

