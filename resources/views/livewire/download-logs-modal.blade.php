<form
    x-data
    x-on:perform-logs-download.window="
        $nextTick(() => $root.dispatchEvent(new SubmitEvent('submit', {
            cancelable: true,
        })))
    "
    wire:submit.prevent="download"
    autocomplete="off"
>
    @if ($this->modalShown)
        <x-ark-modal width-class="max-w-lg" wire-close="closeModal" titleClass="header-2">
            <x-slot name="title">
                {{ trans('pages.download-logs-modal.title') }}
            </x-slot>

            <x-slot name="description">
                <p class="mt-4 text-theme-secondary-700">{{ trans('pages.download-logs-modal.subtitle') }}</p>

                @error ('server')
                    <x-ark-alert class="mt-8" type="warning">{{ $message }}</x-ark-alert>
                @enderror

                <x-server.logs.filter-form
                    class="mt-8"
                    target="download"
                    :wire="$this"
                    :selected-all="$selectedAll"
                    :levels="$levels"
                />
            </x-slot>

            <x-slot name="buttons">
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-y-0 sm:space-x-3">
                    <button
                        type="button"
                        class="mt-3 sm:mt-0 button-secondary"
                        wire:click="closeModal"
                        wire:loading.attr="disabled"
                        wire:target="download"
                    >
                        {{ trans('actions.cancel') }}
                    </button>

                    <x-loading-button icon="arrows.arrow-down-bracket" class="w-full sm:w-auto" type="submit" target="download">
                        {{ trans('actions.download') }}
                    </x-loading-button>
                </div>
            </x-slot>
        </x-ark-modal>
    @endif
</form>
