{namespace name="frontend/pol_payment_payolution/main"}

<div id="payolutionInstallmentInfoModal">
    <span id="payolutionInstallmentInfoModal--header">{s name='installment/info/modal/headline'}Beispielratenzahlung*{/s}</span>
    <div class="clearfix"></div>
    <span>{s name='installment/info/modal/description'}Kaufen Sie dieses Produkt bequem in Raten - w&auml;hlen Sie einfach die gew&uuml;nschte Anzahl an Monatsraten:{/s}</span>
    <div class="clearfix"></div>
    <div id="payolutionInstallmentInfoModal--payolutionInstallmentSelection">
        {*filled with Jquery*}
    </div>
    <div class="clearfix"></div>
    <div id="payolutionInstallmentInfoModal--payolutionInstallmentOverview">
        {*filled with Jquery*}
    </div>
    <div class="clearfix"></div>
    <button class="payolutionInstallmentInfoModal--payolutionInstallmentButton">
        {s name='installment/plan/show'}Ratenplan einblenden{/s}
    </button>
    <div class="clearfix"></div>
    <div id="payolutionInstallmentInfoModal--payolutionInstallmentPlan" style="display: none;">
        <span id="payolutionInstallmentPlan--header">{s name='installment/info/modal/plan/headline'}Finanzierung{/s}</span>
        <div class="clearfix"></div>
        <span>{s name='installment/info/modal/plan/overview/headline'}Raten&uuml;bersicht{/s}</span>
        <div class="clearfix"></div>
        <div id="payolutionInstallmentPlan--payolutionInstallmentPlanListBox">
            {*filled with Jquery*}
        </div>
    </div>
    <div class="clearfix marginBottom15"></div>
    <span class="payolutionInfoText">{s name='installment/info/modal/notice'}*) Die aufgef&uuml;hrten Ratenwerte dienen nur als Beispiel f&uuml;r den gew&auml;hlten Produktbetrag/aktuellen Warenkorbwert. Die endg&uuml;ltigen Raten erhalten Sie w&auml;hrend dem Kaufprozess bei der Zahlungsartenauswahl{/s}</span>
</div>

<script type="text/javascript">
    /** snippets */
    var selectionInstallmentsPerMonth = "{s name='installment/selection/per-month'}pro Monat - {/s}";
    var selectionInstallments = "{s name='installment/selection/installments'}Raten{/s}";
    var payolutionInstallmentInfoModalPlanButtonOpen = "{s name='payolutionInstallmentInfoModalPlanButton'}Ratenplan einblenden{/s}";
    var payolutionInstallmentInfoModalPlanButtonClose = "{s name='payolutionInstallmentInfoModalPlanButtonClose'}Ratenplan ausblenden{/s}";

    var overviewCountInstallments = "{s name='installment/overview/installment-count'}Anzahl der Raten:{/s}";
    var overviewFinanceAmount = "{s name='installment/overview/finance-amount'}Finanzierungsbetrag:{/s}";
    var overviewTotalAmount = "{s name='installment/overview/amount-total'}Gesamtsumme:{/s}";
    var overviewNominalZins = "{s name='installment/overview/nominal-zins'}Nominalzins:{/s}";
    var overviewNominalZinsProzentualSign = "{s name='percent-sign'}%{/s}";
    var overviewEffectiveZins = "{s name='installment/overview/effective-zins'}Effektivzins:{/s}";
    var overviewEffectiveZinsProzentualSign = "{s name='percent-sign'}%{/s}";
    var overviewMonthlyInstallment = "{s name='installment/overview/installment-monthly'}Monatliche Raten:{/s}";

    var planRate = "{s name='installment/plan/xth-rate'}. Rate:{/s}";
    var planPaymentTo = "{s name='installment/plan/due-until'}f&auml;llig am{/s}";
</script>


