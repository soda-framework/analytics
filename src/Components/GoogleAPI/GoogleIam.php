<?php
    namespace Soda\Analytics\Components\GoogleAPI;

    use Google_Service_Iam;
    use Google_Service_Iam_CreateServiceAccountKeyRequest;
    use Google_Service_Iam_CreateServiceAccountRequest;
    use Google_Service_Iam_ServiceAccount;
    use Soda\Analytics\Components\GoogleAPI;

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
                $serviceAccount->setDisplayName('Soda Analytics Service Account');

                $accountRequest = new Google_Service_Iam_CreateServiceAccountRequest();
                $accountRequest->setAccountId($name . '-' . hexdec(uniqid()));
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

        public function CreateServiceAccountKey($serviceAccount=null) {
            try {
                $accountRequest = new Google_Service_Iam_CreateServiceAccountKeyRequest();
                $accountRequest->setIncludePublicKeyData(true);

//                $serviceAccount = $this->iam->projects_serviceAccounts_keys->create($serviceAccount->getName(), $accountRequest);
                $serviceAccount = $this->iam->projects_serviceAccounts_keys->create('projects/spotify-aami/serviceAccounts/spotify-aami-1572875469643774@spotify-aami.iam.gserviceaccount.com', $accountRequest);

                $key = $serviceAccount->getPrivateKeyData();
                $key = base64_decode($key);

                $config = \GoogleConfig::get();
                $config->service_account_credentials_json = $key;
                $config->save();

                return true;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }
    }
