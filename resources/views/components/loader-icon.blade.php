@props([
    'size' => 'w-5 h-5',
])

<x-ark-loader-icon
    path-class="text-theme-primary-600"

    {{
        $attributes->class([
            $size,
            'text-theme-primary-100',
        ])
    }}
/>
