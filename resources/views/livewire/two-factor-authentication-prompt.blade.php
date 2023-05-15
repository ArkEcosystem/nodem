<div>
    @if ($modalShown)
        <x-ark-modal :title="trans('pages.user-settings.security.two_factor_prompt.title')" width-class="max-w-xl">
            <x-slot name="description">
                <div class="mt-8">
                    <img src="{{ asset('images/modal/password-guarded.svg') }}" alt="" />

                    <p class="mt-8 leading-7">{{ trans('pages.user-settings.security.two_factor_prompt.description') }}</p>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="flex justify-end">
                    <button
                        type="button"
                        class="button-primary"
                        wire:click="dismiss"
                        wire:loading.attr="disabled"
                    >
                        {{ trans('pages.user-settings.security.two_factor_prompt.submit') }}
                    </button>
                </div>
            </x-slot>
        </x-ark-modal>
    @endif
</div>
