<?php

    namespace Soda\Analytics\Components;

    use Carbon\Carbon;
    use Soda\Analytics\Database\Models\Config;
    use Spatie\Analytics\Analytics;
    use Illuminate\Support\Collection;
    use Spatie\Analytics\Period;

    class SodaAnalytics extends Analytics {

        protected $config;

        public function setConfig(Config $config) {
            $this->config = $config;

            return $this;
        }

        public function config() {
            if(!$this->config) {
                $this->setConfig(Config::firstOrNew([]));
            }

            return $this->config;
        }

        public function fetchEvents(Period $period, string $category = ''): Collection {
            $others = ['dimensions' => 'ga:eventCategory, ga:eventAction, ga:eventLabel'];
            if ( $category ) $others['filters'] = 'ga:eventCategory==' . $category;

            $response = $this->performQuery(
                $period,
                'ga:totalEvents',
                $others
            );

            return collect($response['rows'] ?? [])->map(function (array $dateRow) {
                return [
                    'category' => $dateRow[0],
                    'action'   => $dateRow[1],
                    'label'    => $dateRow[2],
                    'count'    => $dateRow[3],
                ];
            });
        }
    }
