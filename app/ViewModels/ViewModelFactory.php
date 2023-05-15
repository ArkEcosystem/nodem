<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\InvitationCode;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Spatie\Activitylog\Models\Activity;

final class ViewModelFactory
{
    public static function make(Model $model): ViewModel
    {
        if ($model instanceof User) {
            return new UserViewModel($model);
        }

        if ($model instanceof Server) {
            return new ServerViewModel($model);
        }

        if ($model instanceof Process) {
            return new ProcessViewModel($model);
        }

        if ($model instanceof InvitationCode) {
            return new InvitationCodeViewModel($model);
        }

        if ($model instanceof Activity) {
            return new ActivityLogViewModel($model);
        }

        throw new InvalidArgumentException('Invalid View Model Type.');
    }

    public static function collection(Collection $models): Collection
    {
        return $models->transform(fn ($model) => static::make($model));
    }

    public static function paginate(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $paginator->getCollection()->transform(fn ($model) => static::make($model));

        return $paginator;
    }
}
