<?php
namespace Payolution\Request\PreCheck;

use Payolution\Config\AbstractConfig;
use Payolution\Request\ExecutePayment\CreatePostParams as ExecutePaymentCreatePostParam;

/**
 * Class Payolution_Request_PreCheck_CreatePostParams
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
    public static function createParams(AbstractPreCheckPayment $data, AbstractConfig $payolutionConfig)
    {
        $post_data = ExecutePaymentCreatePostParam::createParams($data, $payolutionConfig);

        $post_data['CRITERION.PAYOLUTION_PRE_CHECK'] = $data->getCRITERIONPAYOLUTIONPRECHECK();
        $post_data['TRANSACTION.CHANNEL'] = $payolutionConfig->getChannelPrecheck();

        return $post_data;
    }
}
