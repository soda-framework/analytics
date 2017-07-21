<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Analytics;
    use Google_Service_Analytics_EntityUserLink;
    use Google_Service_Analytics_EntityUserLinkPermissions;
    use Google_Service_Analytics_UserRef;
    use Google_Service_Analytics_Webproperty;
    use Google_Service_AnalyticsReporting;
    use Google_Service_AnalyticsReporting_DateRange;
    use Google_Service_AnalyticsReporting_Dimension;
    use Google_Service_AnalyticsReporting_GetReportsRequest;
    use Google_Service_AnalyticsReporting_Metric;
    use Google_Service_AnalyticsReporting_ReportRequest;
    use Soda\Analytics\Components\GoogleAPI;

    class GoogleAnalyticsReporting extends GoogleAPI
    {
        /**
         * @var $analytics Analytics object to be used.
         */
        public $analytics_reporting;

        public function __construct() {
            parent::__construct(GoogleAPI::ANALYTICS);
            $this->analytics_reporting = new Google_Service_AnalyticsReporting($this->client);
        }

        public function arrayExisting($arr,$index){
            if( !isset($arr[$index]) ){
                $arr[$index] = null;
            }
            return $arr;
        }

    }
