<x-ark-tables.table sticky class="hidden w-full md:block">
    <thead>
        <tr>
            <x-tables.headers.desktop.text
                name="tables.username"
                class="w-28 text-left"
            />
            <x-tables.headers.desktop.text
                name="tables.role"
                class="w-60 text-left"
            />
            <x-tables.headers.desktop.text
                name="tables.date_joined"
                class="text-left w-33 last-cell"
            />
            <x-tables.headers.desktop.text class="w-33" />
        </tr>
    </thead>
    <tbody>
        @foreach($teamMembers as $teamMember)
            <x-ark-tables.row wire:key="team-member-{{ $teamMember->id() }}-desktop">
                <x-ark-tables.cell class="font-semibold text-theme-secondary-900">
                    <x-avatar
                        :identifier="$teamMember->username()"
                        size="w-11"
                        rounded="xl"
                    />
                    <div class="flex ml-3 w-64">
                        <span class="truncate">
                            {{ $teamMember->username() }}
                        </span>

                        @if (Auth::user()->username === $teamMember->username())
                            <span class="ml-1 font-normal text-theme-secondary-500">@lang('general.you')</span>
                        @endif
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    @lang('roles.'.$teamMember->role())
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-left">
                    {{ $teamMember->createdAtLocal() }}
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    @if($teamMember->model()->isNot($currentUser) && ! $teamMember->isSuperAdmin())
                        @can(TeamMemberPermission::TEAM_MEMBERS_EDIT)
                            <button
                                type="button"
                                class="button-secondary"
                                wire:click="$emit('openUpdateTeamMember', {{ $teamMember->id() }})"
                                data-tippy-content="{{ trans('actions.edit') }}"
                            >
                                <x-ark-icon name="pencil" size="sm" class="my-0.5" />
                            </button>
                        @endcan

                        @can(TeamMemberPermission::TEAM_MEMBERS_DELETE)
                            <button
                                type="button"
                                class="ml-2 button-cancel"
                                wire:click="openConfirm('{{ $teamMember->id() }}')"
                                data-tippy-content="{{ trans('actions.remove') }}"
                            >
                                <x-ark-icon name="trash" size="sm" class="my-0.5" />
                            </button>
                        @endcan
                    @endif
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
