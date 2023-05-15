<div
    wire:init="loadLogs"
    wire:poll.60s="loadLogs"
    class="mt-8"
>
    {{-- desktop/tablet --}}
    <div class="hidden md:flex">
        <x-server.logs.tabs
            :logs="$this->logInstances"
            :processes="$processes"
            :server="$server"
            :view-model="$viewModel"
        />
    </div>

    {{-- mobile --}}
    <div class="md:hidden">
        <x-server.logs.dropdown
            :logs="$this->logInstances"
            :processes="$processes"
            :server="$server"
            :view-model="$viewModel"
        />
    </div>
</div>
