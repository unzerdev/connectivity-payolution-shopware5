{namespace name="frontend/pol_payment_payolution/main"}

{if !$countryError && $sUserData.additional.country.countryiso === 'NL'}
    <div class="register--content">
        <div class="register--phone">
            <label class="phone--label">{s name='form/phone/field/phone/label'}Telefonnummer:{/s}</label>
            <input type="text"
                   name="payolution[{$payolutionPaymentMethod}][phone]"
                   value="{$sUserData.billingaddress.phone}"
                   class="register--field" />
        </div>
    </div>
{/if}
