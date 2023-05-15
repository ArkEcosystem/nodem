<?php

declare(strict_types=1);

use App\ViewModels\ProcessLogViewModel;
use Carbon\Carbon;

beforeEach(function () {
    $this->log = [
        'id'    => 123456789,
        'level' => collect([
            'error',
            'warning',
            'debug',
            'info',
            'trace',
            'fatal',
        ])->random(),
        'timestamp' => Carbon::parse('2000-10-09 08:07:06')->timestamp,
        'content'   => 'Transaction 02c9168a33b73d97d9cc7cd1f4969d3fc6b831eef3a87f79e6b451b809c6919b not eligible for broadcast - fee of 0.005 DѦ is smaller than minimum fee (0.005865 DѦ)',
    ];

    $this->subject = new ProcessLogViewModel($this->log);
});

it('should get the id', function (): void {
    expect($this->subject->id())->toBe(123456789);
});

it('should get the level', function (): void {
    expect(
        in_array(
            $this->subject->level(),
            [
                'error',
                'warning',
                'debug',
                'info',
                'trace',
                'fatal',
            ],
            true
        )
    )->toBeTrue();
});

it('should get the date', function (): void {
    expect($this->subject->date())->toBe('09-10-2000');
});

it('should get the time', function (): void {
    expect($this->subject->time())->toBe('08:07:06');
});

it('should get the datetimeObject', function (): void {
    expect($this->subject->dateTimeObject())->toBeInstanceOf(Carbon::class);
});

it('should get the message', function (): void {
    expect($this->subject->message())->toBe('Transaction 02c9168a33b73d97d9cc7cd1f4969d3fc6b831eef3a87f79e6b451b809c6919b not eligible for broadcast - fee of 0.005 DѦ is smaller than minimum fee (0.005865 DѦ)');
});
