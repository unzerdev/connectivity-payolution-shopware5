{namespace name="frontend/pol_payment_payolution/main"}

<div class="block-group payolution--privacy-check" data-modalbox="true" data-targetselector="a" data-mode="iframe">
    <div class="block payolution-privacy-check--checkbox">
        <input type="checkbox" name="payolution[{$payolutionPaymentMethod}][privacyCheck]" id="payolution-privacy-check--checkbox{$payment_mean.id}"/>
    </div>
    <div class="block payolution-privacy-check--label">
        <label for="payolution-privacy-check--checkbox{$payment_mean.id}">
            {s name='form/privacy-check/label'}{/s}
        </label>
    </div>
</div>
