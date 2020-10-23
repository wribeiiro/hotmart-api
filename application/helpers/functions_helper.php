<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function response($data) {
	ini_set('max_execution_time', 0);
	
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Content-Type');
	header('Content-Type: application/json');
	echo json_encode($data);
	exit;
}	

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param string $filename
 * @return void
 */
function saveLog($data, $filename = "log") {
	$appendText = date('d/m/Y H:i:s') . " | " . $data;
	$fp			= fopen(APPPATH . "logs/{$filename}.txt", "a");

	fwrite($fp, $appendText . "\n");
	fclose($fp);
}


/**
 * Fast debug
 *
 * @param string $var
 * @param boolean $exit
 * @return void
 */
function pre($var, $exit = true) {
    echo '<pre>';
	print_r($var);
    echo '</pre>';

    if ($exit)
        exit;
}

function sendMail(string $from, string $to, string $bcc = null, string $subject, string $message) {
	$ci = &get_instance();

	$ci->load->library('email', [
		'charset'  => 'uft-8',
		'mailtype' => 'html'
	]);

	$ci->email->from($from, 'Notificação Hotmart');
	$ci->email->to($to);
	
	if ($bcc) 
		$ci->email->bcc($to);

	$ci->email->subject($subject);
	$ci->email->message($message);

	return $ci->email->send();
}
