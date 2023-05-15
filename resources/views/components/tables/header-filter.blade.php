@props([
    'class' => 'flex justify-end',
])

<th class="w-16">
    <div class="{{ $class }}">
        <livewire:table-column-filter :columns="$columns" />
    </div>
</th>
