{extends file="parent:frontend/checkout/change_payment.tpl"}

{block name="frontend_checkout_payment_fieldset_input_radio"}
    {if $payment_mean.payolutionB2c.checkout === true && !$countryError && !$currencyError}
        <div class="js--payolution--payment" data-payment-method="b2c">
            {$smarty.block.parent}
    {elseif $payment_mean.payolutionInstallment.checkout && !$countryError && !$currencyError}
        <div class="js--payolution--payment" data-payment-method="installment">
            {$smarty.block.parent}
    {elseif $payment_mean.payolutionB2b.checkout && !$countryError && !$currencyError}
        <div class="js--payolution--payment" data-payment-method="b2b">
            {$smarty.block.parent}
    {elseif $payment_mean.payolutionElv.checkout && !$countryError && !$currencyError}
        <div class="js--payolution--payment" data-payment-method="elv">
            {$smarty.block.parent}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_checkout_payment_fieldset_template"}
    {$smarty.block.parent}
    {if $payment_mean.payolutionB2c.checkout === true && !$countryError && !$currencyError}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="b2c"}
        </div>
    {elseif $payment_mean.payolutionInstallment.checkout && !$countryError && !$currencyError}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="installment"}
        </div>
    {elseif $payment_mean.payolutionB2b.checkout && !$countryError && !$currencyError}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="b2b"}
        </div>
    {elseif $payment_mean.payolutionElv.checkout && !$countryError && !$currencyError}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="elv"}
        </div>
    {/if}
{/block}
