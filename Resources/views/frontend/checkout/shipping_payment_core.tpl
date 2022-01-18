{extends file='parent:frontend/checkout/shipping_payment_core.tpl'}

{block name="frontend_checkout_shipping_payment_core_buttons"}
    <div class='payolution-payment--submit-button {if $countryError}is--hidden{/if}'>
        {$smarty.block.parent}
    </div>
{/block}
