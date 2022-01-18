<?php
namespace Payolution\Request\ELV\PreCheck;

use Payolution\Config\AbstractConfig;
use Payolution\Request\PreCheck\CreatePostParams as PreCheckCreatePostParam;

/**
 * Class Payolution_Request_ELV_PreCheck_CreatePostParams
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
    public static function createParams(AbstractELVPreCheck $data, AbstractConfig $payolutionConfig)
    {
        $post_data = PreCheckCreatePostParam::createParams($data, $payolutionConfig);

        $post_data['CRITERION.PAYOLUTION_ACCOUNT_HOLDER'] = $data->getCRITERIONPAYOLUTIONACCOUNTHOLDER();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_COUNTRY'] = $data->getCRITERIONPAYOLUTIONACCOUNTCOUNTRY();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_BIC'] = $data->getCRITERIONPAYOLUTIONACCOUNTBIC();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_IBAN'] = $data->getCRITERIONPAYOLUTIONACCOUNTIBAN();
        $post_data['CRITERION.PAYOLUTION_SHIPPING_COMPANY'] = $data->getCRITERIONPAYOLUTIONSHIPPINGCOMPANY();
        $post_data['CRITERION.PAYOLUTION_CUSTOMER_NUMBER'] = $data->getCRITERIONPAYOLUTIONCUSTOMERNUMBER();
        $post_data['TRANSACTION.CHANNEL'] = $payolutionConfig->getChannelElv();

        return $post_data;
    }
}
