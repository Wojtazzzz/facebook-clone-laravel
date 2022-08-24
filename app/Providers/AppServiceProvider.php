<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        Carbon::macro('diffAbsolute', function () {
            return $this->diffForHumans([
                'syntax' => Carbon::DIFF_ABSOLUTE,
                'short' => true,
                'options' => Carbon::JUST_NOW | Carbon::ONE_DAY_WORDS | Carbon::TWO_DAY_WORDS,
            ]);
        });

        Carbon::macro('dependentFormat', function () {
            $weekAgo = now()->subWeek();
            $dayAgo = now()->subDay();

            $format = 'h:i';

            if ($this->isBefore($weekAgo)) {
                $format = 'j F Y \a\t h:i';
            } elseif ($this->isBefore($dayAgo)) {
                $format = 'l h:i';
            }

            return $this->format($format);
        });

        JsonResource::withoutWrapping();
    }
}
