<?php

namespace Bap\GmoPayment\Gateway\Service;

use Bap\GmoPayment\Enum\GmoErrType;
use Bap\GmoPayment\Gateway\Config\Config;
use Bap\GmoPayment\Gateway\Exception\GmoPaymentException;
use Magento\Framework\HTTP\ClientFactory;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

class Client
{
    private $client;

    public function __construct(private ClientFactory $clientFactory, private Config $config)
    {
        $this->client = $clientFactory->create();
    }

    public function sendPostRequest(string $endpoint, array $data)
    {
        $url = $this->buildUrl($endpoint);

        $this->client->addHeader('Content-Type', 'application/json');
        $this->client->post($url, json_encode($data));
        $response = $this->client->getBody();

        $outputs = $this->parseResponse($response);
        if ($this->client->getStatus() !== Response::HTTP_OK) {
            throw new GmoPaymentException(__('Bad Request'), $outputs);
        }

        return $outputs;
    }

    private function buildUrl(string $endpoint)
    {
        $baseUri = $this->config->getApiBaseUri();
        if (empty($baseUri)) {
            throw new InvalidArgumentException(__('Base Uri cannot be empty.'));
        }

        return $baseUri . '/' . ltrim($endpoint, '/');
    }

    private function parseResponse(string $response)
    {
        $outputs = json_decode($response, true);

        $errors = [];
        foreach ($outputs as $output) {
            if (is_array($output) && array_key_exists('errCode', $output)) {
                $code = $output['errCode'];
                $info = $output['errInfo'];

                $errors[] = [
                    'code' => $code,
                    'info' => $info,
                    'message' => GmoErrType::getErrorMessage($info),
                ];
            }
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        };

        return [
            'success' => true,
            'data' => $outputs,
        ];
    }
}
