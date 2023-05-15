<x-ark-modal
    width-class="max-w-lg"
    title-class="mr-12 header-2"
    wire-close="close">
    @slot('title')
        @lang('pages.team.invite_title')
    @endslot

    @slot('description')
        <div class="mt-3">@lang('pages.team.invite_description')</div>

        <div class="mt-6 space-y-4">
            <x-ark-input
                type="text"
                name="username"
                wire:model="username"
                :label="trans('forms.username')"
                :errors="$errors"
            />

            <x-ark-rich-select
                name="role"
                wire:model="role"
                :label="trans('forms.role')"
                :initial-value="$this->role"
                :options="$this->getRoleOptions()"
                wrapper-class="mt-2 w-full"
                :errors="$errors"
                button-class="inline-block py-3 px-4 w-full text-left focus-visible:ring-0 form-input transition-default dark:bg-theme-secondary-900 dark:border-theme-secondary-800 focus-visible:border-theme-primary-600"
            />
        </div>
    @endslot

    @slot('buttons')
        <button
            type="submit"
            class="button-primary"
            wire:click="invite"
            wire:target="invite"
            wire:loading.attr="disabled"
        >
            @lang('actions.generate')
        </button>
    @endslot
</x-ark-modal>
