{namespace name="frontend/pol_payment_payolution/main"}

<div class="payolution--panel">
    <div class="panel--heading">
        {s name='installment/right/heading'}3. &Uuml;bersicht und Bankverbindung{/s}
    </div>

    <div class="panel--content payolution-installment--overview">
        <div class="payolution-installment--overview-details">
            <div class="block-group">
                <div class="block column--left">
                    {s name='installment/overview/installment-count'}Anzahl der Raten:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--duration"></span>
                </div>
            </div>

            <div class="block-group spacing--top">
                <div class="block column--left">
                    {s name='installment/overview/finance-amount'}Finanzierungsbetrag:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--original-amount"></span>
                </div>
            </div>

            <div class="block-group">
                <div class="block column--left">
                    {s name='installment/overview/amount-total'}Gesamtsumme:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--total-amount"></span>
                </div>
            </div>

            <div class="block-group spacing--top">
                <div class="block column--left">
                    {s name='installment/overview/nominal-zins'}Nominalzins:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--interest-rate"></span>{s name='percent-sign'}%{/s}
                </div>
            </div>

            <div class="block-group">
                <div class="block column--left">
                    {s name='installment/overview/effective-zins'}Effektivzins:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--effective-interest-rate"></span>{s name='percent-sign'}%{/s}
                </div>
            </div>

            <div class="block-group spacing--top installment-overview--rate-amount">
                <div class="block column--left">
                    {s name='installment/overview/installment-monthly'}Monatliche Raten:{/s}
                </div>
                <div class="block column--right">
                    <span class="placeholder--rate-amount"></span>
                </div>
            </div>
        </div>

        {if $sUserData.additional.country.countryiso != 'CH' && $sUserData.additional.country.iso3 != 'CHE'}
            <div class="payolution-installment--overview-bank payolution-form--bank-details">
                <h2 class="overview-bank--heading">{s name='form/bank-details/heading'}Ihre Bankverbindung{/s}</h2>
                <span>{s name='form/bank-details/description'}Bequeme Lastschriften f&uuml;r Raten, jederzeit widerrufbar.{/s}</span>

                <div class="block-group bank-details--holder">
                    <div class="block column--left">
                        {s name='form/bank-details/field/holder/label'}Kontoinhaber:{/s}
                    </div>
                    <div class="block column--right">
                        <input type="text" name="payolution[{$payolutionPaymentMethod}][holder]" class="bank-details--holder-field"/>
                    </div>
                </div>
                <div class="block-group bank-details--iban">
                    <div class="block column--left">
                        {s name='form/bank-details/field/iban/label'}IBAN:{/s}
                    </div>
                    <div class="block column--right">
                        <input type="text" name="payolution[{$payolutionPaymentMethod}][iban]" class="bank-details--iban-field" />
                    </div>
                </div>
                <div class="block-group bank-details--bic">
                    <div class="block column--left">
                        {s name='form/bank-details/field/bic/label'}BIC:{/s}
                    </div>
                    <div class="block column--right">
                        <input type="text" name="payolution[{$payolutionPaymentMethod}][bic]" class="bank-details--bic-field"/>
                    </div>
                </div>
            </div>
        {/if}

        <input class="payolution-installment--duration-hidden-input" type="hidden" name="payolution[{$payolutionPaymentMethod}][duration]"
               value="{$payolutionInstallmentArray->PaymentDetails.0->Duration}">
    </div>

    <div class="panel--footer">
        <button class="btn is--primary payolution-payment--submit-button">
            {s name='installment/overview/button/finish/label'}Weiter{/s}
            <i class="icon--arrow-right"></i>
        </button>
    </div>
</div>

