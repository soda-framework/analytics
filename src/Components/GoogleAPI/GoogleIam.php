<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Iam;
    use Google_Service_Iam_CreateServiceAccountKeyRequest;
    use Google_Service_Iam_CreateServiceAccountRequest;
    use Google_Service_Iam_ServiceAccount;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Cms\Support\Facades\Soda;

    class GoogleIam extends GoogleAPI
    {
        /**
         * @var $analytics Analytics object to be used.
         */
        public $iam;

        public function __construct() {
            parent::__construct();
            $this->iam = new Google_Service_Iam($this->client);
        }

        public function CreateServiceAccount($name) {
            try {
                $serviceAccount = new Google_Service_Iam_ServiceAccount();
                $displayName = preg_replace("/[^ \w]+/", "", config('soda.analytics.service-account-name')); // remove special characters
                $serviceAccount->setDisplayName($displayName);

                $accountRequest = new Google_Service_Iam_CreateServiceAccountRequest();
                $accountID = $name . '-' . hexdec(uniqid()); // randomize
                $accountID = substr($accountID, 0, 30); // trim, google has a max length (not sure what it is though, but 30 works)
                $accountRequest->setAccountId($accountID);
                $accountRequest->setServiceAccount($serviceAccount);

                $serviceAccount = $this->iam->projects_serviceAccounts->create('projects/'.$name, $accountRequest);

                return $serviceAccount;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function CreateServiceAccountKey($serviceAccountName) {
            try {
                $accountRequest = new Google_Service_Iam_CreateServiceAccountKeyRequest();
                $accountRequest->setIncludePublicKeyData(true);

                $serviceAccount = $this->iam->projects_serviceAccounts_keys->create($serviceAccountName, $accountRequest);

                $key = $serviceAccount->getPrivateKeyData();
                $key = base64_decode($key);

                return $key;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }
    }
