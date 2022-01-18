{namespace name="frontend/pol_payment_payolution/main"}

{if !$countryError}
    {include file="frontend/pol_payment_payolution/forms/bank-details.tpl"}

    {include file="frontend/pol_payment_payolution/forms/birthday.tpl"}

    {include file="frontend/pol_payment_payolution/forms/phone.tpl"}

    {include file="frontend/pol_payment_payolution/forms/privacy-check.tpl"}

    {include file="frontend/pol_payment_payolution/forms/sepa-mandate.tpl"}
{/if}
