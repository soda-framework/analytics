<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Analytics;
    use Google_Service_Analytics_Webproperty;
    use Soda\Analytics\Components\GoogleAPI;

    class GoogleAnalytics extends GoogleAPI
    {
        /**
         * @var $analytics Analytics object to be used.
         */
        public $analytics;

        public function __construct() {
            parent::__construct();
            $this->analytics = new Google_Service_Analytics($this->client);
        }

        public function GetAccounts($optParams = []) {
            try {
                $accountsObject = $this->analytics->management_accounts->listManagementAccounts($optParams);
                $accounts = $accountsObject->getItems();

                return $accounts;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function GetAccountProperties($accountID, $optParams = []) {
            try {
                $propertiesObject = $this->analytics->management_webproperties->listManagementWebproperties($accountID, $optParams);
                $properties = $propertiesObject->getItems();

                return $properties;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function CreateAccountProperty($accountID, $propertyName, $optParams = []) {
            try {
                $property = new Google_Service_Analytics_Webproperty();
                $property->setName($propertyName);

                $propertyObject = $this->analytics->management_webproperties->insert($accountID, $property, $optParams);
                dd( $propertyObject );

                return $propertyObject;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }
    }
