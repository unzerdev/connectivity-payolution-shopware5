{extends file='parent:frontend/account/payment.tpl'}

{block name="frontend_account_payment_action_button_send"}
    <div class='payolution-payment--submit-button'>
        {$smarty.block.parent}
    </div>
{/block}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    {include file="frontend/pol_payment_payolution/shipping_payment/translations.tpl"}
{/block}
