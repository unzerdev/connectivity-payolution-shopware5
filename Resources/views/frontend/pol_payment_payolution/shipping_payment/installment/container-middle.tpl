{namespace name="frontend/pol_payment_payolution/main"}

<div class="payolution--panel">
    <div class="panel--heading">
        {s name='installment/middle/heading'}2. Ratenauswahl{/s}
    </div>
    <div class="panel--content">
        <span>{s name='installment/middle/subheading'}Bitte w&auml;hlen Sie die gew&uuml;nschte Ratenanzahl.{/s}</span>

        <!-- Installment Selection -->
        <div class="payolution-installment--selection">
            {foreach from=$payolutionInstallmentArray->PaymentDetails item=rates name=payolutionDuration}
                {assign var=rateAmount value={$rates->Installment[0]->Amount|currency}}
                <div class="payolution-installment-selection--column {if $smarty.foreach.payolutionDuration.first} is--first{/if} {if $smarty.foreach.payolutionDuration.last} is--last{/if}"
                     data-duration="{$rates->Duration}"
                     data-rate-amount="{$rateAmount}"
                     data-original-amount="{$rates->OriginalAmount|currency}"
                     data-total-amount="{$rates->TotalAmount|currency}"
                     data-interest-rate="{$rates->InterestRate|replace:'.':','}"
                     data-effective-interest-rate="{$rates->EffectiveInterestRate|replace:'.':','}"
                >
                    {$rateAmount} {s name='installment/selection/per-month'}pro Monat - {/s}{$rates->Duration} {s name='installment/selection/installments'}Raten{/s}
                </div>
            {/foreach}
        </div>

        <!-- Installment Plan -->
        <div class="payolution-installment--plan">
            {foreach from=$payolutionInstallmentArray->PaymentDetails item=planList key=planListKey name=payolutionDuration}
                {assign var=rates value=$planList->Installment}
                <div class="payolution-installment--plan-list is--hidden" data-duration="{$rates|count}">
                    {foreach from=$rates key=rateKey item=rate}
                        <div class="row block-group">
                            <div class="block column--left">{$rateKey+1}{s name='installment/plan/xth-rate'}. Rate:{/s}</div>
                            <div class="block column--middle">{$rate->Amount|currency}</div>
                            <div class="block column--right">({s name='installment/plan/due-until'}f&auml;llig am {/s} {$rate->Due|date_format:"%d.%m.%Y"})</div>
                        </div>
                    {/foreach}
                </div>
            {/foreach}
        </div>

        <!-- Installment Pdf Link -->
        <a href="" data-template="{url controller='PolPaymentPayolution' action='getInstallmentPdf' duration="-s-"}" class="payolution-installment--pdf-link" target="_blank">
            <i class="icon--download"></i>
            <span>{s name='installment/plan/download-exemplary'}Download Ratenkredit-Vertragsentwurf{/s}</span>
        </a>
    </div>
</div>
