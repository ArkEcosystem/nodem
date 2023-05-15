@extends('layouts.app')

@section('content')
    <x-ark-pages-includes-header :title="$title ?? trans('pages.user-settings.page_name')" />

    <x-ark-container>
        <div class="flex mx-auto w-full lg:w-175">
            <div>{{ $slot }}</div>
        </div>
    </x-ark-container>
@endsection
