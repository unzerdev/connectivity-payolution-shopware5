{extends file='parent:frontend/register/payment_fieldset.tpl'}

{block name="frontend_register_payment_fieldset_input"}
    {if $payment_mean.payolutionB2c.checkout === true}
        <div class="js--payolution--payment" data-payment-method="b2c">
            {$smarty.block.parent}
        </div>
    {elseif $payment_mean.payolutionB2b.checkout === true}
        <div class="js--payolution--payment" data-payment-method="b2b">
            {$smarty.block.parent}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_register_payment_fieldset_template" append}
    {if $payment_mean.payolutionB2c.checkout === true}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="b2c" isAccount=true}
    {elseif $payment_mean.payolutionB2b.checkout === true}
        {include file="frontend/pol_payment_payolution/payment-method.tpl" payolutionPaymentMethod="b2b" isAccount=true}
    {/if}
{/block}
