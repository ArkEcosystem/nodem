<?php

declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('should render the page without any errors', function () {
    $user   = User::factory()->create();
    $server = Server::factory()->create(['user_id' => $user->id]);

    $fakeSearchResponse = json_decode(file_get_contents(base_path('tests/fixtures/log/search.json')), true);

    Http::fake(Http::response($fakeSearchResponse, 200));

    $this
        ->actingAs($user)
        ->get(route('server', $server))
        ->assertOk();
});
