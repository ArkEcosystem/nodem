@props([
    'type' => 'edit',
])

<form id="{{ $type }}-server" class="flex flex-col mt-8 space-y-5" @submit.prevent>
    @if($this->serverCheckingError)
        <x-ark-alert type="error">
            @if($this->serverCheckingErrorMessage)
                {{ $this->serverCheckingErrorMessage }}
            @else
                @lang('pages.'.$type.'-server-modal.server_error')
            @endif
        </x-ark-alert>
    @endif

    <x-ark-rich-select
        button-class="block py-3 pl-4 w-full font-medium text-left bg-transparent rounded border focus-visible:ring-0 border-theme-secondary-300 text-theme-secondary-900 focus-visible:border-theme-primary-600"
        wrapper-class="input-wrapper"
        dropdown-class="z-10"
        wire:model="state.provider"
        name="state.provider"
        :label="trans('pages.'.$type.'-server-modal.inputs.provider')"
        :initial-value="$this->state['provider']"
        :options="array_flip($this->providers)"
    >
        <x-slot name="dropdownEntry">
            @foreach ($this->providers as $providerItem)
                <div x-show="'{{ $providerItem }}' === value" class="flex items-center space-x-3 font-medium text-theme-secondary-900">
                    <x-ark-icon :name="ServerProviderTypeEnum::iconName($providerItem)" class="text-theme-secondary-900" />
                    <span>@lang('general.providers.'.$providerItem)</span>
                </div>
            @endforeach

            <span x-show="! value" class="block font-normal truncate text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('pages.'.$type.'-server-modal.inputs.provider_placeholder')
            </span>
        </x-slot>

        <x-slot name="dropdownList">
            @foreach ($this->providers as $providerItem)
                <div
                    data-option
                    x-description="Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation."
                    x-state:on="Highlighted"
                    x-state:off="Not Highlighted"
                    wire:ignore
                    role="option"
                    @click="choose('{{$providerItem}}')"
                    @mouseenter="selected = {{$loop->index}}"
                    @mouseleave="selected = null"
                    :class="{
                        'text-theme-primary-600 bg-theme-primary-100 dark:text-white dark:bg-theme-primary-600': value === '{{$providerItem}}',
                        'text-theme-primary-600 bg-theme-secondary-100 dark:bg-theme-primary-600 dark:text-white': selected === {{$loop->index}} && value !== '{{$providerItem}}',
                    }"
                    class="flex items-center py-4 px-8 space-x-3 font-medium transition duration-150 ease-in-out cursor-pointer listbox-option dark:text-theme-secondary-200 dark:hover:bg-theme-primary-600 dark:hover:text-theme-secondary-200 hover:bg-theme-secondary-100 hover:text-theme-secondary-900"
                >
                    <x-ark-icon :name="ServerProviderTypeEnum::iconName($providerItem)" class="text-theme-secondary-900" />
                    <span>@lang('general.providers.'.$providerItem)</span>
                </div>
            @endforeach
        </x-slot>
    </x-ark-rich-select>

    <x-ark-input
        type="text"
        name="state.name"
        :label="trans('pages.'.$type.'-server-modal.inputs.server_name')"
        :placeholder="trans('pages.'.$type.'-server-modal.inputs.server_name_placeholder')"
        :errors="$errors"
    />

    <x-ark-input
        type="text"
        name="state.host"
        :label="trans('pages.'.$type.'-server-modal.inputs.server_address')"
        :placeholder="trans('pages.'.$type.'-server-modal.inputs.server_address_placeholder')"
        :errors="$errors"
    />

    <x-ark-rich-select
        button-class="block py-3 pl-4 w-full font-medium text-left bg-transparent rounded border focus-visible:ring-0 border-theme-secondary-300 text-theme-secondary-900 focus-visible:border-theme-primary-600"
        wrapper-class="input-wrapper"
        dropdown-class="z-10"
        wire:model="state.process_type"
        name="state.process_type"
        :options="array_flip($this->serverProcessTypes)"
        :label="trans('pages.'.$type.'-server-modal.inputs.process_type')"
        :initial-value="$this->state['process_type']"
    >
        <x-slot name="dropdownEntry">
            @foreach ($this->serverProcessTypes as $processType)
                <div x-show="'{{ $processType }}' === value" class="flex items-center font-medium text-theme-secondary-900">
                    @lang('general.process_type.'.$processType)
                    <span class="ml-1 text-theme-secondary-400">(@lang('general.process_type.'.$processType.'_detail'))</span>
                </div>
            @endforeach

            <span x-show="! value" class="block font-normal truncate text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('pages.'.$type.'-server-modal.inputs.process_type_placeholder')
            </span>
        </x-slot>

        <x-slot name="dropdownList">
            @foreach ($this->serverProcessTypes as $processType)
                <div
                    data-option
                    x-description="Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation."
                    x-state:on="Highlighted"
                    x-state:off="Not Highlighted"
                    wire:ignore
                    role="option"
                    @click="choose('{{$processType}}')"
                    @mouseenter="selected = {{$loop->index}}"
                    @mouseleave="selected = null"
                    :class="{
                        'text-theme-primary-600 bg-theme-primary-100 dark:text-white dark:bg-theme-primary-600': value === '{{$processType}}',
                        'text-theme-primary-600 bg-theme-secondary-100 dark:bg-theme-primary-600 dark:text-white': selected === {{$loop->index}} && value !== '{{$processType}}',
                    }"
                    class="flex items-center py-4 px-8 space-x-3 font-medium transition duration-150 ease-in-out cursor-pointer listbox-option dark:text-theme-secondary-200 dark:hover:bg-theme-primary-600 dark:hover:text-theme-secondary-200 hover:bg-theme-secondary-100 hover:text-theme-secondary-900"
                >
                    @lang('general.process_type.'.$processType)
                    <span class="ml-1 text-theme-secondary-500 dark:text-theme-secondary-200">(@lang('general.process_type.'.$processType.'_detail'))</span>
                </div>
            @endforeach
        </x-slot>
    </x-ark-rich-select>

    <div class="flex items-center">
        <x-ark-switch
            default="'{{ $this->useCredentials }}'"
            :left-label="trans('pages.'.$type.'-server-modal.inputs.account')"
            :right-label="trans('pages.'.$type.'-server-modal.inputs.access_key')"
            name="useCredentials"
            alpine-click="window.livewire.emit('credentialsModeChanged')"
        />
    </div>

    <div>
        @if($this->useCredentials)
            <div class="flex flex-col space-y-5" wire:key="credentials">
                <x-ark-input
                    type="text"
                    name="state.auth_username"
                    :label="trans('pages.'.$type.'-server-modal.inputs.username')"
                    :placeholder="trans('pages.'.$type.'-server-modal.inputs.username_placeholder')"
                    :tooltip="trans('pages.'.$type.'-server-modal.inputs.username_tooltip')"
                    tooltip-type="question"
                    tooltip-class="ml-2"
                    :errors="$errors"
                    autocomplete="off"
                />
                <x-ark-password-toggle
                    name="state.auth_password"
                    :label="trans('pages.'.$type.'-server-modal.inputs.password')"
                    :errors="$errors"
                    autocomplete="off"
                />
            </div>
        @else
            <div class="flex flex-col space-y-8" wire:key="access-key">
                <x-ark-input
                    type="text"
                    name="state.auth_access_key"
                    :label="trans('pages.'.$type.'-server-modal.inputs.access_key')"
                    :errors="$errors"
                    :tooltip="trans('pages.'.$type.'-server-modal.inputs.username_tooltip')"
                    tooltip-type="question"
                    tooltip-class="ml-2"
                    autocomplete="off"
                />
            </div>
        @endif
    </div>

    <x-ark-checkbox
        id="uses_bip38_encryption"
        name="state.uses_bip38_encryption"
        :label="trans('pages.'.$type.'-server-modal.inputs.uses_bip38_encryption')"
        label-classes="text-base cursor-pointer"
    />
</form>
