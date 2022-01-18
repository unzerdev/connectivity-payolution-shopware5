<?php
namespace Payolution\Request\Installment\PreCheck;

use Payolution\Config\AbstractConfig;
use Payolution\Request\PreCheck\CreatePostParams as PreCheckCreatePostParam;

/**
 * Class Payolution_Request_Installment_PreCheck_CreatePostParams
 */
class CreatePostParams
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
     * create Post Parameter for Payment
     *
     * @param $data
     * @return array
     */
    public function createParams(AbstractInstallmentPreCheck $data)
    {
        $post_data = PreCheckCreatePostParam::createParams($data, $this->payolutionConfig);

        $post_data['CRITERION.PAYOLUTION_CALCULATION_ID'] = $data->getCRITERIONPAYOLUTIONCALCULATIONID();

        return $post_data;
    }
}
