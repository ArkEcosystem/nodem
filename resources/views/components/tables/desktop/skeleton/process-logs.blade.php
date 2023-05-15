<x-table-skeleton
    device="desktop"
    class="hidden lg:block"
    :items="[
        'general.date' => [
            'type' => 'text',
            'class' => 'w-32 text-left',
        ],
        'general.time' => [
            'type' => 'text',
            'class' => 'w-32 text-left',
        ],
        'general.type' => [
            'type' => 'log-type',
            'class' => 'w-32 text-left',
        ],
        'general.message' => [
            'type' => 'text',
            'lastOn' => 'full',
        ],
    ]"
/>
