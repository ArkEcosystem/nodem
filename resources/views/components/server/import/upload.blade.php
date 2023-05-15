<div>
    <div
        x-data="FileUpload(
            'file-single-upload-jsonFile',
            @entangle($attributes->wire('model'))
        )"
        x-init="() => {
            this.model = null;
            this.isUploading = false;
            this.isErrored = false;
        }"
        x-on:livewire-upload-start="isUploading = true;"
        x-on:livewire-upload-finish="isUploading = false;"
        x-on:livewire-upload-error="isUploading = false;"
    >

        <div x-show="! isErrored">
            <h2 class="mb-4">@lang('pages.import-servers.title')</h2>
            <span class="leading-7 text-theme-secondary-700">@lang('pages.import-servers.description')</span>
        </div>

        <div x-show="isErrored" x-cloak>
            <h2 class="mb-4">@lang('pages.import-servers.import-error.title')</h2>
            <span class="leading-7 text-theme-secondary-700">@lang('pages.import-servers.import-error.description')</span>
        </div>

        <div class="relative w-full">
            <div
                class="p-2 mt-8 w-full rounded-xl border-2 border-dashed focus-within:ring-2 focus-within:ring-offset-4 h-50.5 border-theme-primary-100 focus-within:ring-theme-primary-500"
                x-show="! isUploading && ! isErrored"
            >
                <label
                    class="block w-full h-full rounded-xl transition-default hover:bg-theme-primary-100"
                    :class="{
                        'bg-theme-primary-50': !dragHover,
                        'bg-theme-primary-100': dragHover,
                    }"
                    x-data="{
                        dragHover: false,
                        init() {
                            this.$root.addEventListener('dragenter', (e) => {
                                this.dragHover = true;
                            })
                            this.$root.addEventListener('dragleave', (e) => {
                                this.dragHover = false;
                            })
                        }
                    }"
                    @click.self="select"
                >
                    <input
                        id="file-single-upload-jsonFile"
                        type="file"
                        class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                        accept="application/json"
                        wire:model="jsonFile"
                    />

                    <div class="flex absolute top-2 right-2 bottom-2 left-2 flex-col justify-center items-center space-y-3 rounded-xl pointer-events-none select-none">
                        <div class="mb-5 text-theme-primary-500">
                            <x-ark-icon name="app-document-json" size="xl"/>
                        </div>

                        <div class="flex text-lg font-semibold leading-8 text-theme-secondary-900">
                            <span class="hidden md:block">@lang('pages.import-servers.upload_description.drag_and_drop')</span>
                            <span class="md:ml-2 text-theme-primary-600">@lang('pages.import-servers.upload_description.browse')</span>
                        </div>

                        <div class="text-sm font-semibold text-theme-secondary-500">
                            @lang('pages.import-servers.supported_format', ['format' => '.json'])
                        </div>
                    </div>
                </label>
            </div>

            <div
                class="flex justify-center mt-8"
                x-show="isUploading && ! isErrored"
                x-cloak
            >
                <x-loader-icon size="w-10 h-10" />
            </div>
        </div>

        <div
            class="flex flex-col items-center p-4 mt-8 space-y-4 w-full rounded-xl border sm:flex-row sm:justify-between sm:space-y-0 border-theme-secondary-300"
            x-show="isErrored"
            x-cloak
        >
            <div class="flex flex-col justify-center items-center space-y-4 sm:flex-row sm:space-y-0">
                <x-ark-icon name="app-document-json" size="xl" />

                <span class="ml-5 text-lg font-semibold leading-8 text-theme-secondary-900">{{ $this->filename }}</span>
            </div>

            <button wire:click="resetWizard" type="button">
                <x-ark-icon name="circle.cross-big" class="text-theme-danger-400 hover:text-theme-danger-600" />
            </button>
        </div>

        @error('jsonFile')
            <div class="flex flex-col items-end w-full">
                <x-ark-alert class="mt-5 w-full" type="warning">{!! $message !!}</x-ark-alert>
                <button
                    type="button"
                    class="mt-8 w-full sm:w-auto button-secondary"
                    wire:click="resetWizard"
                >
                    @lang('actions.back')
                </button>
            </div>
        @enderror
    </div>
</div>

