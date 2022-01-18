{namespace name="frontend/pol_payment_payolution/main"}

{if $payolutionDisplayNoneInstallment != 1}
    <div class="block-group">
        <div class="block payolution-installment--column payolution-installment--column1">
            {include file="frontend/pol_payment_payolution/shipping_payment/installment/container-left.tpl"}
        </div>
        <div class="block payolution-installment--column payolution-installment--column2 is--disabled">
            {include file="frontend/pol_payment_payolution/shipping_payment/installment/container-middle.tpl"}
        </div>
        <div class="block payolution-installment--column payolution-installment--column2-error is--hidden">
            {include file="frontend/pol_payment_payolution/shipping_payment/installment/container-middle-error.tpl"}
        </div>
        <div class="block payolution-installment--column payolution-installment--column3 is--disabled">
            {include file="frontend/pol_payment_payolution/shipping_payment/installment/container-right.tpl"}
        </div>
    </div>
{/if}
