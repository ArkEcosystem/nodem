<x:ark-fortify::form-wrapper :action="$formUrl" x-data="{isTyping: false}">
    @csrf


    <div class="space-y-5">
        <div>
            <div class="flex flex-1">
                <x-ark-input
                    model="code"
                    name="code"
                    :label="trans('forms.invitation_code')"
                    autocomplete="one-time-code"
                    class="w-full"
                    :autofocus="true"
                    :errors="$errors"
                />
            </div>
        </div>

        <div>
            <div class="flex flex-1">
                <x-ark-input
                    model="username"
                    name="username"
                    :label="trans('forms.username')"
                    autocomplete="nickname"
                    class="w-full"
                    :autofocus="true"
                    :errors="$errors"
                />
            </div>
        </div>

        <x:ark-fortify::password-rules
            :password-rules="$passwordRules"
            is-typing="isTyping"
            rules-wrapper-class="grid grid-cols-1 gap-4 my-4"
            @typing="isTyping=true"
        >
            <x-ark-password-toggle
                model="password"
                name="password"
                :label="trans('ui::forms.password')"
                autocomplete="new-password"
                class="w-full"
                :errors="$errors"
            />
        </x:ark-fortify::password-rules>

        <div>
            <div class="flex flex-1">
                <x-ark-password-toggle
                    model="password_confirmation"
                    name="password_confirmation"
                    :label="trans('ui::forms.confirm_password')"
                    autocomplete="new-password"
                    class="w-full"
                    :errors="$errors"
                />
            </div>
        </div>

        <div>
            <x-ark-checkbox
                model="terms"
                name="terms"
                :errors="$errors"
            >
                @slot('label')
                    @lang('auth.register-form.conditions', ['termsOfServiceRoute' => route('terms-of-service')])
                @endslot
            </x-ark-checkbox>

            @error('terms')
                <p class="input-help--error">{{ $message }}</p>
            @enderror
        </div>

        <div class="text-right">
            <button
                type="submit"
                class="w-full sm:w-auto button-secondary"
                {{ ! $this->canSubmit() ? 'disabled' : '' }}
            >
                @lang('ui::actions.sign_up')
            </button>
        </div>
    </div>
</x:ark-fortify::form-wrapper>
