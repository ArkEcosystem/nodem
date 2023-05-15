<div>
    @if($this->modalShown)
        <x-ark-modal width-class="max-w-2xl" wire-close="closeModal" titleClass="header-2">
            @slot('title') @lang('pages.edit-server-modal.title') @endslot

            @slot('description')
                <x-server.forms.manage-server type="edit" />
            @endslot

            @slot('buttons')
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="closeModal"
                    wire:loading.attr="disabled"
                    wire:target="editServer"
                >
                    @lang('actions.cancel')
                </button>

                <button
                    form="edit-server"
                    type="submit"
                    class="button-primary"
                    wire:click="editServer"
                    wire:loading.remove
                    wire:target="editServer"
                    {{ ! $this->canSubmit() ? 'disabled' : '' }}
                >
                    @lang('actions.update')
                </button>

                <button
                    class="primary-button-loading-state button-secondary"
                    wire:loading
                    wire:target="editServer"
                    disabled
                >
                    <x-ark-loader-icon class="w-7 h-7" circle-class="text-white" path-class="bg-theme-primary-600" />
                </button>
            @endslot
        </x-ark-modal>
    @endif
</div>
