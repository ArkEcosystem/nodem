<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;

it('should get the message from the lang key', function (): void {
    $alert = new Alert(AlertType::UPDATING_MANAGER_STATE, 'warning', 'Server name');

    expect($alert->message())->toBe('An error occurred while updating the manager state. See server logs for details.');
});

it('should translate the message if not a key', function (): void {
    $message  = 'ERR_NO_KEY';
    $expected = 'The given server has no delegate configured. Configure it first.';

    $alert = new Alert($message, 'warning', 'Server name');

    expect($alert->message())->toBe($expected);
});

it('should return the original message if not key or translation', function (): void {
    $message  = 'Whatever';
    $expected = 'Whatever';

    $alert = new Alert($message, 'warning', 'Server name');

    expect($alert->message())->toBe($expected);
});
