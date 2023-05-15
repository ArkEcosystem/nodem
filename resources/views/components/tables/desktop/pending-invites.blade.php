<x-ark-tables.table sticky class="hidden w-full md:block">
    <thead>
        <tr>
            <x-tables.headers.desktop.text
                name="tables.invites.username"
                class="w-28 text-left lg:w-48"
            />
            <x-tables.headers.desktop.text
                name="tables.invites.invitation_code"
                class="w-60 text-left"
            />
            <x-tables.headers.desktop.text name="tables.invites.role" />
            <x-tables.headers.desktop.text
                name="tables.invites.date_generated"
                class="text-left w-33 last-cell"
            />
            <x-tables.headers.desktop.text class="w-14" />
        </tr>
    </thead>
    <tbody>
        @foreach($invites as $invite)
            <x-ark-tables.row wire:key="invite-{{ $invite->id() }}">
                <x-ark-tables.cell class="font-semibold text-theme-secondary-900">
                    <div class="w-28 lg:w-48 truncate">
                        {{ $invite->username() }}
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="font-semibold text-theme-secondary-900">
                    <div class="flex justify-between space-x-3 w-full">
                        <div>{{ $invite->code() }}</div>

                        <x-ark-clipboard
                            class="flex justify-center items-center h-full focus-visible:rounded text-theme-primary-400 hover:text-theme-primary-700"
                            value="{{ $invite->code() }}"
                            icon-size="base"
                            no-styling
                        />
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    @lang('roles.'.$invite->role())
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    {{ $invite->dateGeneratedString() }}
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <button
                        type="button"
                        class="button-cancel"
                        wire:click="openDeleteInvitationCode('{{ $invite->code() }}')"
                    >
                        <x-ark-icon name="trash" size="sm" class="my-0.5" />
                    </button>
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
