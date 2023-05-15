<form
    x-data
    wire:submit.prevent="submit"
    autocomplete="off"
>
    @if ($this->modalShown)
        <x-ark-modal
            width-class="max-w-lg"
            wire-close="closeModal"
            title-class="header-2"
            buttons-style="w-full"
        >
            <x-slot name="title">
                {{ trans('pages.filter-logs-modal.title') }}
            </x-slot>

            <x-slot name="description">
                <p class="mt-4 text-theme-secondary-700">{{ trans('pages.filter-logs-modal.subtitle') }}</p>

                @error ('server')
                    <x-ark-alert class="mt-8" type="warning">{{ $message }}</x-ark-alert>
                @enderror

                <x-server.logs.filter-form
                    :wire="$this"
                    target="submit"
                    :selected-all="false"
                    :levels="$levels"
                />
            </x-slot>

            <x-slot name="buttons">
                <div class="flex flex-col sm:flex-row sm:justify-between">
                    <button
                        type="button"
                        class="h-11 text-base font-semibold link"
                        wire:click="resetFilters"
                        wire:loading.attr="disabled"
                    >
                        <div class="flex justify-center items-center space-x-2">
                            <x-ark-icon name="arrows.arrow-rotate-left" size="sm" />

                            <span>@lang('actions.reset')</span>
                        </div>
                    </button>

                    <div class="flex flex-col-reverse sm:flex-row sm:space-x-3">
                        <button
                            type="button"
                            class="mt-3 sm:mt-0 button-secondary"
                            wire:click="closeModal"
                            wire:loading.attr="disabled"
                            wire:target="download"
                        >
                            {{ trans('actions.cancel') }}
                        </button>

                        <button
                            type="submit"
                            @if($this->busy)
                                disabled
                            @endif
                            class="flex justify-center items-center w-full h-11 text-base sm:w-auto button-primary"
                        >
                            @if($this->busy)
                                <x-ark-loader-icon class="mr-2 w-4 h-4" />
                            @endif

                            @lang('actions.apply')
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-ark-modal>
    @endif
</form>
