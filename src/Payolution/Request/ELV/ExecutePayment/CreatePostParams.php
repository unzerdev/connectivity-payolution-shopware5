<?php
namespace Payolution\Request\ELV\ExecutePayment;

use Payolution\Config\AbstractConfig;
use Payolution\Request\ELV\PreCheck\CreatePostParams as ELVPreCheckCreatePostParam;

/**
 * Class Payolution_Request_ELV_ExecutePayment_CreatePostParams
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
     * @param $payolutionConfig
     * @return array
     */
    public static function createParams(AbstractELVExecutePayment $data, AbstractConfig $payolutionConfig)
    {
        $post_data = ELVPreCheckCreatePostParam::createParams($data, $payolutionConfig);

        $post_data['CRITERION.PAYOLUTION_PRE_CHECK_ID'] = $data->getCRITERIONPAYOLUTIONPRECHECKID();
        $post_data['CRITERION.PAYOLUTION_PRE_CHECK'] = false;

        return $post_data;
    }
}
