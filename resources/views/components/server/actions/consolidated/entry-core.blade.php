@props([
    'action',
    'server',
    'disabled' => false,
    'tooltip'  => null,
])

<x-server.actions.action-processes
    types="core"
    :disabled="!$server->{'can'.ucfirst($action).'Core'}() || $disabled"
    :action="$action"
    :server="$server"
    class="block py-4 px-10 w-full text-left focus-visible:rounded"
/>
