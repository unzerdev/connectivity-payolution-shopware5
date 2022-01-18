<?php
namespace Payolution\Request\Installment\ExecutePayment;

use Payolution\Config\AbstractConfig;
use Payolution\Request\ExecutePayment\CreatePostParams as ExecutePaymentCreatePostParam;

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
    public function createParams(AbstractInstallmentExecutePayment $data)
    {
        $post_data = ExecutePaymentCreatePostParam::createParams($data, $this->payolutionConfig);

        $post_data['TRANSACTION.CHANNEL'] = $this->payolutionConfig->getChannelInstallment();

        $post_data['CRITERION.PAYOLUTION_CALCULATION_ID'] = $data->getCRITERIONPAYOLUTIONCALCULATIONID();
        $post_data['CRITERION.PAYOLUTION_INSTALLMENT_AMOUNT'] = $data->getCRITERIONPAYOLUTIONINSTALLMENTAMOUNT();
        $post_data['CRITERION.PAYOLUTION_DURATION'] = $data->getCRITERIONPAYOLUTIONDURATION();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_HOLDER'] = $data->getCRITERIONPAYOLUTIONACCOUNTHOLDER();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_COUNTRY'] = $data->getCRITERIONPAYOLUTIONACCOUNTCOUNTRY();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_BIC'] = $data->getCRITERIONPAYOLUTIONACCOUNTBIC();
        $post_data['CRITERION.PAYOLUTION_ACCOUNT_IBAN'] = $data->getCRITERIONPAYOLUTIONACCOUNTIBAN();

        return $post_data;
    }
}
