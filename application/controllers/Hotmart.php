<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'libraries/HotmartApi.php';

class Hotmart extends CI_Controller {
    private $hotmart;

    public function __construct() {
        parent::__construct();

        $this->hotmart = new HotmartApi(
            '1d88dc15-4eaf-49ae-bd6c-53e574f22f57', 
            'bde0d145-6883-4007-a065-99e846f7b663', 
            'MWQ4OGRjMTUtNGVhZi00OWFlLWJkNmMtNTNlNTc0ZjIyZjU3OmJkZTBkMTQ1LTY4ODMtNDAwNy1hMDY1LTk5ZTg0NmY3YjY2Mw=='
        );
    }

    public function index() {
        
        $result = $this->hotmart->authorize();

        if ($result['code'] == 200) {
            echo '<pre>';
            print_r($result);

            $subscriptions = $this->hotmart->get('subscriptions', null, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->hotmart->getAccessToken()
            ]);

            print_r($subscriptions);

            exit;
        }

    }

    public function notifications() {

    }
}