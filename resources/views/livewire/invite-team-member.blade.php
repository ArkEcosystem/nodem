<div>
    @if($this->modalShown)
        @if($this->readonly)
            @include('includes.invitation.readonly')
        @else
            @include('includes.invitation.invite')
        @endif
    @endif
</div>

@push('scripts')
    <script src="{{ mix('js/clipboard.js') }}" defer></script>
@endpush
