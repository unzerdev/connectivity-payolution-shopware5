<?php
namespace Payolution\Request\Installment\Cl;

use Payolution\Config\AbstractConfig;

/**
 * Class CreateRequest
 * @package Payolution\Request
 */
class GetJsLibrary
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
     * getting Cl Js Library
     *
     * @param $path
     */
    public function getLibrary($path)
    {
        if($this->payolutionConfig->isTestmode()) {
            $url = 'https://test-payment.payolution.com/payolution-payment/infoport/installments/generatejs?id=test-installment';
        } else {
            $url = 'https://payment.payolution.com/payolution-payment/infoport/installments/generatejs?id='.$this->payolutionConfig->getInstallmentPayolutionUser();
        }

        $dirName = dirname($path);
        if (!is_dir($dirName))
        {
            mkdir($dirName, 0755, true);
        }

        $file = fopen($path,'w');
        $content = file_get_contents($url);
        fwrite($file,$content);
        fclose($file);
    }
}
