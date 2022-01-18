{namespace name="frontend/pol_payment_payolution/main"}

<div class="payolution--panel">
    <div class="panel--heading">
        {s name='installment/middle/heading'}2. Ratenauswahl{/s}
    </div>
    <div class="panel--content">
        <div class="block-group payolution-error--container">
            <div class="block">
                {s name='installment/middle/error/text'}Die Ratenkauf&uuml;berpr&uuml;fung war leider nicht erfolgreich{/s}
            </div>
        </div>
        <p>
            {s name='installment/middle/error/text/top'}Bitte w&auml;hlen Sie eine andere Zahlungsart. Ein Ratenkauf war leider nicht m&ouml;glich. Dies kann unterschiedliche Gr&uuml;nde haben. Bitte versuchen Sie es erneut und achten Sie dabei auf folgende Punkte:{/s}
        </p>
        {s name='installment/middle/error/text/center'}
        <ul>
            <li>Korrekte Schreibweise aller Angaben</li>
            <li>Ihr korrektes Geburtsdatum</li>
            <li>Keine Firmen-, Postfach- oder Packstationadresse</li>
            <li>Sofern Sie bereits &uuml;ber eine laufende Demoshop-Finanzierung verf&uuml;gen, hat auch das
                Einfluss auf weitere Ratenk&auml;ufe
            </li>
            <li>Anschrift und Namen in der Rechnungsadresse m&uuml;ssen mit der Meldeadresse
                &uuml;bereinstimmen
            </li>
        </ul>
        {/s}
        <p>
            {s name='installment/middle/error/text/bottom'}Sollte der Versuch erneut fehlschlagen, w&auml;hlen Sie bitte eine andere Zahlungsart.{/s}
        </p>
    </div>
    <div class="panel--footer">
        <button class="btn is--primary is--large js--payolution-installment--retry-button">
            {s name='installment/middle/error/button/retry'}Erneut versuchen{/s}
        </button>
    </div>
</div>
