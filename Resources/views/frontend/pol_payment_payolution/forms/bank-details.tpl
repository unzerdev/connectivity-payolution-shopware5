{namespace name="frontend/pol_payment_payolution/main"}

<div class="block-group payolution-form--bank-details">
    <div class="block">
        <label>
            {s name='form/bank-details/heading'}Ihre Bankverbindung{/s}
        </label>

        <div class="block-group bank-details--holder">
            <div class="block">
                <input type="text"
                       class="bank-details--holder-field"
                       name="payolution[{$payolutionPaymentMethod}][holder]"
                       placeholder="{s name='form/bank-details/field/holder/label'}Kontoinhaber:{/s}"
                       value="{if $payolutionElv.accountHolder}{$payolutionElv.accountHolder}{else}{$sUserData.billingaddress.firstname} {$sUserData.billingaddress.lastname}{/if}" />
            </div>
        </div>

        <div class="block-group bank-details--iban">
            <div class="block">
                <input type="text"
                       class="bank-details--iban-field"
                       name="payolution[{$payolutionPaymentMethod}][iban]"
                       placeholder="{s name='form/bank-details/field/iban/label'}IBAN:{/s}"
                       value="{$payolutionElv.accountIban}" />
            </div>
        </div>

        <div class="block-group bank-details--bic">
            <div class="block">
                <input type="text"
                       class="bank-details--bic-field"
                       name="payolution[{$payolutionPaymentMethod}][bic]"
                       placeholder="{s name='form/bank-details/field/bic/label'}BIC:{/s}"
                       value="{$payolutionElv.accountBic}" />
            </div>
        </div>

    </div>
</div>
