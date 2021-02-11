<?php

namespace Bims;

use Google\Client;
use GuzzleHttp\Client as Http;
use Throwable;
use Bims\Instance;

class WPeopleAPI
{

    private $file_token,
        $base_url = 'http://localhost/testapi/wp-admin/options-general.php?page=wpeopleapi-setting',
        $client,
        $message,
        $client_secret,
        $prefix;

    public function __construct($client_id = false, $client_secret = false)
    {
        $init = Instance::init();
        $this->prefix           = $init;
        $this->file_token       = 'access_token_' . $init . '.json';
        $this->client_secret    = 'client_secret_' . $init . '.json';

        if ($client_id && $client_secret) {
            $this->storeClientSecret($client_id, $client_secret);
        }

        if (!is_file(dirname(__FILE__) . '/' . $this->client_secret)) {
            $this->message = 'Client secret not found';
            return false;
        }

        $client = new Client();
        $client->setAccessType('offline');
        $client->setAuthConfig(dirname(__FILE__) . '/' . $this->client_secret);
        $client->addScope('https://www.googleapis.com/auth/contacts');
        $client->setIncludeGrantedScopes(true);
        $client->setRedirectUri($this->base_url);
        $this->client =  $client;
    }

    /**
     * Get client instance of Goole\Client
     * 
     * @return Google\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Store contact to google contact using get request
     * @param $name string name
     * @param $phone string phone
     * @param $email string email
     * @param $token string access_token
     * @return GuzzleHttp\Client response
     */
    public function store($name = null, $phone = null, $email = null, $token = false)
    {
        if (!$token) {
            $get_token = $this->getToken();
            $token = $get_token->access_token;
            if (!$token) {
                $this->message = 'Token invalid, please renew token';
                return false;
            }
        }

        $http       = new Http();
        $endpoint   = 'https://people.googleapis.com/v1/people:createContact?personFields=names%2CphoneNumbers%2CemailAddresses&sources=READ_SOURCE_TYPE_CONTACT&prettyPrint=true';

        try {
            $response = $http->request('POST', $endpoint, [
                'body' => json_encode([
                    'names' => [
                        ['givenName' => $name]
                    ],
                    'phoneNumbers' => [
                        ['value' => $phone]
                    ],
                    'emailAddresses' => [
                        [
                            'displayName' => $name,
                            'value' => $email
                        ]
                    ]
                ]),
                'headers' => [
                    'content-type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json'
                ]
            ]);
            header('content-type:application/json');
            echo $response->getBody()->getContents();
            return true;
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }

    /**
     * Store token to file
     * 
     * @param string $code
     */
    public function storeToken($code)
    {
        $client = $this->client;
        $client->fetchAccessTokenWithAuthCode($code);
        $token  = $client->getAccessToken();
        $json   = json_encode($token, JSON_PRETTY_PRINT);
        file_put_contents(dirname(__FILE__) . '/' . $this->file_token, $json);
        return header('Location: ' . $this->base_url);
    }

    public function setFileToken($filename)
    {
        $this->file_token = $filename;
    }

    public function setBaseUrl($url)
    {
        $this->base_url = $url;
    }

    public function getToken()
    {
        if (!is_file(dirname(__FILE__) . '/' . $this->file_token)) {
            $this->message = 'File token not found';
            return false;
        }
        $file_data  = file_get_contents(dirname(__FILE__) . '/' . $this->file_token);
        $json = json_decode($file_data);
        return (isset($json->access_token)) ? $json : false;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function tokenInfo($access_token)
    {
        $http = new Http();
        try {
            $response = $http->request('GET', 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $access_token);
            if ($response->getStatusCode() != '200') {
                return false;
            }

            header('content-type:application/json');
            return json_decode($response->getBody());
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }

    public function storeJsonToken($token)
    {
        $json = json_encode($token, JSON_PRETTY_PRINT);
        try {
            file_put_contents(dirname(__FILE__) . '/' . $this->file_token, $json);
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }


    public function lists($access_token)
    {
        $http = new Http();
        try {
            $response = $http->request('GET', 'https://people.googleapis.com/v1/people/me/connections?personFields=names%2CphoneNumbers%2CemailAddresses', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Accept'        => 'application/json'
                ]
            ]);
            header('content-type:application/json');
            echo $response->getBody()->getContents();
            return true;
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }

    public function storeClientSecret($client_id = false, $client_secret = false)
    {

        if (!$client_id && !$client_secret) {
            $this->message = 'Provide an client id and client secret';
            return false;
        }

        $data = [
            "web" => [
                "client_id"     => $client_id,
                "client_secret" => $client_secret
            ]
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT);
        try {
            file_put_contents(dirname(__FILE__) . '/' . $this->client_secret, $json);
            return header('location:' . $this->base_url);
        } catch (Throwable $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }

    public function removeAuthorization()
    {
        $the_token      = dirname(__FILE__) . '/' . $this->file_token;
        $the_client     = dirname(__FILE__) . '/' . $this->client_secret;
        $the_filename   = dirname(__FILE__) . '/' . 'Filename.php';

        if (is_file($the_token) && is_file($the_client)) {
            try {
                unlink($the_token);
                unlink($the_client);

                // read filename
                $file = file_get_contents($the_filename);
                $file = str_replace('$prefix = "' . $this->prefix . '"', '$prefix = NULL', $file);
                file_put_contents($the_filename, $file);

                return header('location:' . $this->base_url);
            } catch (Throwable $e) {
                $this->message = $e->getMessage();
                return false;
            }
        }
    }
}
