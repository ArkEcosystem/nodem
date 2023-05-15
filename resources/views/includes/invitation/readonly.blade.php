<x-ark-modal
    class="max-w-lg"
    title-class="header-2"
    wire-close="close">
    @slot('title')
        @lang('pages.team.pending_title')
    @endslot

    @slot('description')
        <div class="mt-3">@lang('pages.team.pending_description')</div>

        <div class="mt-6 space-y-4">
            <x-ark-input-with-icon
                type="text"
                name="username"
                wire:model="username"
                :label="trans('forms.username')"
                slot-class="absolute inset-y-0 right-0"
                readonly
            >
                <x-ark-clipboard
                    class="flex justify-center items-center w-12 h-full focus-visible:rounded text-theme-primary-400 hover:text-theme-primary-700"
                    value="{{ $username }}"
                    no-styling/>
            </x-ark-input-with-icon>

            <x-ark-input-with-icon
                type="text"
                name="code"
                wire:model="code"
                :label="trans('forms.code')"
                slot-class="absolute inset-y-0 right-0"
                readonly
            >
                <x-ark-clipboard
                    class="flex justify-center items-center w-12 h-full focus-visible:rounded text-theme-primary-400 hover:text-theme-primary-700"
                    value="{{ $code }}"
                    no-styling/>
            </x-ark-input-with-icon>
        </div>
    @endslot

    @slot('buttons')
        <button
            type="submit"
            class="button-primary"
            wire:click="close"
        >
            @lang('actions.done')
        </button>
    @endslot
</x-ark-modal>
