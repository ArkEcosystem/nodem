<x-ark-container container-class="flex flex-col items-center space-y-6">
    <img
        src="{{ asset('images/defaults/no-results.svg') }}"
        width="269"
        height="94"
        alt=""
    />

    <p class="max-w-2xl leading-7 text-center">
        @lang('pages.home.no_servers')
    </p>
</x-ark-container>
