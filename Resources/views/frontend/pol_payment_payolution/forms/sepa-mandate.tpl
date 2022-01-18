{namespace name="frontend/pol_payment_payolution/main"}

<div class="block-group payolution--sepa-mandate">
    <div class="block payolution-sepa-mandate--checkbox">
        <input type="checkbox" name="payolution[{$payolutionPaymentMethod}][sepaMandate]" id="payolution-sepa-mandate--checkbox{$payment_mean.id}"/>
    </div>
    <div class="block payolution-sepa-mandate--label">
        <a href="https://payment.payolution.com/payolution-payment/infoport/sepa/mandate.pdf?lang={$payolutionLocale}">
            {s name='form/sepa-mandate/label'}{/s}
        </a>
    </div>
</div>
