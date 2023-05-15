<div>
    @if($this->modalShown)
        <x-ark-modal width-class="max-w-lg" title-class="header-2" wire-close="close">
            @slot('title')
                @lang('pages.team.edit-modal.title')
            @endslot

            @slot('description')
                <div class="mt-3">@lang('pages.team.edit-modal.description')</div>

                <div class="flex items-center pb-5 mt-8">
                    <x-avatar
                        :identifier="$this->member->username"
                        size="w-10"
                        class="mr-3"
                        rounded="xl"
                    />

                    <div class="flex-1 font-semibold">
                        <div class="text-sm text-theme-secondary-500">@lang('general.username')</div>

                        <div class="text-theme-secondary-900">{{ $this->member->username }}</div>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-ark-rich-select
                        name="role"
                        wire:model="role"
                        :label="trans('forms.role')"
                        :errors="$errors"
                        :initial-value="$this->role"
                        :options="$this->getRoleOptions()"
                        :placeholder="trans('actions.select_an_option')"
                        class="space-y-2"
                        button-class="inline-block py-3 px-4 w-full text-left focus-visible:ring-0 form-input transition-default dark:bg-theme-secondary-900 dark:border-theme-secondary-800 focus-visible:border-theme-primary-600"
                    />
                </div>
            @endslot

            @slot('buttons')
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="close"
                >
                    @lang('actions.cancel')
                </button>

                <button
                    type="button"
                    class="button-primary"
                    wire:click="save"
                    wire:target="save"
                    wire:loading.attr="disabled"
                >
                    @lang('actions.save')
                </button>
            @endslot
        </x-ark-modal>
    @endif
</div>
