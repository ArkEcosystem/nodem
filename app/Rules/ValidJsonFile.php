<?php

declare(strict_types=1);

namespace App\Rules;

use ErrorException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Livewire\TemporaryUploadedFile;

final class ValidJsonFile implements Rule
{
    /**
     * Indicates if the JSON is invalid.
     *
     * @var bool
     */
    protected $withInvalidJson = false;

    /**
     * Indicates if the content is invalid.
     *
     * Exports contain an array of servers, meaning that a JSON file without array structure is invalid
     * and should be ignored.
     *
     * @var bool
     */
    protected $withInvalidContent = false;

    /**
     * Indicates if the JSON is missing a required property.
     *
     * @var bool
     */
    protected $withMissingProperty = false;

    /**
     * Indicates if the JSON contains an unexpected property.
     *
     * @var bool
     */
    protected $withUnexpectedProperty = false;

    public function passes($attribute, $value)
    {
        if ($this->isInvalidJson($value)) {
            $this->withInvalidJson = true;

            return false;
        }

        if ($this->isInvalidContent($value)) {
            $this->withInvalidContent = true;

            return false;
        }

        if ($this->hasMissingProperty($value)) {
            $this->withMissingProperty = true;

            return false;
        }

        if ($this->hasUnexpectedProperty($value)) {
            $this->withUnexpectedProperty = true;

            return false;
        }

        return true;
    }

    public function message(): string
    {
        if ($this->withInvalidJson) {
            return trans('validation.messages.with_invalid_json');
        }

        if ($this->withInvalidContent) {
            return trans('validation.messages.with_invalid_content');
        }

        if ($this->withMissingProperty) {
            return trans('validation.messages.with_missing_property');
        }

        if ($this->withUnexpectedProperty) {
            return trans('validation.messages.with_unexpected_property');
        }

        return trans('validation.messages.success');
    }

    private function isInvalidJson(UploadedFile | TemporaryUploadedFile $value): bool
    {
        return is_null(json_decode((string) $value->get()));
    }

    private function isInvalidContent(UploadedFile | TemporaryUploadedFile $value): bool
    {
        try {
            return ! is_array(json_decode((string) $value->get(), true)[0]);
        } catch (ErrorException $e) {
            return true;
        }
    }

    private function hasMissingProperty(UploadedFile | TemporaryUploadedFile $value): bool
    {
        $content = json_decode((string) $value->get(), true);

        foreach ($content as $propertiesArray) {
            if (count($propertiesArray) !== count($this->getRequiredProperties())) {
                return true;
            }
        }

        return false;
    }

    private function hasUnexpectedProperty(UploadedFile | TemporaryUploadedFile $value): bool
    {
        $content = json_decode((string) $value->get(), true);

        foreach ($content as $propertiesArray) {
            if (array_keys($propertiesArray) !== $this->getRequiredProperties()) {
                return true;
            }
        }

        return false;
    }

    private function getRequiredProperties(): array
    {
        return ['provider', 'name', 'host', 'process_type', 'uses_bip38_encryption', 'auth'];
    }
}
