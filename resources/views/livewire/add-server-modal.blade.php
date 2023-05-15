<div>
    @if($this->modalShown)
        <x-ark-modal width-class="max-w-2xl" wire-close="closeModal" titleClass="header-2">
            @slot('title') @lang('pages.add-server-modal.title') @endslot

            @slot('description')
                <x-server.forms.manage-server type="add" />
            @endslot

            @slot('buttons')
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="closeModal"
                    wire:loading.attr="disabled"
                    wire:target="addServer"
                >
                    {{ trans('actions.cancel') }}
                </button>

                <x-loading-button :disabled="! $this->canSubmit()" icon="plus" wire="addServer">
                    {{ trans('actions.add') }}
                </x-loading-button>
            @endslot
        </x-ark-modal>
    @endif
</div>
