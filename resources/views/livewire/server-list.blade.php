<div
    x-data="{
        hiddenColumns: {{ Js::from(Auth::user()->getHiddenColums()) }},
        tableView: '{{ $this->user->defaultTableView() }}',
        initTippy() {
            const gridListEl = this.$refs['grid-list'];
            const tableListEl = this.$refs['table-list'];

            if (this.tableView === 'grid') {
                initTippy(gridListEl);
            } else {
                initTippy(tableListEl);
            }
        },
        init() {
            this.initTippy();

            this.$watch('tableView', () => {
                this.$nextTick((tableView) => {
                    this.initTippy();
                });
            });

            window.livewire.on('columnRefresh', (columns) => {
                this.hiddenColumns = columns
            });
        }
    }"
    x-on:updated-table-view="tableView = $event.detail"
    x-cloak
>
    <x-dashboard.header :table-view="$this->user->defaultTableView()" />

    @if($servers->count())
        <div wire:poll.60s>
            <div x-ref="grid-list" x-show="tableView === 'grid'">
                <x-server.grid.desktop :servers="$servers" />
            </div>

            <div x-ref="table-list" x-show="tableView === 'list'">
                <x-server.list.desktop :servers="$servers" />
            </div>

            <x-server.list.mobile :servers="$servers" />
        </div>

        <x-pagination :results="$servers" class="mb-4" />

        <livewire:edit-server-modal />
    @else
        <x-server.no-results />
    @endif
</div>
