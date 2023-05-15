<div class="flex flex-col content-container md:w-191">
    <div class="flex mt-8 space-x-2 w-full">
        <div class="flex-1 border-b-2 border-theme-warning-300"></div>
        <div
            @class([
                'flex-1 border-b-2',
                'border-theme-warning-300' => $this->currentStep >= 2,
                'border-theme-primary-100' => $this->currentStep < 2,
            ])
        ></div>
        <div
            @class([
                'flex-1 border-b-2',
                'border-theme-warning-300' => $this->currentStep === 3,
                'border-theme-primary-100' => $this->currentStep !== 3,
            ])
        ></div>
    </div>

    <div class="py-8">
        @if ($this->currentStep === 1)
            <x-server.import.upload />
        @elseif ($this->currentStep === 2)
            <x-server.import.manage />
        @elseif ($this->currentStep === 3)
            <x-server.import.complete />
        @endif
    </div>
</div>