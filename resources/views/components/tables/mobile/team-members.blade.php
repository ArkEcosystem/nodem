<div class="divide-y table-list-mobile">
    @foreach ($teamMembers as $teamMember)
        <div class="table-list-mobile-row" wire:key="team-member-{{ $teamMember->id() }}-mobile">
            <x-tables.rows.mobile.text class="w-full">
                <div class="flex items-center">
                    <x-avatar
                        :identifier="$teamMember->username()"
                        size="w-11"
                        rounded="xl"
                        class="hidden sm:flex"
                    />
                    <div class="flex w-full">
                        <span class="flex sm:hidden">@lang('general.username')</span>

                        <div class="flex justify-end w-full font-semibold text-right sm:justify-start sm:ml-3 sm:text-left text-theme-secondary-900">
                            <span class="w-32 truncate">{{ $teamMember->username() }}</span>

                            @if (Auth::user()->username === $teamMember->username())
                                <span class="ml-1 font-normal text-theme-secondary-500">@lang('general.you')</span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text title="tables.role">
                @lang('roles.'.$teamMember->role())
            </x-tables.rows.mobile.text>

            <x-tables.rows.mobile.text title="tables.date_joined">
                {{ $teamMember->createdAtLocal() }}
            </x-tables.rows.mobile.text>

            @if($teamMember->model()->isNot($currentUser) && ! $teamMember->isSuperAdmin())
                <x-tables.rows.mobile.text class="flex justify-end w-full">
                    @can(TeamMemberPermission::TEAM_MEMBERS_EDIT)
                        <button
                            type="button"
                            class="flex justify-center items-center w-full sm:w-auto button-secondary"
                            wire:click="$emit('openUpdateTeamMember', {{ $teamMember->id() }})"
                            data-tippy-content="{{ trans('actions.edit') }}"
                        >
                            <x-ark-icon name="pencil" size="sm" class="my-0.5" />
                        </button>
                    @endcan

                    @can(TeamMemberPermission::TEAM_MEMBERS_DELETE)
                        <button
                            type="button"
                            class="flex justify-center items-center ml-3 w-full sm:w-auto button-cancel"
                            wire:click="openConfirm('{{ $teamMember->id() }}')"
                            data-tippy-content="{{ trans('actions.remove') }}"
                        >
                            <x-ark-icon name="trash" size="sm" class="my-0.5" />
                        </button>
                    @endcan
                </x-tables.rows.mobile.text>
            @endif
        </div>
    @endforeach
</div>
