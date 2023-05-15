<?php

declare(strict_types=1);

use App\Rules\Host;

it('should fail if the value is not a URL', function (): void {
    expect((new Host())->passes('host', 'gibberish'))->toBeFalse();
});

it('should fail if the value is an invalid URL', function (): void {
    expect((new Host())->passes('host', 'http://ip:port'))->toBeFalse();
});

it('should fail if the the value has a port but an invalid IP', function (): void {
    expect((new Host())->passes('host', 'http://ip:4005'))->toBeFalse();
});

it('should pass with a domain', function (): void {
    expect((new Host())->passes('host', 'http://nodem.io'))->toBeTrue();
});

it('should pass with an ip and port', function (): void {
    expect((new Host())->passes('host', 'http://127.0.0.1:4005'))->toBeTrue();
});

it('should have an error message', function (): void {
    expect((new Host())->message())->toBe('The host is invalid. Please https://domain.com or https://ip:port as the host.');
});
