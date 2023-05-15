<div>
    @if($this->modalShown)
        <x-ark-modal
            title-class="header-2"
            width-class="max-w-2xl"
            wire-close="closeModal"
        >
            <x-slot name="title">
                @lang('pages.bip38-password-modal.title')
            </x-slot>

            <x-slot name="description">
                <div class="flex flex-col">
                    <div class="flex justify-center mt-8 w-full">
                        <img
                            src="{{ asset('images/auth/confirm-password.svg') }}"
                            class="h-28"
                            alt=""
                        />
                    </div>

                    <div class="mt-8">
                        @lang('pages.bip38-password-modal.description')
                    </div>
                </div>

                <form
                    class="mt-8"
                    x-on:submit.prevent
                    id="bip39-password-form"
                >
                    <div class="space-y-2">
                        <x-ark-password-toggle
                            :label="trans('forms.password')"
                            name="bip38Password"
                            autocomplete="current-password"
                            :errors="$errors"
                            masked
                        />
                    </div>
                </form>
            </x-slot>

            <x-slot name="buttons">
                <div class="flex flex-col-reverse justify-end space-y-4 space-y-reverse w-full sm:flex-row sm:space-y-0 sm:space-x-3">
                    <button
                        type="button"
                        dusk="confirm-password-form-cancel"
                        class="button-secondary"
                        wire:click="closeModal"
                    >
                        @lang('actions.cancel')
                    </button>

                    <button
                        type="submit"
                        dusk="confirm-password-form-submit"
                        class="inline-flex justify-center items-center button-primary"
                        wire:click="submit"
                        form="bip39-password-form"
                    >
                        @lang('actions.confirm')
                    </button>
                </div>
            </x-slot>
        </x-ark-modal>
    @endif
</div>
