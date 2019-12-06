<?php
/**
 * Created by PhpStorm.
 * User: DESKTOP-1
 * Date: 06.12.2019
 * Time: 12:22
 */

namespace omen666\epayments\wallet_api;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Payment
{
    private $authorisation;

    private $currency = 'USD';

    private $client;

    /**
     * PaymentExecution constructor.
     *
     * @param Authorisation $authorisation
     */
    public function __construct(Authorisation $authorisation)
    {
        $this->authorisation = $authorisation;
        $client              = new Client(['base_uri' => $this->authorisation->getUrl()]);
    }

    /**
     * @param $amount
     * @param $from_identity
     * @param $to_identity
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payEWalletToEWallet($amount, $from_identity, $to_identity)
    {
        $payments = [
            'payments' => [
                'from'       => [
                    'provider' => 'eWallet',
                    'currency' => $this->currency,
                    'amount'   => $amount,
                    'identity' => $from_identity,
                ],
                'to'         => [
                    'provider' => 'eWallet',
                    'currency' => $this->currency,
                    'amount'   => $amount,
                    'identity' => $to_identity,
                ],
                'details'    => '',
                'externalId' => '',
                'quote'      => '',
                'metadata'   => '',
            ],
        ];

        return $this->sendPostRequest($payments);
    }

    /**
     * @param array $payments
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    private function sendPostRequest(array $payments)
    {
        $params = [
            'headers' => $this->authorisation->getAuthorizationHeader(),
            'json'    => json_encode($payments),
        ];

        $request  = new Request('POST', $this->authorisation->getUrl() . '/v2/payments', $params);
        $response = $this->client->send($request, ['timeout' => 2]);

        if ($response->getStatusCode() == 400) {
            throw new Exception('error');
        }

        return $response;
    }

    public function getStatus($externalId)
    {
        return false;
    }
}