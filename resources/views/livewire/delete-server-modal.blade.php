<div>
    @if($this->modalShown)
        <x-modals.delete
            :title="trans('pages.remove-server-modal.title')"
            :description="trans('pages.remove-server-modal.description')"
            action-method="deleteServer"
            close-method="close"
            :can-submit="$this->canSubmit"
        >
            <x-slot name="description">
                @lang('pages.remove-server-modal.description')

                <div class="mt-4 space-y-5">
                    <div>
                        <span class="input-label">@lang('pages.remove-server-modal.inputs.server_name')</span>
                        <div class="input-wrapper">
                            <input
                                type="text"
                                value="{{ $this->server->name }}"
                                class="font-semibold text-center input-text"
                                readonly
                            />
                        </div>
                    </div>

                    <x-ark-input
                        type="text"
                        name="serverNameConfirmation"
                        wire:model="serverNameConfirmation"
                        :errors="$errors"
                        :placeholder="trans('pages.remove-server-modal.inputs.server_name_confirm_placeholder')"
                        hide-label
                    />
                </div>
            </x-slot>
        </x-modals.delete>
    @endif
</div>
