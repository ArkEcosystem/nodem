@props([
    'server' => null,
    'type' => null,
    'disabledStyle' => 'flex justify-between items-center py-4 px-4 cursor-not-allowed',
    'class' => null,
])

@if ($server->canUpdate($type))
    <x-server.actions.action-processes
        action="update"
        :server="$server"
        :types="[$type]"
        :class="$class"
    />
@else
    <div @class([
        $class,
        $disabledStyle,
    ])>
        <span class="whitespace-normal">
            @lang('server.types.'.$type)
        </span>
    </div>
@endif
