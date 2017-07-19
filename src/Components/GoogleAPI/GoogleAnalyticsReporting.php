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

        public function GetEvents($startDate, $endDate) {
            try {
                $config = \GoogleConfig::get();

                $dateRange = new Google_Service_AnalyticsReporting_DateRange();
                $dateRange->setStartDate($startDate);
                $dateRange->setEndDate($endDate);

                //Create the Dimensions object.
                $dimensions = [];
                foreach(["ga:eventCategory", "ga:eventAction", "ga:eventLabel"] as $dimension_name) {
                    $dimension = new Google_Service_AnalyticsReporting_Dimension();
                    $dimension->setName($dimension_name);
                    $dimensions[] = $dimension;
                }

                // Create the Metrics object.
                $metrics = [];
                foreach (["Value"=>"ga:eventValue","Total Events"=>"ga:totalEvents", "Unique Events" => "ga:uniqueEvents"] as $alias=>$metric_name) {
                    $metric = new Google_Service_AnalyticsReporting_Metric();
                    $metric->setExpression($metric_name);
                    $metric->setAlias($alias);
                    $metrics[] = $metric;
                }

                // Create the ReportRequest object.
                $request = new Google_Service_AnalyticsReporting_ReportRequest();
                $request->setViewId($config->view_id);
                $request->setDateRanges($dateRange);
                $request->setDimensions($dimensions);
                $request->setMetrics($metrics);

                $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
                $body->setReportRequests([$request]);

//            $analytics = new Google_Service_AnalyticsReporting($client);
                return $this->analytics_reporting->reports->batchGet($body);

            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        /**
         * Parses and prints the Analytics Reporting API V4 response.
         *
         * @param An Analytics Reporting API V4 response.
         */
        public function formatResults($reports) {

            for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
                $report = $reports[$reportIndex];
                $header = $report->getColumnHeader();
                $dimensionHeaders = $header->getDimensions();
                $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
                $rows = $report->getData()->getRows();

                for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                    $row = $rows[$rowIndex];
                    $dimensions = $row->getDimensions();
                    $metrics = $row->getMetrics();
                    for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "<br/>");
                    }

                    for ($j = 0; $j < count($metrics); $j++) {
                        $values = $metrics[$j]->getValues();
                        for ($k = 0; $k < count($values); $k++) {
                            $entry = $metricHeaders[$k];
                            print($entry->getName() . ": " . $values[$k] . "<br/>");
                        }
                    }
                }
            }
        }

        public function formatResultsArray($report) {
            $results = [];

            // get headers
            $header = $report->getColumnHeader();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();

            // get content
            $rows = $report->getData()->getRows();

            foreach ($rows as $row) {
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();

                $metric = [];
                foreach ($metricHeaders as $key => $metricHeader) {
                    $metric[$metricHeader->getName()] = $metrics[0]->getValues()[$key];
                }

                $results = self::arrayExisting($results, $dimensions[0]);
                $results[$dimensions[0]] = self::arrayExisting($results[$dimensions[0]], $dimensions[1]);
                $results[$dimensions[0]][$dimensions[1]] = self::arrayExisting($results[$dimensions[0]][$dimensions[1]], $dimensions[2]);
                $results[$dimensions[0]][$dimensions[1]][$dimensions[2]] = $metric;
            }

            return $results;
        }

        public function arrayExisting($arr,$index){
            if( !isset($arr[$index]) ){
                $arr[$index] = null;
            }
            return $arr;
        }

    }
