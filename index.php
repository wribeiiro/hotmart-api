<?php

class HotmartApi {

    protected static $endpoint = "https://sandbox.hotmart.com/payments/api/v1/";
    protected static $authpoint = "https://api-sec-vlc.hotmart.com/security/oauth/";

    protected $client_id;
    protected $client_secret;
    protected $token;
    protected $access_token;
    protected $sub_endpoint;

    /**
     * Undocumented function
     *
     * @param [type] $client_id
     * @param [type] $client_secret
     * @param [type] $token
     */
    public function __construct($client_id, $client_secret, $token) {
        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
        $this->token         = $token;
    }

    /**
     * Undocumented function
     *
     * @param [type] $url
     * @return void
     */
    public function setEndpoint($url) {
        $this->sub_endpoint = self::$endpoint.$url;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getEndpoint() {
        return $this->sub_endpoint;
    }

    /**
     * Undocumented function
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken) {
        $this->access_token = $accessToken;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * Undocumented function
     *
     * @param [type] $url
     * @param string $type
     * @param [type] $fields
     * @param array $headers
     * @return void
     */
    public function execute($url, $type = 'POST', $fields = null, $headers = array('Content-Type: application/json')) {
        $params = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $type
        );

        if ($fields !== null) 
            $params[CURLOPT_POSTFIELDS] = $fields;

        if ($headers)         
            $params[CURLOPT_HTTPHEADER] = $headers;

        $ch = curl_init();
        curl_setopt_array($ch, $params);
        
        $response["body"] = json_decode(curl_exec($ch));
        $response["code"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $response;
    }

    /**
     * Undocumented function
     *
     * @param [type] $url
     * @param [type] $params
     * @param [type] $headers
     * @return void
     */
    public function get($url, $params = null, $headers = null) {
        $this->setEndpoint($url);
        
        $result = $this->execute($this->sub_endpoint, 'GET', $params, $headers);

        return $result;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function authorize() {
        $auth = $this->execute(self::$authpoint."token?grant_type=client_credentials&client_id=$this->client_id&client_secret=$this->client_secret", 'POST', null, [
            "Content-Type: application/json",
            "Authorization: Basic $this->token"
        ]);

        if ($auth["code"] == 200) {             
            $this->setAccessToken($auth["body"]->access_token);

            return $auth;
        } 

        return $auth;
    }
}

$api = new HotmartApi(
    '1d88dc15-4eaf-49ae-bd6c-53e574f22f57', 
    'bde0d145-6883-4007-a065-99e846f7b663', 
    'MWQ4OGRjMTUtNGVhZi00OWFlLWJkNmMtNTNlNTc0ZjIyZjU3OmJkZTBkMTQ1LTY4ODMtNDAwNy1hMDY1LTk5ZTg0NmY3YjY2Mw=='
);

$result = $api->authorize();

if ($result['code'] == 200) {
    echo '<pre>';
    print_r($result);

    $subscriptions = $api->get('subscriptions', null, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $api->getAccessToken()
    ]);

    print_r($subscriptions);

    exit;
}

