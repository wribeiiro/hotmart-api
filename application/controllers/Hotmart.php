<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hotmart extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('hotmartclient');    
    }

    public function index() {
        $result = $this->hotmartclient->authorize();

        if ($result['code'] == 200) {
            echo '<pre>';
            print_r($result);

            $subscriptions = $this->hotmartclient->get('subscriptions', null, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->hotmartclient->getAccessToken()
            ]);

            print_r($subscriptions);

            exit;
        }
    }

    public function notifications() {

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        $this->hotmartclient->writeLog($email);

        $notify = sendMail(
            'dev@wribeiiro.com', 
            'welleh10@gmail.com', 
            null, 
            'Test of notify HotmartApi', 
            'Test of notify HotmartApi'
        );

        $status = ["message" => "error", "code" => 400];
        
        if ($notify)
            $status = ["message" => "OK", "code" => 200];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status['code'])
            ->set_output(json_encode(array(
                'message' => $status['message'],
                'status'  => $status['code']
            )));
    }
}