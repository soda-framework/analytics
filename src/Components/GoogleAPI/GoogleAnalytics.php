<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Analytics;
    use Google_Service_Analytics_EntityUserLink;
    use Google_Service_Analytics_EntityUserLinkPermissions;
    use Google_Service_Analytics_UserRef;
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

        public function GetView($accountID, $propertyID, $optParams = []){
            $viewObjects = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID, $optParams);
            $viewObjects = $viewObjects->getItems();

            // Find where name is All Website Data
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
