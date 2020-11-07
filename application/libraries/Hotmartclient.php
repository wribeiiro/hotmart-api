<?php

class HotmartClient {

    protected static $endpoint = "https://sandbox.hotmart.com/payments/api/v1/";
    protected static $sandbox_endpoint = "https://sandbox.hotmart.com/payments/api/v1/";
    protected static $authpoint = "https://api-sec-vlc.hotmart.com/security/oauth/";

    protected $client_id;
    protected $client_secret;
    protected $token;
    protected $access_token;
    protected $sub_endpoint;
    private $CI;

    public function __construct() {
        $this->CI = get_instance();
        $this->CI->config->load('hotmart');

        $this->client_id     = $this->CI->config->item('client_id');
        $this->client_secret = $this->CI->config->item('client_secret');
        $this->token         = $this->CI->config->item('token');
    }

    /**
     * setEndpoint
     *
     * @param string $url
     * @return void
     */
    public function setEndpoint(string $url): void {
        $this->sub_endpoint = self::$endpoint.$url;
    }

    /**
     * getEndpoint
     *
     * @return string
     */
    public function getEndpoint(): string {
        return $this->sub_endpoint;
    }

    /**
     * setAccessToken
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken): void {
        $this->access_token = $accessToken;
    }

    /**
     * getAccessToken
     *
     * @return string
     */
    public function getAccessToken(): string {
        return $this->access_token;
    }

    /**
     * execute
     *
     * @param string $url
     * @param string $type
     * @param array $fields
     * @param array $headers
     * @return array
     */
    public function execute(string $url, string $type = 'POST', array $fields = null, array $headers = ['Content-Type: application/json']): array {
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
     * get
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return array
     */
    public function get(string $url, array $params = null, array $headers = null): array {
        $this->setEndpoint($url);
        
        $result = $this->execute($this->sub_endpoint, 'GET', $params, $headers);

        return $result;
    }

    /**
     * authorize
     *
     * @return array
     */
    public function authorize(): array {
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

    public function writeLog(string $input): void {
        $fp	= fopen("log_.txt", "w");

	    fwrite($fp, $input. "\n");
        fclose($fp);
    }
}