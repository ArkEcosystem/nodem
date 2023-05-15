<?php

declare(strict_types=1);

use App\Rules\ValidJsonFile;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->subject = new ValidJsonFile();
});

it('will reject if the json is invalid', function () {
    $file = UploadedFile::fake()->createWithContent('test.json', '["provider":"aws","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    expect($this->subject->passes('json', $file))->toBeFalse();

    expect($this->subject->message())->toBe(trans('validation.messages.with_invalid_json'));
});

it('will reject if the json is valid but the content is not', function () {
    $file = UploadedFile::fake()->createWithContent('test.json', '{"name":"foo"}');

    expect($this->subject->passes('json', $file))->toBeFalse();

    expect($this->subject->message())->toBe(trans('validation.messages.with_invalid_content'));
});

it('will reject if the json is missing a property', function () {
    $file = UploadedFile::fake()->createWithContent('test.json', '[{"name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    expect($this->subject->passes('json', $file))->toBeFalse();

    expect($this->subject->message())->toBe(trans('validation.messages.with_missing_property'));
});

it('will reject if the json is not matching the expected properties', function () {
    $file = UploadedFile::fake()->createWithContent('test.json', '[{"foo":"aws","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    expect($this->subject->passes('json', $file))->toBeFalse();

    expect($this->subject->message())->toBe(trans('validation.messages.with_unexpected_property'));
});

it('will not reject if proper json and expected properties', function () {
    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider":"aws","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    expect($this->subject->passes('json', $file))->toBeTrue();

    expect($this->subject->message())->toBe(trans('validation.messages.success'));
});
