<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Analytics;
    use Google_Service_Analytics_EntityUserLink;
    use Google_Service_Analytics_EntityUserLinkPermissions;
    use Google_Service_Analytics_Profile;
    use Google_Service_Analytics_UserRef;
    use Google_Service_Analytics_Webproperty;
    use Google_Service_AnalyticsReporting_DateRange;
    use Google_Service_AnalyticsReporting_GetReportsRequest;
    use Google_Service_AnalyticsReporting_Metric;
    use Google_Service_AnalyticsReporting_ReportRequest;
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

        public function AddUser($accountId, $webPropertyId, $profileId, $userEmail) {
            try {
                $userRef = new Google_Service_Analytics_UserRef();
                $userRef->setEmail($userEmail);

                $userLinkPermissions = new Google_Service_Analytics_EntityUserLinkPermissions();
                $userLinkPermissions->setLocal(['READ_AND_ANALYZE']);

                $entityUserLink = new Google_Service_Analytics_EntityUserLink();
                $entityUserLink->setUserRef($userRef);
                $entityUserLink->setPermissions($userLinkPermissions);

                $this->analytics->management_profileUserLinks->insert($accountId, $webPropertyId, $profileId, $entityUserLink);
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
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

        public function CreateAccount($accountName) {
            try {
                //TODO: Wait for google to update the api
                $account = new \Google_Service_Analytics_Account();
                $account->setName($accountName);

                $account = $this->analytics->management_accounts->insert($accountName);

                return $account;
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
                // create a new property
                $property = new Google_Service_Analytics_Webproperty();
                $property->setName($propertyName);
                $property->setWebsiteUrl(\Request::root());
                $property->setIndustryVertical('UNSPECIFIED');
                $propertyObject = $this->analytics->management_webproperties->insert($accountID, $property, $optParams);

                // create a default view/profile now that we have an id
                $view = new Google_Service_Analytics_Profile();
                $view->setName('All Web Site Data');
                $viewObject = $this->analytics->management_profiles->insert($accountID, $propertyObject->id, $view, $optParams);

                // update the new property to have this default view
                $property = new Google_Service_Analytics_Webproperty();
                $property->setDefaultProfileId($viewObject->id);
                $propertyObject = $this->analytics->management_webproperties->patch($accountID, $propertyObject->id, $property);

                return $propertyObject;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function GetAccountPropertyViews($accountID, $propertyID, $optParams = []) {
            try {
                $viewsObject = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID, $optParams);
                $views = $viewsObject->getItems();

                return $views;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function CreateAccountPropertyView($accountID, $propertyID, $viewName, $optParams = []) {
            try {
                $view = new Google_Service_Analytics_Profile();
                $view->setName($viewName);

                $viewObject = $this->analytics->management_profiles->insert($accountID, $propertyID, $view, $optParams);

                return $viewObject;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function GetView($accountID, $propertyID, $optParams = []){
            $viewObjects = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID, $optParams);
            $viewObjects = $viewObjects->getItems();

            // Find where name is All Web Site Data
            $viewObject = array_filter(
                $viewObjects,
                function ($e) {
                    return $e->name == 'All Web Site Data';
                }
            );
            $viewObject = reset($viewObject);
            return $viewObject;
        }
    }
