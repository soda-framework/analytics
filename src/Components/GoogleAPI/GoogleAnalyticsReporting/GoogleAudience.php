<?php
    namespace Soda\Analytics\Components\GoogleAPI\GoogleAnalyticsReporting;

    use Google_Service_AnalyticsReporting_DateRange;
    use Google_Service_AnalyticsReporting_Dimension;
    use Google_Service_AnalyticsReporting_GetReportsRequest;
    use Google_Service_AnalyticsReporting_Metric;
    use Google_Service_AnalyticsReporting_ReportRequest;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalyticsReporting;

    class GoogleAudience extends GoogleAnalyticsReporting
    {
        public function __construct() {
            parent::__construct();
        }

        public function GetAudience($startDate, $endDate) {
            try {
                $config = \GoogleConfig::get();

                $dateRange = new Google_Service_AnalyticsReporting_DateRange();
                $dateRange->setStartDate($startDate);
                $dateRange->setEndDate($endDate);

                // Create the Metrics object.
                $metrics = [];
                foreach (["ga:users","ga:sessions","ga:avgSessionDuration"] as $metric_name) {
                    $metric = new Google_Service_AnalyticsReporting_Metric();
                    $metric->setExpression($metric_name);
                    $metrics[] = $metric;
                }

                // Create the ReportRequest object.
                $request = new Google_Service_AnalyticsReporting_ReportRequest();
                $request->setViewId($config->view_id);
                $request->setDateRanges($dateRange);
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

        public function formatResultsArray($report) {
            $results = [];

            // get headers
            $header = $report->getColumnHeader();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();

            // get content
            $rows = $report->getData()->getRows();

            foreach ($rows as $row) {
                $metrics = $row->getMetrics();

                $metric = [];
                foreach ($metricHeaders as $key => $metricHeader) {
                    $metric[$metricHeader->getName()] = $metrics[0]->getValues()[$key];
                }

                $results[] = $metric;
            }

            return $results;
        }

        public function secondsToTime($seconds, $format="H:i:s") {
            return gmdate($format, $seconds);
        }
    }
