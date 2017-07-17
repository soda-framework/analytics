<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_ServiceManagement;
    use Google_Service_ServiceManagement_EnableServiceRequest;
    use Soda\Analytics\Components\GoogleAPI;

    class GoogleService extends GoogleAPI
    {
        public $service;

        public function __construct() {
            parent::__construct();
            $this->service = new Google_Service_ServiceManagement($this->client);
        }

        public function EnableAPI($consumerId,$apis=[]) {
            try {
                $enableRequest = new Google_Service_ServiceManagement_EnableServiceRequest();
                $enableRequest->setConsumerId($consumerId);

                if( is_string($apis) ) $apis = [$apis];
                foreach ($apis as $api) {
                    $this->service->services->enable($api, $enableRequest);
                }
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }
    }
