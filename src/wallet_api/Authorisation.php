<?php
/**
 * Created by PhpStorm.
 * User: omen666
 * Date: 06.12.2019
 * Time: 11:46
 */

namespace omen666\epayments\wallet_api;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Authorisation
{

    private $url = 'https://api.epayments.com/';

    private $test_url = 'https://api.sandbox.epayments.com/';

    private $test_mode;

    private $accept_language = 'ru-RU';

    private $partner_id;

    private $partner_secret;

    private $access_token;

    /**
     * Authorisation constructor.
     *
     * @param      $partner_id
     * @param      $partner_secret
     * @param bool $test_mode
     */
    public function __construct($partner_id, $partner_secret, $test_mode = true)
    {
        $this->partner_id     = $partner_id;
        $this->partner_secret = $partner_secret;
        $this->test_mode      = $test_mode;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAccessToken()
    {
        $params   = [
            'headers' => [
                'accept'         => 'application/json',
                'Content-Type'   => 'application/x-www-form-urlencoded',
                'grant_type'     => 'partner',
                'partner_id'     => $this->partner_id,
                'partner_secret' => $this->partner_secret,
            ],
        ];
        $client   = new Client(['base_uri' => $this->getUrl()]);
        $request  = new Request('POST', $this->getUrl() . 'token', $params);
        $response = $client->send($request, ['timeout' => 2]);
        if ($response->getStatusCode() == 400) {
            throw new Exception('error');
        }

        $this->access_token = $response;
    }

    /**
     * @return array
     */
    public function getAuthorizationHeader()
    {
        return [
            'Authorization'   => 'Bearer ' . $this->access_token,
            'Content-type'    => 'application/json',
            'Accept-Language' => $this->accept_language,
        ];
    }

    /**
     * @param $accept_language
     */
    public function setAcceptLanguage($accept_language)
    {
        $this->accept_language = $accept_language;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->test_mode) {
            return $this->test_url;
        }

        return $this->url;
    }
}