<div class="hidden md:block">
    @foreach($servers as $server)
        <livewire:server-list-item :model="$server->model()" show-as="grid-item" :wire:key="'grid-item-'.$server->id().'-'.Str::random(10)" />
    @endforeach
</div>
