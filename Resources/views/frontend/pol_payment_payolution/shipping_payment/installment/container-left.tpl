{namespace name="frontend/pol_payment_payolution/main"}

<div class="payolution--panel">
    <div class="panel--heading">
        {s name='installment/left/heading'}1. &Uuml;berpr&uuml;fung der Ratenverf&uuml;gbarkeit{/s}
    </div>
    <div class="panel--content">
        {include file="frontend/pol_payment_payolution/forms/birthday.tpl"}

        {include file="frontend/pol_payment_payolution/forms/phone.tpl"}

        {include file="frontend/pol_payment_payolution/forms/privacy-check.tpl"}

        <div id="payolutionInstallmentLeft--errorText" class="clearfix"></div>
    </div>
    <div class="panel--footer">
        <button class="btn is--primary is--large js--payolution-installment--availability-check" name="{s name='installment/button/installment-availability-check/label'}Ratenverf&uuml;gbarkeit pr&uuml;fen{/s}">
            {s name='installment/button/installment-availability-check/label'}Ratenverf&uuml;gbarkeit pr&uuml;fen{/s}
        </button>
    </div>
</div>

