<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\UserTimetable;
use Illuminate\Support\Facades\Auth;

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
    Schema::defaultStringLength(191);

    \View::composer('*', function ($view) { // '*' aplica a TODAS las vistas
        if (Auth::check()) { // Verifica si el usuario está autenticado
            $userTimetable = UserTimetable::where('user_id', Auth::id())->get()->toArray();
            
            //comprobar el tipo de usuario
            $userType = Auth::check() ? Auth::user()->type : null; // Evitar errores si no hay usuario autenticado

            $view->with('userType', $userType);
        } else {
            $userTimetable = collect(); // Colección vacía si no está autenticado
        }

        $view->with('userTimetable', $userTimetable);
    });
}
}
