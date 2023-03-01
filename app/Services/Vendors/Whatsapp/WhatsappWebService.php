<?php

namespace App\Services\Vendors\Whatsapp;
use Log;

class WhatsappWebService
{
    function __construct() {
        $this->base_url = env('WHATSAPP_SERVER_URL');
    }

    public function connect($data)
    {
        $url = $this->base_url . '/instance/qrCode';
        $resp = send_guzzle_req($url, $data);
        $response = json_decode($resp->getBody(), true);
        return $response;
    }

    public function send_message($data)
    {
        $url = $this->base_url . '/messages/chat';
        $resp = send_guzzle_req($url, $data);
        $response = json_decode($resp->getBody(), true);
        return $response;
    }

    public function logout($data)
    {
        $url = $this->base_url . '/instance/logout';
        $resp = send_guzzle_req($url, $data);
        $response = json_decode($resp->getBody(), true);
        return $response;
    }

    public function get_sessions($data)
    {
        $url = $this->base_url . '/instance/get_sessions';
        $resp = send_guzzle_req($url, $data);
        $response = json_decode($resp->getBody(), true);
        return $response;
    }

    public function check_session_status($data)
    {
        $url = $this->base_url . '/check_session';
        $resp = send_guzzle_req($url, $data);
        $response = json_decode($resp->getBody(), true);
        return $response;
    }
}
