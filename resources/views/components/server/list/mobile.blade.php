<div class="py-8 md:hidden">
    <div class="flex flex-col space-y-5 content-container">
        @foreach($servers as $server)
            <livewire:server-list-item :model="$server->model()" show-as="mobile-item" :wire:key="'mobile-item-'.$server->id().'-'.Str::random(10)" />
        @endforeach

        @if($servers->count() === 0)
            <x-ark-no-results :text="trans('pages.home.no_servers')" exclude-dark />
        @endif
    </div>
</div>
