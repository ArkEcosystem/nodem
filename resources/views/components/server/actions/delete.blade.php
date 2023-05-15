<x-server.actions.button
    :disabled="$disabled ?? false"
    icon="trash"
    button-class="button-cancel"
    on-click="window.livewire.emit('triggerServerDelete', [{{ $server->id() }}])"
/>
