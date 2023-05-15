@props([
    'closeConfirm' => 'closeConfirmModal',
    'widthClass' => 'max-w-2xl',
    'title' => trans('generic.are-you-sure'),
    'svg' => null,
    'image' => null,
    'message' => null,
    'imageClass' => 'px-8 my-12 w-full',
    'svgClass' => 'px-8 my-12 w-full',
    'customButtons',
])

<div>
    @if($showConfirm ?? $this->showConfirmModal ?? false)
        <x-ark-modal
            wire-close="{{ $closeConfirm }}"
            title-class="header-2"
            :width-class="$widthClass"
        >
            @slot('title')
                {{ $title }}
            @endslot

            @slot('description')
                @if ($svg)
                    <div class="flex justify-center items-center w-full">
                        <x-ark-icon :name="$svg" :class="$svgClass" />
                    </div>
                @else
                    <img src="{{ asset($image) }}" class="{{ $imageClass }}" alt="" />
                @endif

                @if($message)
                    <div>
                        {!! $message !!}
                    </div>
                @endif
            @endslot

            @slot('buttons')
                {{ $customButtons }}
            @endslot
        </x-ark-modal>
    @endif
</div>
