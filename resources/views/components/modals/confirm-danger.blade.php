
@props([
    'title',
    'message',
    'showConfirm',
    'confirmAction',
    'closeConfirm' => 'closeConfirm',
    'confirmButton' => trans('actions.confirm'),
    'confirmIcon' => null,
    'image' => null,
    'imageClass' => 'px-32 my-8 w-full',
    'svg' => 'fortify-modal.delete-account',
    'svgClass' => 'text-theme-primary-600 px-32 my-8 w-full h-1/2',
])

<x-modals.confirm
    :title="$title"
    :message="$message"
    :show-confirm="$showConfirm"
    :image="$image"
    :image-class="$imageClass"
    :svg="$svg"
    :svg-class="$svgClass"
    :close-confirm="$closeConfirm"
    width-class="max-w-xl"
>
    <x-slot name="customButtons">
        <button
            type="button"
            dusk="modal-cancel"
            class="flex items-center button-secondary"
            wire:click="{{ $closeConfirm }}"
        >
            @lang('actions.cancel')
        </button>

        <button
            type="button"
            dusk="modal-confirm"
            class="flex items-center button-cancel"
            wire:click="{{ $confirmAction }}"
        >
            @if($confirmIcon)
                <x-ark-icon :name="$confirmIcon" size="sm" class="inline my-auto mr-2" />
            @endif

            {{ $confirmButton }}
        </button>
    </x-slot>
</x-modals.confirm>
