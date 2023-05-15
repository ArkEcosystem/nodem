<div class="-mb-4 divide-y md:-mb-0 table-list-mobile">
    @foreach ($invites as $invite)
        <div class="table-list-mobile-row">
            <x-tables.rows.mobile.text title="tables.invites.username" class="overflow-auto">
                <span class="block font-semibold truncate text-theme-secondary-900">
                    {{ $invite->username() }}
                </span>
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text title="tables.invites.invitation_code" class="overflow-auto">
                <div class="flex justify-between space-x-3 w-full font-semibold text-theme-secondary-900">
                    <div class="truncate">{{ $invite->code() }}</div>

                    <x-ark-clipboard
                        class="flex justify-center items-center h-full text-theme-primary-400 hover:text-theme-primary-700"
                        value="{{ $invite->code() }}"
                        icon-size="base"
                        no-styling
                    />
                </div>
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text title="tables.invites.role">
                @lang('roles.'.$invite->role())
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text title="tables.invites.date_generated">
                {{ $invite->dateGeneratedString() }}
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text class="flex justify-end w-full">
                <button
                    type="button"
                    class="inline-block flex justify-center items-center w-full sm:w-auto button-cancel"
                    wire:click="openDeleteInvitationCode('{{ $invite->code() }}')"
                >
                    <x-ark-icon name="trash" size="sm" class="my-0.5" />

                    <div class="ml-2 sm:hidden">
                        @lang('actions.delete')
                    </div>
                </button>
            </x-tables.rows.mobile.text>
        </div>
    @endforeach
</div>
