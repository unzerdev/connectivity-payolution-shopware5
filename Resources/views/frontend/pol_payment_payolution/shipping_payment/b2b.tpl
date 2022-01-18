{namespace name="frontend/pol_payment_payolution/main"}

<!-- company mode -->
<div class="block-group payolution-form--company-type">
    <label for="payolution-form--company-type-select">
        {s name='form/company/field/type/label'}Organisationsform:{/s}
    </label>
    <div class="block">
        <select class="company-type--select" id="payolution-form--company-type-select" name="payolution[{$payolutionPaymentMethod}][companyType]">
            <option value="">{s name="form/company/field/type/option/none"}Bitte wählen{/s}</option>
            <option value="company">{s name="form/company/field/type/option/company"}Unternehmen{/s}</option>
            <option value="soletrader">{s name="form/company/field/type/option/soletrader"}Einzelunternehmen{/s}</option>
            <option value="authority">{s name="form/company/field/type/option/authority"}Behörde{/s}</option>
            <option value="society">{s name="form/company/field/type/option/society"}Verein{/s}</option>
            <option value="other">{s name="form/company/field/type/option/others"}Sonstiges{/s}</option>
        </select>
    </div>
</div>

<div class="payolution-b2b--company-type-selected-container is--hidden">
    <!-- company name -->
    <div class="block-group">
        <label for="payolution-b2b-form--company-input">
            {s name='form/company/field/company/label'}Firma:{/s}
        </label>
        <div class="block">
            <input id="payolution-b2b-form--company-input" type="text" name="payolution[{$payolutionPaymentMethod}][company]"
                   value="{$sUserData.billingaddress.company}"/>
        </div>
    </div>

    <!-- VatId -->
    <div class="block-group">
        <label id="payolution-b2b-form--vatnumber-label" for="payolution-b2b-form--vatnumber-input">
            {s name='form/company/field/vatid/label'}Ust-ID:{/s}
        </label>
        <div class="block">
            <input id="payolution-b2b-form--vatnumber-input" type="text" name="payolution[{$payolutionPaymentMethod}][vatid]"
                   value="{$sUserData.billingaddress.ustid}">
        </div>
    </div>

    <div class="payolution-b2b--sole-trader-only-container is--hidden">
        <!-- Bearer first and lastname -->
        <div class="block-group">
            <label for="payolution-b2b-form--bearer-firstname-input">
                {s name='form/company/field/firstname/label'}Vorname:{/s}
            </label>
            <div class="block">
                <input id="payolution-b2b-form--bearer-firstname-input" type="text" name="payolution[{$payolutionPaymentMethod}][firstname]"
                       value="{$sUserData.billingaddress.firstname}"/>
            </div>
        </div>

        <div class="block-group">
            <label for="payolution-b2b-form--bearer-lastname-input">
                {s name='form/company/field/lastname/label'}Nachname:{/s}
            </label>
            <div class="block">
                <input id="payolution-b2b-form--bearer-lastname-input" type="text" name="payolution[{$payolutionPaymentMethod}][lastname]"
                       value="{$sUserData.billingaddress.lastname}"/>
            </div>
        </div>

        <!-- Bearer Birthday -->
        {include file="frontend/pol_payment_payolution/forms/birthday.tpl"}
    </div>

    {if $sUserData.additional.country.countryiso == 'NL'}
        {include file="frontend/pol_payment_payolution/forms/phone.tpl"}
    {/if}

    {include file="frontend/pol_payment_payolution/forms/privacy-check.tpl"}
</div>