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
     * @param string $client_id
     * @param string $client_secret
     * @param string $token
     */
    public function __construct(string $client_id, string $client_secret, string $token) {
        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
        $this->token         = $token;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return void
     */
    public function setEndpoint(string $url): void {
        $this->sub_endpoint = self::$endpoint.$url;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getEndpoint(): string {
        return $this->sub_endpoint;
    }

    /**
     * Undocumented function
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken): void {
        $this->access_token = $accessToken;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAccessToken(): string {
        return $this->access_token;
    }

    /**
     * Undocumented function
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
     * Undocumented function
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
     * Undocumented function
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
}