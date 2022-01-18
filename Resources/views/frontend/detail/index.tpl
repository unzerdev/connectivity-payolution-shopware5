{extends file="parent:frontend/detail/index.tpl"}

{block name='frontend_detail_index_buy_container_base_info' append}
    {if $payolutionInstallmentActive}
        <script type="text/javascript" src="{url controller='PolPaymentPayolution' action='getClJsLibrary'}"></script>
        <div class="payolution-installment--detail" data-additionalClass="payolution-installment--modal" data-currency-symbol="{$payolutionCurrency.symbol}" data-currency="{$payolutionCurrency.iso}" data-price="{$sArticle.price}" id="payolutionInstallmentInfo" data-content="" data-modalbox="true" data-targetselector="a" data-mode="ajax" data-width="500">

            <div id="payolutionInstallmentInfo--text">
                <div id="icon--currency"><span>{$payolutionCurrency.symbol}</span></div>
                <span>
                    {s name='payolutionRatePayBeforeAmount'}Finanzieren ab{/s} <span class="payolutionInstallmentPrice"></span> {$payolutionCurrency.symbol} {s name='payolutionRatePayAfterAmount'}/Monat{/s}
                </span>
            </div>
            <div id="payolutionInstallmentButton">
                <a href="{url controller='PolPaymentPayolution' action='installmentInformation'}">
                    {s name='payolutionRatePayButton'}Mehr Informationen{/s}
                </a>
            </div>
        </div>
    {/if}
{/block}

