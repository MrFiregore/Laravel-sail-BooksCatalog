<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;

    class ValidatorBooleanServiceProvider extends ServiceProvider
    {
        /**
         * Register services.
         *
         * @return void
         */
        public function register()
        {
            //
        }

        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot()
        {
            $this->app['validator']->extendDependent('trueboolean', Validation\Boolean::class . '@validate');
        }
    }
