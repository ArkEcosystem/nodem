<?php

declare(strict_types=1);

use App\Enums\ServerProviderTypeEnum;
use App\Models\Server as Model;
use App\Rules\Host;
use App\Rules\Server;
use Illuminate\Validation\Rule;

it('should return an array of rules for the provider', function (): void {
    expect((new Server())->provider())->toBeArray();
    expect((new Server())->provider())->toMatchArray([[], 'string', Rule::in(ServerProviderTypeEnum::toArray())]);
});

it('should return an array of rules for the name', function (): void {
    expect((new Server())->name())->toBeArray();
    expect((new Server())->name())->toMatchArray([[], 'string', 'min:3', 'max:30']);
});

it('should return an array of rules for the host rule', function (): void {
    expect((new Server())->host())->toBeArray();
    expect((new Server())->host())->toMatchArray([[], 'string', 'min:3', Rule::unique('servers', 'host'), new Host()]);
});

it('should return an array of rules for the host rule and ignore a specific server', function (): void {
    $server = Model::factory()->create();

    $rule = Rule::unique('servers', 'host')->ignore($server->id);

    expect((new Server())->host([], $server))->toBeArray();
    expect((new Server())->host([], $server))->toMatchArray([[], 'string', 'min:3', $rule, new Host()]);
});

it('should return an array of rules for the auth username', function (): void {
    expect((new Server())->authUsername())->toBeArray();
    expect((new Server())->authUsername())->toMatchArray([[], 'nullable', 'string', 'max:500']);
});

it('should return an array of rules for the auth password', function (): void {
    expect((new Server())->authPassword())->toBeArray();
    expect((new Server())->authPassword())->toMatchArray([[], 'nullable', 'string', 'max:500']);
});

it('should return an array of rules for auth access key', function (): void {
    expect((new Server())->authAccessKey())->toBeArray();
    expect((new Server())->authAccessKey())->toMatchArray([[], 'nullable', 'string', 'max:500']);
});

it('should return an array of rules for bip38 encryption', function (): void {
    expect((new Server())->bip38())->toBeArray();
    expect((new Server())->bip38())->toMatchArray([[], 'required', 'boolean']);
});

it('should be able to prepend some extra validation rule', function (): void {
    expect((new Server())->name())->toBeArray();
    expect((new Server())->name())->toMatchArray([[], 'string', 'min:3', 'max:30']);
    expect((new Server())->name(['required']))->toMatchArray([['required'], 'string', 'min:3', 'max:30']);

    $fooBar = true;
    expect((new Server())->name([Rule::requiredIf($fooBar)]))->toMatchArray([[new Illuminate\Validation\Rules\RequiredIf($fooBar)], 'string', 'min:3', 'max:30']);
});
