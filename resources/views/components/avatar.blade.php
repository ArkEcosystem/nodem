@props([
    'size' => 'w-full',
    'imageClass' => 'absolute object-cover w-full h-full',
    'identifier' => null,
    'rounded' => ''
])

@php
    $roundiness = Arr::get([
        'none' => 'rounded-none',
        'xl' => 'rounded-xl',
        '' => 'rounded',
    ], $rounded, 'rounded');

    $imageClass = $imageClass.' '.$roundiness;
@endphp

<div {{ $attributes->merge(['class' => $size . ' ' . $roundiness])}}>
    <div class="relative w-full pb-full">
        <x-ark-avatar :identifier="$identifier" class="{{ $imageClass }}" show-identifier-letters />
    </div>
</div>
