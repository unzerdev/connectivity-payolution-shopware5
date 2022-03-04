<?php
namespace Payolution\Request\Installment;

use Payolution\Client\ClientInterface;
use Payolution\Config\AbstractConfig;
use GuzzleHttp\Client as GuzzleClient;
use Payolution\Exception\ClientException;
use Payolution\Request\Model\RequestOptions;
use Payolution\Request\Request;
use Payolution\Request\RequestEnums;

/**
 * Class GetDocument
 * @package Payolution\Request
 */
class GetDocument
{
    /** @var AbstractConfig */
    private $payolutionConfig;

    /** @var ClientInterface */
    private $client;

    public function __construct(AbstractConfig $payolutionConfig, ClientInterface $client)
    {
        $this->payolutionConfig = $payolutionConfig;
        $this->client = $client;
    }

    public function doRequest($url): string
    {
        $requestOptions = new RequestOptions('get', RequestEnums::REQUEST_TYPE, $url);
        $request = new Request([
            'auth' => [
                $this->payolutionConfig->getInstallmentPayolutionUser(),
                $this->payolutionConfig->getInstallmentPayolutionPassword()
            ]
        ], $requestOptions);

        try {
            $response = $this->client->executeRequest($request);
        } catch (ClientException $e) {
            return '';
        }

        return $response->getData();
    }
}
