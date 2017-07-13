<?php

    namespace Soda\Analytics\Components;

    use Illuminate\Support\Facades\Facade;

    class SodaAnalyticsFacade extends Facade {

        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor() {
            return 'soda-analytics';
        }
    }
