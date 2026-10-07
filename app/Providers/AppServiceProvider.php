<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Product costs, commissions, selling prices and pay rates are admin-only.
        Gate::define('manage-pricing', fn (User $user) => $user->isAdmin());

        // The live attendance board, attendance history, corrections and who is tracked.
        Gate::define('manage-attendance', fn (User $user) => $user->isAdmin());

        // Stored in attendances.attendable_type instead of the class names.
        Relation::morphMap([
            'worker' => Worker::class,
            'user' => User::class,
        ]);

        // Every ->links() call renders the themed pager.
        Paginator::defaultView('frontend.componenet.pagination');
    }
}
