<?php

declare(strict_types=1);

use App\Models\InvitationCode;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use App\ViewModels\ActivityLogViewModel;
use App\ViewModels\InvitationCodeViewModel;
use App\ViewModels\ProcessViewModel;
use App\ViewModels\ServerViewModel;
use App\ViewModels\UserViewModel;
use App\ViewModels\ViewModelFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;
use Tests\InvalidModel;

it('should make a view model', function ($modelClass, $viewModel) {
    expect(ViewModelFactory::make($modelClass::factory()->create()))->toBeInstanceOf($viewModel);
})->with([
    [User::class, UserViewModel::class],
    [Server::class, ServerViewModel::class],
    [Process::class, ProcessViewModel::class],
    [InvitationCode::class, InvitationCodeViewModel::class],
]);

it('should make a view model for the log', function () {
    $model = Activity::create(['description' => 'whatever']);
    expect(ViewModelFactory::make($model))->toBeInstanceOf(ActivityLogViewModel::class);
});

it('should make a view model collection', function ($modelClass, $viewModel) {
    $models = $modelClass::factory(10)->create();

    expect(ViewModelFactory::collection($models))->toBeInstanceOf(Collection::class);

    foreach ($models as $model) {
        expect($model)->toBeInstanceOf($viewModel);
    }
})->with([
    [User::class, UserViewModel::class],
    [Server::class, ServerViewModel::class],
    [Process::class, ProcessViewModel::class],
    [InvitationCode::class, InvitationCodeViewModel::class],
]);

it('should make a view model pagination', function ($modelClass, $viewModel) {
    $modelClass::factory(10)->create();

    expect(ViewModelFactory::paginate($modelClass::paginate()))->toBeInstanceOf(LengthAwarePaginator::class);
})->with([
    [User::class, UserViewModel::class],
    [Server::class, ServerViewModel::class],
    [Process::class, ProcessViewModel::class],
    [InvitationCode::class, InvitationCodeViewModel::class],
]);

it('cannot make an invalid view model', function () {
    $this->expectException(InvalidArgumentException::class);

    ViewModelFactory::make(new InvalidModel());
})->throws(InvalidArgumentException::class);
