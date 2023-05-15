<?php

declare(strict_types=1);

use App\Http\Livewire\ServerPerformance;
use App\Models\ResourceIndicator;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Livewire;

beforeEach(fn () => $this->travelTo(Carbon::parse('2021-08-20 12:17')));

it('has a server', function () {
    $server = Server::factory()->create();

    Livewire::test(ServerPerformance::class, [
        'server' => $server,
    ])->assertSet('server', fn ($model) => $model->is($server));
});

it('defaults a selected configuration', function () {
    $server = Server::factory()->create();

    Livewire::test(ServerPerformance::class, [
        'server' => $server,
    ])->assertSet('selectedConfiguration', 'ram');
});

it('renders a component', function () {
    $server = Server::factory()->create();

    Livewire::test(ServerPerformance::class, [
        'server' => $server,
    ])->assertViewIs('livewire.server-performance')->assertViewHas('charts', function ($charts) {
        expect($charts)->toHaveKeys(['ram', 'cpu', 'disk']);

        collect($charts)->each(function ($chart) {
            expect($chart)->toHaveKeys(['day']);

            collect($chart)->each(function ($period) {
                expect($period)->toHaveKeys(['labels', 'datasets']);

                expect($period['labels'])->toBeArray();
                expect($period['datasets'])->toBeInstanceOf(Collection::class);
            });
        });

        return true;
    });
});

it('generates ticks for all periods', function () {
    $server = Server::factory()->create([
        'created_at' => Carbon::parse('2019-07-07'),
    ]);

    Livewire::test(ServerPerformance::class, [
        'server' => $server,
    ])->assertViewIs('livewire.server-performance')->assertViewHas('charts', function ($charts) {
        collect($charts)->each(function ($chart) {
            // Day labels...
            expect($chart['day']['labels'])->toMatchArray([
                '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00',
                '20:00', '21:00', '22:00', '23:00', '00:00', '01:00', '02:00', '03:00',
                '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', 'Now',
            ]);

            // Week labels...
            expect($chart['week']['labels'])->toMatchArray([
                'Friday', 'Saturday', 'Sunday', 'Monday',
                'Tuesday', 'Wednesday', 'Thursday', 'Today',
            ]);

            // Month...
            expect($chart['month']['labels'])->toMatchArray([
                '20.07', '21.07', '22.07', '23.07', '24.07', '25.07', '26.07', '27.07', '28.07', '29.07', '30.07',
                '31.07', '01.08', '02.08', '03.08', '04.08', '05.08', '06.08', '07.08', '08.08', '09.08', '10.08',
                '11.08', '12.08', '13.08', '14.08', '15.08', '16.08', '17.08', '18.08', '19.08', 'Today',
            ]);

            // Year...
            expect($chart['year']['labels'])->toMatchArray([
                'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug',
            ]);

            // All time...
            expect($chart['all']['labels'])->toMatchArray([
                '2019', '2020', '2021',
            ]);
        });

        return true;
    });
});

it('retrieves todays charts', function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    Livewire::test(ServerPerformance::class, [
        'server' => $server,
    ])->assertViewHas('charts', function ($charts) {
        $cpu  = $charts['cpu']['day']['datasets'];
        $disk = $charts['disk']['day']['datasets'];

        // RAM...
        $ram = $charts['ram']['day']['datasets'];
        expect($ram[24])->toBe(round(1.6 * 100 / 2, 2));
        expect($ram[23])->toBe(round(1.65 * 100 / 2, 2));

        $ram = tap(collect($ram), function ($collection) {
            $collection->pop();
            $collection->pop();
        });

        expect($ram->every(fn ($value) => $value === null))->toBeTrue();

        // CPU...
        $cpu = $charts['cpu']['day']['datasets'];
        expect($cpu[24])->toBe(36.0);
        expect($cpu[23])->toBe(58.5);

        $cpu = tap(collect($cpu), function ($collection) {
            $collection->pop();
            $collection->pop();
        });

        expect($cpu->every(fn ($value) => $value === null))->toBeTrue();

        // Disk...
        $disk = $charts['disk']['day']['datasets'];
        expect($disk[24])->toBe(round(7 * 100 / 20, 2));
        expect($disk[23])->toBe(round(11.5 * 100 / 20, 2));

        $disk = tap(collect($disk), function ($collection) {
            $collection->pop();
            $collection->pop();
        });

        expect($disk->every(fn ($value) => $value === null))->toBeTrue();

        return true;
    });
});

it('ignores unknown ticks', function () {
    $server = Server::factory()->create([
        'created_at' => Carbon::parse('2019-07-07'),
    ]);

    $ticks = Livewire::test(ServerPerformance::class, ['server' => $server])
        ->instance()
        ->ticks('unknown');

    expect($ticks)->toBeArray();
    expect($ticks)->toHaveCount(0);
});
