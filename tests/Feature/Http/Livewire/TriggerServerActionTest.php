<?php

declare(strict_types=1);

use App\Cache\AlertStore;
use App\Http\Livewire\TriggerServerAction;
use App\Jobs\DeleteProcess;
use App\Jobs\RestartProcess;
use App\Jobs\StartProcess;
use App\Jobs\StopProcess;
use App\Jobs\UpdateCore;
use App\Jobs\UpdateCoreManager;
use App\Jobs\UpdateProcesses;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use function Tests\mockWithFixture;

it('should trigger the [start] action for the given server', function (string $processType): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => $processType]);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', $processType, $server->id]);

    Queue::assertPushed(StartProcess::class);
})->with(['core', 'relay', 'forger']);

it('should trigger the [start] action for all processes for the given server', function (): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'forger']);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'all', $server->id]);

    Queue::assertPushed(StartProcess::class, 2);
});

it('accepts extra request parameters when starting a process', function (): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create([
        'user_id'               => $user->id,
        'uses_bip38_encryption' => true,
    ]);

    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'relay', $server->id, ['password' => 'my-password']]);

    Queue::assertPushed(StartProcess::class, function ($job) {
        return $job->options['password'] === 'my-password';
    });
});

it('should trigger the [stop] action for the given server', function (string $processType): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => $processType]);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['stop', $processType, $server->id]);

    Queue::assertPushed(StopProcess::class);
})->with(['core', 'relay', 'forger']);

it('should trigger the [restart] action for the given server', function (string $processType): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => $processType]);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['restart', $processType, $server->id]);

    Queue::assertPushed(RestartProcess::class);
})->with(['core', 'relay', 'forger']);

it('emits serverActionTriggered event', function (): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'relay', $server->id]);

    $component->assertEmitted('serverActionTriggered'.$server->id);
});

it('should trigger the [delete] action for the given server', function (string $processType): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => $processType]);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['delete', $processType, $server->id]);

    Queue::assertPushed(DeleteProcess::class);
})->with(['core', 'relay', 'forger']);

it('should trigger the [update] action for the given server', function (string $serverType, string $jobName): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'core']);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['update', $serverType, $server->id]);

    Queue::assertPushed($jobName);
})->with([
    ['core', UpdateCore::class],
    ['manager', UpdateCoreManager::class],
]);

it('should be able to create a missing process on trigger of the [start] action for the given server ', function (): void {
    mockWithFixture('process/start');
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id, 'host' => 'https://mynode.com']);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'forger', $server->id]);

    Queue::assertPushed(UpdateProcesses::class);

    Http::assertSent(function ($request) use ($server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'process.start' &&
            $request['params']->name === 'forger';
    });
});

it('should catch passphrase response exception and store the proper alert', function (): void {
    mockWithFixture('process/error_passphrase');
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id, 'host' => 'https://mynode.com']);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    expect(count(AlertStore::getAll($user)))->toBe(0);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'forger', $server->id]);

    $alerts = AlertStore::getAll($user);

    expect(count($alerts))->toBe(1);

    expect($alerts[0]->message())->toBe('The given server has no delegate configured. Configure it first.');
});

it('should catch a random response exception and store the exception message', function (): void {
    mockWithFixture('process/error');
    Queue::fake();

    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id, 'host' => 'https://mynode.com']);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    expect(count(AlertStore::getAll($user)))->toBe(0);

    $component = Livewire::actingAs($user)->test(TriggerServerAction::class);
    $component->emit('triggerServerAction', ['start', 'forger', $server->id]);

    $alerts = AlertStore::getAll($user);

    expect(count($alerts))->toBe(1);

    expect($alerts[0]->message())->toBe('something went wrong');
});
