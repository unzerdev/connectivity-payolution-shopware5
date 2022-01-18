<?php
namespace Payolution\Request\Installment;

use Payolution\Config\AbstractConfig;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Class GetDocument
 * @package Payolution\Request
 */
class GetDocument
{

    private $payolutionConfig;
    /**
     * class constructor
     *
     * @param AbstractConfig $payolutionConfig
     */
    public function __construct(AbstractConfig $payolutionConfig)
    {
        $this->payolutionConfig = $payolutionConfig;
    }

    /**
     * getting document from payolution
     *
     * @param $url
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     */
    public function doRequest($url)
    {
        $client = new GuzzleClient();

        /**
         * get URL from Shopware Config
         */
        $request = $client->get($url,
            array(
                'auth' =>
                    array(
                        $this->payolutionConfig->getInstallmentPayolutionUser(),
                        $this->payolutionConfig->getInstallmentPayolutionPassword()
                    )
            )
        );

        $return = $request->getBody()->getContents();

        return $return;
    }
}
