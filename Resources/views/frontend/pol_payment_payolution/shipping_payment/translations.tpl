{namespace name="frontend/pol_payment_payolution/main"}

<script type="text/javascript">
    var payolutionB2bErrorText = "{s name='form/company/field/company/error/empty'}Bitte tragen Sie einen Firmennamen ein.{/s}";
    var payolutionSecurityErrorText = "{s name='form/privacy-check/error/unchecked'}Bitte best&auml;tigen Sie Ihr Einverst&auml;ndnis mit der &Uuml;bermittlung der notwendigen Daten.{/s}";
    var payolutionErrorTextPhone = "{s name='form/phone/field/phone/error/empty'}Bitte tragen Sie einen Telefonnummer ein.{/s}";

    var payolutionBirthdayErrorText = "{s name='form/birthday/error/unselected'}Bitte w&auml;hlen Sie Ihr Geburtsdatum aus{/s}";
    var payolutionBirthdayErrorText18 = "{s name='form/birthday/error/not18'}F&uuml;r diese Zahlungsart m&uuml;ssen Sie mindestens 18 Jahre alt sein.{/s}";
    var payolutionMandateErrorText = "{s name='form/sepa-mandate/error/unchecked'}Bitte best&auml;tigen Sie die Erteilung des SEPA-Lastschriftsmandats.{/s}";
    var payolutionPhoneErrorText = "{s name='form/phone/error/empty'}Bitte geben Sie ihre Telefonnummer an.{/s}";

    var payolutionElvAccountFormHolderError = "{s name='form/bank-details/field/holder/error/empty'}Bitte geben Sie den Namen des Kontoinhabers an{/s}";
    var payolutionElvAccountFormIbanError = "{s name='form/bank-details/field/iban/error/empty'}Bitte geben Sie eine g&uuml;ltige IBAN ein{/s}";
    var payolutionElvAccountFormBicError = "{s name='form/bank-details/field/bic/error/empty'}Bitte geben Sie eine g&uuml;ltige BIC ein{/s}";
    var payolutionInstallmentBirthdayErrorText = "{s name='form/birthday/error/unselected'}Bitte geben Sie Ihr Geburtsdatum an.{/s}";
    var payolutionInstallmentBirthday18ErrorText = "{s name='form/birthday/error/not18'}Sie m&uuml;ssen &uuml;ber 18 Jahre alt sein.{/s}";
    var payolutionInstallmentSecurityErrorText = "{s name='form/privacy-check/error/unchecked'}Bitte best&auml;tigen Sie Ihr Einverst&auml;ndnis mit der &Uuml;bermittlung der notwendigen Daten.{/s}";

    var payolutionInstallmentAccountFormHolderError = "{s name='form/bank-details/field/holder/error/empty'}Bitte geben Sie den Namen des Kontoinhabers an{/s}";
    var payolutionInstallmentAccountFormIbanError = "{s name='form/bank-details/field/iban/error/empty'}Bitte geben Sie eine g&uuml;ltige IBAN ein{/s}";
    var payolutionInstallmentAccountFormBicError = "{s name='form/bank-details/field/bic/error/empty'}Bitte geben Sie eine g&uuml;ltige BIC ein{/s}";

    var payolutionInstallmentCheckoutPlanButtonOpen = "{s name='installment/plan/show'}Ratenplan einblenden{/s}";
    var payolutionInstallmentCheckoutPlanButtonClose = "{s name='installment/plan/close'}Ratenplan ausblenden{/s}";

    var payolutionFieldEmptyError = "{s name='form/general/error/empty'}Bitte füllen Sie das folgende Feld aus.{/s}";
    var payolutionCompanyTypeNotSelectedError = "{s name='form/company/field/type/error/unselected'}Bitte wählen Sie eine Organisationsform aus.{/s}";

    var Controller = '{$payolutionControllerCheck}';

    var installmentPreCheckCheckoutUrl = '{url controller='PolPaymentPayolution' action='installmentCheckoutPreCheck'}';
    var setBirthDayUrl = '{url controller='PolPaymentPayolution' action='setBirthday'}';
</script>
