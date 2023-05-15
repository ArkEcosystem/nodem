@props([
    'action',
    'server',
    'types'    => ['all', 'relay', 'forger'],
    'disabled' => false,
    'class'    => 'block p-4 focus-visible:rounded w-full text-left',
])

@php
    $isMultiple = is_array($types);
@endphp

@foreach(collect($types) as $type)
    @php
        $permission = 'can'.ucfirst($action);
        if ($type === 'manager') {
            $permission .= 'CoreManager';
        } else {
            $permission .= Str::of($type)->studly();
        }

        $canPerformAction = $server->{$permission}() && ! $disabled;
    @endphp

    <button
        type="button"
        @if ($disabled) disabled @endif
        @class([
            $class,
            'cursor-pointer text-theme-secondary-900 hover:bg-theme-secondary-100' => $canPerformAction,
            'cursor-not-allowed text-theme-secondary-700' => ! $canPerformAction,
        ])
        @if ($canPerformAction && $server->actionRequiresPassword($action, $type))
            x-on:click="
                $dispatch('dropdown-close');
                window.livewire.emit('askForBip38Password', [
                    '{{ $action }}',
                    '{{ $type }}',
                    {{ $server->id() }},
                ]);
            "
        @elseif ($canPerformAction)
            x-on:click="
                $dispatch('dropdown-close');
                window.livewire.emit('triggerServerAction', [
                    '{{ $action }}',
                    '{{ $type }}',
                    {{ $server->id() }},
                ]);
            "
        @endif
    >
        <span class="relative font-semibold whitespace-normal">
            @if ($action === 'update' && in_array($type, ['core', 'manager']) && $server->hasNewVersionFor($type))
                <x-ark-notification-dot class="top-0 right-0 p-0.5 -mt-1.5 -mr-2 bg-white" />

                @lang('server.types.'.$type)
            @else
                @if ($isMultiple)
                    @lang('server.types.'.$type)
                @else
                    @lang('server.actions.'.$action)
                @endif
            @endif
        </span>
    </button>
@endforeach
