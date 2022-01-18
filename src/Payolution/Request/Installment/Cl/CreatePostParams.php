<?php
namespace Payolution\Request\Installment\Cl;

use Payolution\Config\AbstractConfig;

/**
 * Class Payolution_Request_Installment_Cl_CreatePostParams
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
     * @return string
     */
    public function createParams(AbstractInstallmentCl $data)
    {
        $xml = new \DOMDocument("1.0","UTF-8");
        $xml->xmlStandalone = true;

        $request = $xml->createElement('Request');
        $attribute = $xml->createAttribute('version');
        $attribute->value = $data->getREQUESTVERSION();
        $request->appendChild($attribute);

        $sender = $xml->createElement('Sender', $this->payolutionConfig->getSender());
        $transaction = $xml->createElement('Transaction');
        $sender->nodeValue = 'PSP Name';
        if($this->payolutionConfig->isTestmode()) {
            $attribute = $xml->createAttribute('mode');
            $attribute->value = 'TEST';
            $transaction->appendChild($attribute);
            $attribute = $xml->createAttribute('channel');
            $attribute->value = $this->payolutionConfig->getInstallmentPayolutionUser();
            $transaction->appendChild($attribute);
        } else {
            $attribute = $xml->createAttribute('mode');
            $attribute->value = 'LIVE';
            $transaction->appendChild($attribute);
            $attribute = $xml->createAttribute('channel');
            $attribute->value = $this->payolutionConfig->getInstallmentPayolutionUser();
            $transaction->appendChild($attribute);
        }

        $identification = $xml->createElement('Identification');
        $identification->appendChild(
            $xml->createElement('TransactionID',$data->getIDENTIFICATIONTRANSACTIONID())
        );
        $transaction->appendChild($identification);

        $payment = $xml->createElement('Payment');
        $payment->appendChild(
            $xml->createElement('OperationType', 'CALCULATION')
        );
        $payment->appendChild(
            $xml->createElement('PaymentType', 'INSTALLMENT')
        );

        $presentation = $xml->createElement('Presentation');
        $presentation->appendChild(
            $xml->createElement('Currency', $data->getPRESENTATIONCURRENCY())
        );
        $presentation->appendChild(
            $xml->createElement('Usage', $data->getPRESENTATIONUSAGE())
        );
        $presentation->appendChild(
            $xml->createElement('Amount', $data->getPRESENTATIONAMOUNT())
        );
        $presentation->appendChild(
            $xml->createElement('VAT', $data->getPRESENTATIONVAT())
        );
        $payment->appendChild($presentation);
        $transaction->appendChild($payment);

        $request->appendChild($sender);
        $request->appendChild($transaction);
        $xml->appendChild($request);

        return $xml->saveXML();
    }
}
