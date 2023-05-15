<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\LogLevel;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Cache;

/**
 * @property string $process
 * @property array $state
 */
trait InteractsWithLogFilters
{
    use InteractsWithUser;

    public function isLevelSelected(string $level) : bool
    {
        return in_array($level, $this->levels(), true);
    }

    private function cachedFilters(?string $process = null) : array
    {
        return Cache::tags($this->filtersTagKey())->get($this->filtersCacheKey($process), []);
    }

    private function cacheFilters(array $state, ?string $process = null) : void
    {
        Cache::tags($this->filtersTagKey())->forget($this->filtersCacheKey($process));

        Cache::tags($this->filtersTagKey())->rememberForever($this->filtersCacheKey($process), fn () : array => [
            'from'   => $state['from'],
            'to'     => $state['to'],
            'levels' => $state['levels'],
        ]);
    }

    private function filtersTagKey() : array
    {
        return [
            'server-filters:'.$this->server->id,
            'user-filters:'.$this->user->id,
        ];
    }

    private function filtersCacheKey(?string $process = null) : string
    {
        // This method is never used in the DownloadLogsModal component...

        return sprintf('filters:%s:%s:%s', $this->server->id, $this->user->id, $process ?? $this->process);
    }

    private function startDate() : Carbon|null
    {
        if (! $this->state['dateFrom']) {
            return null;
        }

        return Carbon::parse(implode(' ', [
            $this->state['dateFrom'],
            $this->state['timeFrom'],
        ]));
    }

    private function endDate() : Carbon|null
    {
        if (! $this->state['dateTo']) {
            return null;
        }

        return Carbon::parse(implode(' ', [
            $this->state['dateTo'],
            $this->state['timeTo'],
        ]));
    }

    /**
     * Because HTML strips out seconds if no seconds are entered, we want to manually append them if ommited from request.
     *
     * @param string $value
     *
     * @return string|null
     */
    private function formatTimeInputPayload(string $value) : string|null
    {
        if ($value === '') {
            return null;
        }

        // Test the HH:mm format...
        if (preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $value) !== 0) {
            $value = $value.':00';
        }

        return $value;
    }

    private function messages() : array
    {
        return [
            'state.*.required'           => trans('pages.download-logs-modal.messages.required'),
            'state.dateFrom.date_format' => trans('pages.download-logs-modal.messages.date-format'),
            'state.dateTo.date_format'   => trans('pages.download-logs-modal.messages.date-format'),
            'state.timeFrom.date_format' => trans('pages.download-logs-modal.messages.time-format'),
            'state.timeTo.date_format'   => trans('pages.download-logs-modal.messages.time-format'),
        ];
    }

    private function validateDates(Validator $validator) : void
    {
        if (optional($this->startDate())->isFuture()) {
            $validator->errors()->add('state.dateFrom', trans('pages.download-logs-modal.messages.start-date-future'));
        }

        if ($this->endDate() !== null && optional($this->startDate())->gte($this->endDate())) {
            $validator->errors()->add('state.dateTo', trans('pages.download-logs-modal.messages.end-date-future'));
        }
    }

    private function hasSelectedAllLevels() : bool
    {
        return collect(LogLevel::all())->diff($this->levels())->isEmpty();
    }

    private function levels() : array
    {
        return collect($this->state['levels'])
                ->filter() // remove `false` values
                ->keys() // get log level keys (debug, error, ...)
                ->intersect(LogLevel::all()) // remove anything that's not a valid log level
                ->toArray();
    }
}
