<?php

    namespace Soda\Analytics\Components;

    use Illuminate\Support\Facades\Facade;

    class SodaGoogleConfigFacade extends Facade {

        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor() {
            return 'soda-google-config';
        }

        public static function get()
        {
            return static::getFacadeRoot();
        }
    }
