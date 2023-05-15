<div
    x-on:export-ready="$wire.call('closeModal')"
    x-data
>
    <button
        class="hidden items-center space-x-2 h-11 md:flex button-secondary"
        type="button"
        wire:click="openModal"
        @if ($this->user->servers()->count() === 0)
            disabled
        @endif
    >
        <x-ark-icon
            name="arrows.arrow-up-turn-bracket"
            size="sm"
        />

        <span class="hidden md:block">
            {{ trans('pages.export-modal.button') }}
        </span>
    </button>

    @if ($modalShown)
        <x-ark-modal
            wire-close="closeModal"
            :title="trans('pages.export-modal.title')"
            width-class="max-w-md"
        >
            <x-slot name="description">
                <div class="mt-6">
                    <div class="mx-auto w-72 max-w-full">
                        <img src="{{ asset('images/modal/export.svg') }}" alt="" />
                    </div>

                    <p class="mt-8 leading-7">
                        {{ trans('pages.export-modal.description') }}
                    </p>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3">
                    <button
                        type="button"
                        class="mt-3 w-full sm:mt-0 sm:w-auto button-secondary"
                        wire:click="closeModal"
                        wire:loading.attr="disabled"
                        wire:target="export"
                    >
                        {{ trans('pages.export-modal.cancel') }}
                    </button>

                    <x-loading-button icon="arrows.arrow-down-bracket" class="w-full sm:w-auto" wire="export">
                        {{ trans('pages.export-modal.submit') }}
                    </x-loading-button>
                </div>
            </x-slot>
        </x-ark-modal>
    @endif
</div>
