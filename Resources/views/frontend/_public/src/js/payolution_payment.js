;(function ($, window) {
    $.plugin('payolutionPayment', {

        $paymentMethods: null,
        $payolutionPaymentMethodInputs: null,

        $payolutionPaymentSubmitButton: null,

        $payolutionInstallmentColumn1: null,
        $payolutionInstallmentColumn2: null,
        $payolutionInstallmentColumn2Error: null,
        $payolutionInstallmentColumn3: null,

        $payolutionInstallmentSelectionColumn: null,
        $payolutionInstallmentPlanList: null,
        $payolutionInstallmentPdfLink: null,
        $payolutionInstallmentDurationHiddenInput: null,
        $payolutionInstallmentOverviewBank: null,

        $installmentAvailabilityCheck: null,
        $installmentRetryButton: null,

        $b2bCompanyTypeSelect: null,
        $payolutionB2BSoleTraderOnlyContainer: null,
        $payolutionB2BCompanyTypeSelectedContainer: null,

        defaults: {
            //Classes
            isVisibleClass: 'is--visible',
            isHiddenClass: 'is--hidden',
            isDisabledClass: 'is--disabled',
            isActiveClass: 'is--active',
            isFirstClass: 'is--first',
            isLastClass: 'is--last',
            isRemovableClass: 'is--removable',

            errorMessageContainerClass: 'payolution-error--container',

            //Selectors
            paymentMethodSelector: '.payment--method',
            payolutionPaymentBlockSelector: '.js--payolution--payment',
            payolutionPaymentBlockAccountSelector: '.payment',
            payolutionPaymentBlockDataAttributePaymentMethod: 'payment-method',
            payolutionPaymentBlockInputSelector: '.js--payolution--payment input[type=radio]',
            payolutionPaymentBlockInputSelectorShipping: '.payment--method-list input[type=radio]',
            methodPayolutionSelector: '.method--payolution',
            payolutionBirthdateSelector: '.payolution-form--birthdate',
            payolutionBirthdateDaySelector: '.payolution-form--birthdate .birthdate--day select',
            payolutionBirthdateMonthSelector: '.payolution-form--birthdate .birthdate--month select',
            payolutionBirthdateYearSelector: '.payolution-form--birthdate .birthdate--year select',
            payolutionPrivacyCheckboxSelector: '.payolution--privacy-check input[type=checkbox]',
            payolutionSepaMandateCheckboxSelector: '.payolution--sepa-mandate input[type=checkbox]',

            payolutionPaymentSubmitButtonSelector: '.payolution-payment--submit-button',

            payolutionInstallmentColumn1Selector: '.payolution-installment--column1',
            payolutionInstallmentColumn2Selector: '.payolution-installment--column2',
            payolutionInstallmentColumn2ErrorSelector: '.payolution-installment--column2-error',
            payolutionInstallmentColumn3Selector: '.payolution-installment--column3',

            payolutionInstallmentSelectionColumnSelector: '.payolution-installment-selection--column',
            payolutionInstallmentPlanListSelector: '.payolution-installment--plan-list',
            payolutionInstallmentPdfLinkSelector: '.payolution-installment--pdf-link',
            payolutionInstallmentDurationHiddenInputSelector: '.payolution-installment--duration-hidden-input',

            payolutionInstallmentOverviewBankSelector: '.payolution-installment--overview-bank',

            phoneFieldSelector: '.register--phone',

            bankDetailsContainerSelector: '.payolution-form--bank-details',
            bankDetailsHolderSelector: '.bank-details--holder',
            bankDetailsIbanSelector: '.bank-details--iban',
            bankDetailsBicSelector: '.bank-details--bic',

            bankDetailsHolderFieldSelector: '.bank-details--holder-field',
            bankDetailsIbanFieldSelector: '.bank-details--iban-field',
            bankDetailsBicFieldSelector: '.bank-details--bic-field',

            errorMessageContainerSelector: '.block-group.payolution-error--container',

            payolutionFormPhoneeSelector: '.register--phone',
            payolutionFormBirthdateSelector: '.block-group.payolution-form--birthdate',
            payolutionFormPrivacyCheckSelector: '.block-group.payolution--privacy-check',
            payolutionFormSepaMandateSelector: '.block-group.payolution--sepa-mandate',

            //Installment
            installmentKeyword: 'installment',
            installmentAvailabilityCheckSelector: '.js--payolution-installment--availability-check',
            installmentRetryButtonSelector: '.js--payolution-installment--retry-button',

            //B2C
            b2cKeyword: 'b2c',

            //B2B
            b2bKeyword: 'b2b',
            b2bCompanyTypeSelectSelector: '.payolution-form--company-type select.company-type--select',
            payolutionB2BCompanyTypeSelectedContainerSelector: '.payolution-b2b--company-type-selected-container',
            payolutionB2BSoleTraderOnlyContainerSelector: '.payolution-b2b--sole-trader-only-container',

            //ELV
            elvKeyword: 'elv'

        },

        /**
         * Init plugin.
         */
        init: function () {
            var me = this;
            var opts = me.opts;

            me.$paymentMethods = $(opts.paymentMethodSelector);
            me.$payolutionPaymentMethodInputs = $(opts.payolutionPaymentBlockInputSelector);
            me.$installmentAvailabilityCheck = $(opts.installmentAvailabilityCheckSelector);
            me.$installmentRetryButton = $(opts.installmentRetryButtonSelector);

            me.$payolutionPaymentSubmitButton = $(opts.payolutionPaymentSubmitButtonSelector);

            me.$payolutionInstallmentColumn1 = $(opts.payolutionInstallmentColumn1Selector);
            me.$payolutionInstallmentColumn2 = $(opts.payolutionInstallmentColumn2Selector);
            me.$payolutionInstallmentColumn2Error = $(opts.payolutionInstallmentColumn2ErrorSelector);
            me.$payolutionInstallmentColumn3 = $(opts.payolutionInstallmentColumn3Selector);

            me.$payolutionInstallmentSelectionColumn = $(opts.payolutionInstallmentSelectionColumnSelector);
            me.$payolutionInstallmentPlanList = $(opts.payolutionInstallmentPlanListSelector);
            me.$payolutionInstallmentPdfLink = $(opts.payolutionInstallmentPdfLinkSelector);
            me.$payolutionInstallmentDurationHiddenInput = $(opts.payolutionInstallmentDurationHiddenInputSelector);
            me.$payolutionInstallmentOverviewBank = $(opts.payolutionInstallmentOverviewBankSelector);

            me.$b2bCompanyTypeSelect = $(opts.b2bCompanyTypeSelectSelector);
            me.$payolutionB2BSoleTraderOnlyContainer = $(opts.payolutionB2BSoleTraderOnlyContainerSelector);
            me.$payolutionB2BCompanyTypeSelectedContainer = $(opts.payolutionB2BCompanyTypeSelectedContainerSelector);

            me.registerEvents();

            //Initially show payment method of selected payment mean
            me.showPaymentMethod();
        },

        /**
         * Registers event listeners.
         */
        registerEvents: function() {
            var me = this;

            //General
            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(me.onPaymentChanged, me));
            $.subscribe('plugin/swRegister/onPaymentChanged', $.proxy(me.onPaymentChanged, me));
            me._on(me.$payolutionPaymentSubmitButton, 'click', $.proxy(me.onPayolutionPaymentSubmitButtonClicked, me));

            //Installment
            me._on(me.$installmentAvailabilityCheck, 'click', $.proxy(me.onInstallmentAvailabilityCheckClicked, me));
            me._on(me.$installmentRetryButton, 'click', $.proxy(me.onPayolutionInstallmentRetryButtonClicked, me));
            me._on(me.$payolutionInstallmentSelectionColumn, 'click', $.proxy(me.onInstallmentSelectionColumnClicked, me));

            //B2B
            me._on(me.$b2bCompanyTypeSelect, 'change', $.proxy(me.onPayolutionB2BCompanyTypeChanged, me));

        },

        /**
         * Initializes shopware modals.
         */
        initModals: function() {
            if ($.fn.modalbox) {
                $('*[data-modalbox="true"]').modalbox();
            } else {
                $('*[data-modalbox="true"]').swModalbox();
            }
        },

        //Event Listener

        /**
         * Event listener on payolution b2b company type has changed.
         */
        onPayolutionB2BCompanyTypeChanged: function() {
            var me = this;
            var opts = me.opts;

            me.$payolutionB2BCompanyTypeSelectedContainer.addClass(opts.isHiddenClass);
            me.$payolutionB2BSoleTraderOnlyContainer.addClass(opts.isHiddenClass);

            var companyType = me.$b2bCompanyTypeSelect.first().val();

            if (companyType === '') {
                return;
            }

            me.$payolutionB2BCompanyTypeSelectedContainer.removeClass(opts.isHiddenClass);

            if (companyType === 'soletrader') {
                me.$payolutionB2BSoleTraderOnlyContainer.removeClass(opts.isHiddenClass);
            }
        },

        /**
         * Event listener on payolution payment submit button clicked.
         */
        onPayolutionPaymentSubmitButtonClicked: function (event) {
            var me = this;
            var opts = me.opts;

            me.removeAllErrorMessages();

            if (!me.isPayolutionPayment()) {
                console.log('no payolution payment');
                return true;
            }

            event.preventDefault();

            if (me.isCurrentPayment(opts.installmentKeyword)) {
                me.submitPaymentInstallment();
            }

            if (me.isCurrentPayment(opts.b2cKeyword)) {
                me.submitPaymentB2C();
            }

            if (me.isCurrentPayment(opts.elvKeyword)) {
                me.submitPaymentELV();
            }

            if (me.isCurrentPayment(opts.b2bKeyword)) {
                me.submitPaymentB2B();
            }

            return false;
        },

        /**
         * Sub function to handle b2b submission.
         *
         * @return null
         */
        submitPaymentB2B: function() {
            var me = this;
            var opts = me.opts;

            var error = false;

            if (me.$b2bCompanyTypeSelect.val() === '') {
                me.addErrorByField(
                    me.$b2bCompanyTypeSelect,
                    payolutionCompanyTypeNotSelectedError
                );
                error = true;
            }

            if (!me.checkPrivacyCheckbox()) {
                me.addPrivacyCheckError();
                error = true;
            }

            var inputFields = $(opts.payolutionB2BCompanyTypeSelectedContainerSelector).find('input:visible');

            $.each(inputFields, function (key, field) {
                var $field = $(field);

                if ($field.val() === '') {

                    me.addErrorByField(
                        $field,
                        payolutionFieldEmptyError
                    );
                    error = true;
                }
            });

            var $selectedPayment = me.getSelectedPaymentMethod();
            var $birthdayContainer = $selectedPayment.find(opts.payolutionBirthdateSelector + ' select');
            var birthdayContainerVisible = $birthdayContainer.is(':visible');

            if (birthdayContainerVisible && !me.checkBirthday()) {
                me.addBirthdayInvalidError();
                error = true;
            }

            if (error) {
                me.scrollToSelectedPayment();
                return null;
            }

            if (birthdayContainerVisible) {
                $.loadingIndicator.open();

                var birthday = me.getFormattedBirthday();
                var promiseBirthdaySet = me.doSetBirthdayRequest(birthday);

                promiseBirthdaySet.done(function () {
                    me.submitShippingPaymentForm();
                }).fail(function () {
                    me.addBirthdayNot18Error();
                    me.scrollToSelectedPayment();
                }).always(function() {
                    $.loadingIndicator.close();
                });

                return null;
            }

            me.submitShippingPaymentForm();

        },

        /**
         * Sub function to handle installment submission.
         *
         * @return {boolean}
         */
        submitPaymentInstallment: function() {
            var me = this;

            if (!me.isPaymentOverviewBankAvailable()) {
                return false;
            }

            if (!me.checkBankDetails()) {
                me.scrollToSelectedPayment();
                return false;
            }

            $.loadingIndicator.open();

            me.submitShippingPaymentForm();
        },

        /**
         * Check Phone
         */
        checkPhone: function () {
            var me = this;
            var opts = me.opts;

            var $field = $(opts.phoneFieldSelector + '> input');

            var value = $field.val();

            return typeof value === 'string' && value !== '';
        },

        /**
         * Add Phone Invalid Error
         */
        addPhoneInvalidError: function () {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.phoneFieldSelector,
                payolutionPhoneErrorText
            );
        },

        /**
         * Sub function to handle b2c submission.
         *
         */
        submitPaymentB2C: function() {
            var me = this;
            var opts = me.opts;

            var error = false;

            if (me.checkFieldExist(opts.phoneFieldSelector) && !me.checkPhone()) {
                me.addPhoneInvalidError();
                error = true;
            }

            if (!me.checkBirthday()) {
                me.addBirthdayInvalidError();
                error = true;
            }

            if (!me.checkPrivacyCheckbox()) {
                me.addPrivacyCheckError();
                error = true;
            }

            if (error) {
                me.scrollToSelectedPayment();
                return;
            }

            $.loadingIndicator.open();

            var birthday = me.getFormattedBirthday();

            var promiseBirthdaySet = me.doSetBirthdayRequest(birthday);

            promiseBirthdaySet.done(function () {
                me.submitShippingPaymentForm();
            }).fail(function () {
                me.addBirthdayNot18Error();
                me.scrollToSelectedPayment();
            }).always(function() {
                $.loadingIndicator.close();
            });
        },

        /**
         * Sub function to handle elv submission.
         *
         */
        submitPaymentELV: function() {
            var me = this;

            var error = false;

            if (!me.checkBankDetails()) {
                error = true;
            }

            if (!me.checkBirthday()) {
                me.addBirthdayInvalidError();
                error = true;
            }

            if (!me.checkPrivacyCheckbox()) {
                me.addPrivacyCheckError();
                error = true;
            }

            if (!me.checkSepaMandateCheckbox()) {
                me.addSepaMandateError();
                error = true;
            }

            if (error) {
                me.scrollToSelectedPayment();
                return;
            }

            $.loadingIndicator.open();

            var birthday = me.getFormattedBirthday();

            var promiseBirthdaySet = me.doSetBirthdayRequest(birthday);

            promiseBirthdaySet.done(function () {
                me.submitShippingPaymentForm();
            }).fail(function () {
                me.addBirthdayNot18Error();
                me.scrollToSelectedPayment();
            }).always(function() {
                $.loadingIndicator.close();
            });
        },

        /**
         * Checks if the bank detail fields are available for current selected payment method.
         *
         * @return {boolean}
         */
        bankDetailsAvailable: function () {
            var me = this;
            var opts = me.opts;

            var $selectedPayment = me.getSelectedPaymentMethod();

            return $selectedPayment.find(opts.bankDetailsContainerSelector).length > 0;
        },

        /**
         * Checks bank details and shows specific error
         */
        checkBankDetails: function() {
            var me = this;

            //Skip bank detail check if fields are not available (CH)
            if (!me.bankDetailsAvailable()) {
                return true;
            }

            var error = false;

            if (!me.checkBankDetailsHolder()) {
                me.addBankDetailsHolderEmptyError();
                error = true;
            }

            if (!me.checkBankDetailsIban()) {
                me.addBankDetailsIbanInvalidError();
                error = true;
            }

            if (!me.checkBankDetailsBic()) {
                me.addBankDetailsBicEmptyError();
                error = true;
            }

            return !error;
        },


        /**
         * Checks the bank detail holder is empty
         */
        checkBankDetailsHolder: function() {
            var me = this;
            var opts = me.opts;

            return me.checkPaymentInputFieldEmpty(opts.bankDetailsHolderFieldSelector);
        },

        /**
         * Checks the bank detail iban is empty
         */
        checkBankDetailsIban: function() {
            var me = this;
            var opts = me.opts;

            return me.checkPaymentInputFieldEmpty(opts.bankDetailsIbanFieldSelector);
        },

        /**
         * Checks if the sepa mandate checkbox is checked.
         */
        checkSepaMandateCheckbox: function() {
            var me = this;
            var opts = me.opts;

            var $selectedPayment = me.getSelectedPaymentMethod();

            var checkbox = $selectedPayment.find(opts.payolutionSepaMandateCheckboxSelector);

            return checkbox.is(':checked');
        },

        /**
         * Checks the bank detail bic is empty
         */
        checkBankDetailsBic: function() {
            var me = this;
            var opts = me.opts;

            return me.checkPaymentInputFieldEmpty(opts.bankDetailsBicFieldSelector);
        },

        /**
         * Checks if the given input by selector is empty.
         *
         * @param {object} selector
         *
         * @return {boolean}
         */
        checkPaymentInputFieldEmpty: function(selector) {
            var me = this;

            var $selectedPayment = me.getSelectedPaymentMethod();
            var $field = $selectedPayment.find(selector);

            var value = $field.val();

            return typeof value === 'string' && value !== '';
        },

        /**
         * Check if Field exist
         *
         * @param {object} selector
         *
         * @return {boolean}
         */
        checkFieldExist: function (selector) {
            var me = this;

            var $selectedPayment = me.getSelectedPaymentMethod();
            var $field = $selectedPayment.find(selector);

            return $field.length  > 0;
        },

        /**
         * Event listener on payolution payment mean selected.
         *
         * @return {void}
         */
        onPaymentChanged: function() {
            var me = this;

            //Reinitilize plugin
            window.StateManager.removePlugin('#shippingPaymentForm, form[name=frmRegister]', 'payolutionPayment');
            window.StateManager.addPlugin('#shippingPaymentForm, form[name=frmRegister]', 'payolutionPayment');

            if ($.fn.selectboxReplacement) {
                window.StateManager.removePlugin('select:not([data-no-fancy-select="true"])', 'selectboxReplacement');
                window.StateManager.addPlugin('select:not([data-no-fancy-select="true"])', 'selectboxReplacement');
            } else {
                window.StateManager.removePlugin('select:not([data-no-fancy-select="true"])', 'swSelectboxReplacement');
                window.StateManager.addPlugin('select:not([data-no-fancy-select="true"])', 'swSelectboxReplacement');
            }

            me.hidePaymentMethods();
            me.showPaymentMethod();
        },

        /**
         * Event listener on installment availability check clicked.
         *
         * @param {object} event
         */
        onInstallmentAvailabilityCheckClicked: function(event) {
            var me = this;

            event.preventDefault();

            $.loadingIndicator.open();

            me.removeAllErrorMessages();

            me.checkInstallmentAvailability().done(function () {
                me.disableInstallmentCheck();
                me.enableInstallmentDetails();
            }).fail(function (simpleError) {
                if (!simpleError) {
                    me.showInstallmentError();
                }
                me.scrollToSelectedPayment();
            }).always(function () {
                $.loadingIndicator.close();
            });
        },

        /**
         * Event listener on payolution installment retry button clicked.
         *
         * @param {object} event
         *
         * @return boolean
         */
        onPayolutionInstallmentRetryButtonClicked: function(event) {
            var me = this;

            event.preventDefault();

            $.loadingIndicator.open();

            me.hideInstallmentError();
            me.disableInstallmentDetails();
            me.enableInstallmentCheck();

            setTimeout(function () {
                $.loadingIndicator.close();
            }, 500);

            return false;
        },

        /**
         * Event listener on installment selection column.
         *
         * @param {object} event
         */
        onInstallmentSelectionColumnClicked: function (event) {
            var me = this;

            var $installmentSelectionColumn = $(event.currentTarget);

            me.selectInstallmentSelectionColumn($installmentSelectionColumn);
        },

        /**
         * Checks if overview dank details are there and not disabled.
         *
         * @return {boolean}
         */
        isPaymentOverviewBankAvailable: function() {
            var me = this;
            var opts = me.opts;

            var columnContainer = me.$payolutionInstallmentOverviewBank
                .closest(opts.payolutionInstallmentColumn3Selector);

            var columnContainerDisabled = columnContainer.hasClass(opts.isDisabledClass);

            return !columnContainerDisabled;
        },

        /**
         * Selects given installment selection column.
         *
         * @param $installmentSelectionColumn
         */
        selectInstallmentSelectionColumn: function ($installmentSelectionColumn) {
            var me = this;
            var opts = me.opts;

            me.unselectInstallmentSelectionColumns();

            $installmentSelectionColumn.addClass(opts.isActiveClass);

            var duration = $installmentSelectionColumn.data('duration');

            me.showInstallmentPlanList(duration);
            me.prepareInstallmentPdfLink(duration);
            me.prepareInstallmentOverview($installmentSelectionColumn);
            me.$payolutionInstallmentDurationHiddenInput.val(duration);
        },

        /**
         * Unselects all installment selection columns.
         */
        unselectInstallmentSelectionColumns: function() {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentSelectionColumn.removeClass(opts.isActiveClass);
        },

        /**
         * Pre selects first installment selection column.
         */
        preselectFirstInstallmentSelectionColumn: function() {
            var me = this;

            var $selectionColumns = me.$payolutionInstallmentSelectionColumn;

            var $firstSelectionColumn = $selectionColumns.first();

            me.selectInstallmentSelectionColumn($firstSelectionColumn);
        },

        /**
         * Prepares Installment pdf link by given duration.
         *
         * @param {string} duration
         */
        prepareInstallmentPdfLink: function(duration) {
            var me = this;

            var urlTemplate = me.$payolutionInstallmentPdfLink.data('template');

            urlTemplate = urlTemplate.replace('-s-', duration);

            me.$payolutionInstallmentPdfLink.attr('href', urlTemplate);
        },

        /**
         * Prepares installment overview.
         *
         * @param {object} $installmentSelectionColumn
         */
        prepareInstallmentOverview: function($installmentSelectionColumn) {
            var overviewFields = [
                'duration',
                'original-amount',
                'total-amount',
                'interest-rate',
                'effective-interest-rate',
                'rate-amount'
            ];

            $.each(overviewFields, function (index, value) {
                var placeholderValue = $installmentSelectionColumn.data(value);
                var placeholderField = $('.placeholder--' + value);

                placeholderField.text(placeholderValue);
            });
        },

        /**
         * Checks installment availability.
         */
        checkInstallmentAvailability: function() {
            var me = this;
            var opts = me.opts;

            var deferredInstallmentAvailability = $.Deferred();

            if (!me.isCurrentPayment(opts.installmentKeyword)) {
                return null;
            }

            if (!me.checkBirthday()) {
                me.addBirthdayInvalidError();
                deferredInstallmentAvailability.reject(true);
                return deferredInstallmentAvailability.promise();
            }

            if (!me.checkPrivacyCheckbox()) {
                me.addPrivacyCheckError();
                deferredInstallmentAvailability.reject(true);
                return deferredInstallmentAvailability.promise();
            }

            var birthday = me.getFormattedBirthday();

            var promiseBirthdaySet = me.doSetBirthdayRequest(birthday);

            var promisePreCheckInstallment = promiseBirthdaySet.then(function () {
                return me.doPreCheckInstallmentRequest();
            }, function () {
                me.addBirthdayNot18Error();
                deferredInstallmentAvailability.reject(true);

                return deferredInstallmentAvailability.promise();
            });

            promisePreCheckInstallment.done(function () {
                deferredInstallmentAvailability.resolve();
            });

            promisePreCheckInstallment.fail(function () {
                deferredInstallmentAvailability.reject();
            });

            return deferredInstallmentAvailability.promise();
        },

        /**
         * Sends a request to set the given birthday.
         *
         * @param {string} birthday
         *
         * @return {object}
         */
        doSetBirthdayRequest: function(birthday) {
            var deferred = $.Deferred();

            $.ajax({
                type: "GET",
                url: setBirthDayUrl + '?birthday=' + birthday,
                success: function (data) {
                    if (data['success']) {
                        deferred.resolve();
                        return;
                    }

                    deferred.reject();
                },
                error: function () {
                    deferred.reject();
                }
            });

            return deferred.promise();
        },

        /**
         * Pre check installment request.
         *
         * @return {object}
         */
        doPreCheckInstallmentRequest: function() {
            var deferred = $.Deferred();

            $.ajax({
                type: "GET",
                url: installmentPreCheckCheckoutUrl,
                success: function (response) {
                    if (response === 'true') {
                        deferred.resolve();
                        return;
                    }
                    deferred.reject(true);
                },
                error: function () {
                    deferred.reject(false);
                }
            });

            return deferred.promise();
        },

        /**
         * Returns the birthday of the selected payment "YYYY-MM-DD" formatted.
         *
         * @return {string}
         */
        getFormattedBirthday: function() {
            var me = this;
            var opts = me.opts;

            var $selectedPayment = me.getSelectedPaymentMethod();

            var day = $selectedPayment.find(opts.payolutionBirthdateDaySelector).val();
            var month = $selectedPayment.find(opts.payolutionBirthdateMonthSelector).val();
            var year = $selectedPayment.find(opts.payolutionBirthdateYearSelector).val();

            return year + '-' + month + '-' + day;
        },

        /**
         * Checks if all birthday fields are numeric.
         *
         * @return {boolean}
         */
        checkBirthday: function() {
            var me = this;
            var opts = me.opts;
            var regexNumeric = new RegExp('[0-9]+');
            var birthdateValid = true;

            var $selectedPayment = me.getSelectedPaymentMethod();

            $selectedPayment.find(opts.payolutionBirthdateSelector + ' select').each(function () {
                var selectValue = $(this).val();

                if (!regexNumeric.test(selectValue)) {
                    birthdateValid = false;
                }
            });

            return birthdateValid;
        },

        /**
         * Checks if the privacy checkbox of the current payment is checked.
         *
         * @return {boolean}
         */
        checkPrivacyCheckbox: function() {
            var me = this;
            var opts = me.opts;

            var $selectedPayment = me.getSelectedPaymentMethod();

            var privacyCheckbox = $selectedPayment.find(opts.payolutionPrivacyCheckboxSelector);

            return privacyCheckbox.is(':checked');
        },

        /**
         * Disables the second and the third column for payment installment.
         */
        enableInstallmentDetails: function () {
            var me = this;
            var opts = me.opts;

            me.hideInstallmentError();
            me.$payolutionInstallmentColumn2.removeClass(opts.isDisabledClass);
            me.$payolutionInstallmentColumn3.removeClass(opts.isDisabledClass);

        },

        /**
         * Enables the second and the third column for payment installment.
         */
        disableInstallmentDetails: function () {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentColumn2.addClass(opts.isDisabledClass);
            me.$payolutionInstallmentColumn3.addClass(opts.isDisabledClass);
        },

        /**
         * Shows the installment error column and hides column 2
         */
        showInstallmentError: function() {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentColumn2.addClass(opts.isHiddenClass);
            me.$payolutionInstallmentColumn2Error.removeClass(opts.isHiddenClass);

            me.disableInstallmentCheck();
            me.disableInstallmentDetails();
        },

        /**
         * Hides the installment error column and shows column 2
         */
        hideInstallmentError: function() {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentColumn2.removeClass(opts.isHiddenClass);
            me.$payolutionInstallmentColumn2Error.addClass(opts.isHiddenClass);
        },

        /**
         * Disables the second and the third column for payment installment.
         */
        enableInstallmentCheck: function () {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentColumn1.removeClass(opts.isDisabledClass);
        },

        /**
         * Enables the second and the third column for payment installment.
         */
        disableInstallmentCheck: function () {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentColumn1.addClass(opts.isDisabledClass);
        },

        /**
         * Shows the installment plan list by given duration.
         *
         * @param duration
         */
        showInstallmentPlanList: function(duration) {
            var me = this;
            var opts = me.opts;

            me.hideInstallmentPlanLists();

            me.$payolutionInstallmentPlanList
                .filter('[data-duration=' + duration + ']')
                .removeClass(opts.isHiddenClass);
        },

        /**
         * Hides all installment plan lists.
         */
        hideInstallmentPlanLists: function() {
            var me = this;
            var opts = me.opts;

            me.$payolutionInstallmentPlanList.addClass(opts.isHiddenClass);
        },

        /**
         * Checks if current payment is a payolution payment.
         *
         * @return {boolean}
         */
        isPayolutionPayment: function() {
            var me = this,
                $selectedPaymentMethod,
                opts = me.opts,
                $payolutionPaymentBlock;

            $selectedPaymentMethod = me.getSelectedPaymentMethod();

            if ($selectedPaymentMethod === null) {
                return false;
            }

            $payolutionPaymentBlock = $selectedPaymentMethod.find(opts.payolutionPaymentBlockSelector);

            return typeof $payolutionPaymentBlock.data(opts.payolutionPaymentBlockDataAttributePaymentMethod)  !== "undefined"
        },

        /**
         * Check if current selected payment method equals given payolution payment
         *
         * @return {boolean}
         */
        isCurrentPayment: function(paymentMethod) {
            var me = this,
                opts = me.opts,
                $selectedPaymentMethod,
                $payolutionPaymentBlock;


            paymentMethod = paymentMethod || undefined;

            if (paymentMethod === undefined) {
                return false;
            }

            $selectedPaymentMethod = me.getSelectedPaymentMethod();


            if ($selectedPaymentMethod === null) {
                return false;
            }

            $payolutionPaymentBlock = $selectedPaymentMethod.find(opts.payolutionPaymentBlockSelector);

            return $payolutionPaymentBlock.data(opts.payolutionPaymentBlockDataAttributePaymentMethod) === paymentMethod;
        },

        /**
         * Returns selected payment mean.
         *
         * @return {Object}
         */
        getSelectedPaymentMethod: function() {
            var me = this,
                opts = me.opts,
                $paymentMean = null;

            if (!me.isAccountForm()) {
                $paymentMean = $(opts.payolutionPaymentBlockInputSelectorShipping + ':checked')
            } else {
                $paymentMean = $(opts.payolutionPaymentBlockInputSelector + ':checked');
            }

            var $paymentMethod = $paymentMean.closest(opts.paymentMethodSelector);

            if ($paymentMethod.length === 0 || $paymentMethod.length > 1) {
                return null;
            }

            return $paymentMethod;
        },

        /**
         * Scrolls selected payment method into the view.
         */
        scrollToSelectedPayment: function() {
            var me = this;

            var $selectedPaymentMethod = me.getSelectedPaymentMethod();
            var offset = $selectedPaymentMethod.offset();

            $('html, body').animate({
                scrollTop: offset.top - 20,
                scrollLeft: offset.left - 20
            });
        },

        /**
         * Show selected payment method payolution container.
         */
        showPaymentMethod: function() {
            var me = this;
            var opts = me.opts;

            var $paymentMean = $(opts.payolutionPaymentBlockInputSelector + ':checked');

            $paymentMean.closest(opts.paymentMethodSelector)
                .find(opts.methodPayolutionSelector)
                .removeClass(opts.isHiddenClass);

            if (me.isCurrentPayment(opts.installmentKeyword)) {
                me.preselectFirstInstallmentSelectionColumn();
            }

            me.initModals();
        },

        /**
         * Hide method payolution containers.
         */
        hidePaymentMethods: function() {
            var me = this;
            var opts = me.opts;

            me.$paymentMethods
                .find(opts.methodPayolutionSelector)
                .addClass(opts.isHiddenClass);
        },

        /**
         * Removes all error messages.
         */
        removeAllErrorMessages: function () {
            var me = this;
            var opts = me.opts;

            me.$el.find(opts.errorMessageContainerSelector + '.' + opts.isRemovableClass).remove();
        },

        getOutlineErrorContainer: function (errorMessage) {
            var me = this;
            var opts = me.opts;

            var $container = $('<div class="block-group"></div>')
                .addClass(opts.errorMessageContainerClass)
                .addClass(opts.isRemovableClass);

            $container.append(
                $('<div class="block"></div>').html(errorMessage)
            );

            return $container;
        },

        addError: function($container, errorMessage) {
            var me = this;

            var $errorContainer = me.getOutlineErrorContainer(errorMessage);

            $errorContainer.insertBefore(
                $container
            );
        },

        addErrorBySelector: function(containerSelector, errorMessage) {
            var me = this;

            var $selectedPayment = me.getSelectedPaymentMethod();
            var $container = $selectedPayment.find(containerSelector);

            me.addError($container, errorMessage);
        },

        /**
         * Add error by field.
         */
        addErrorByField: function($field, errorMessage) {
            var me = this;

            var $container = $field.closest('.block-group');

            me.addError($container, errorMessage);
        },

        /**
         * Adds birthday error.
         */
        addBirthdayNot18Error: function () {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.payolutionFormBirthdateSelector,
                payolutionBirthdayErrorText18
            );
        },

        /**
         * Adds birthday error.
         */
        addBirthdayInvalidError: function () {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.payolutionFormBirthdateSelector,
                payolutionBirthdayErrorText
            );
        },

        addPrivacyCheckError: function () {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.payolutionFormPrivacyCheckSelector,
                payolutionSecurityErrorText
            );
        },

        addSepaMandateError: function () {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.payolutionFormSepaMandateSelector,
                payolutionMandateErrorText
            );
        },


        /**
         * Adds the empty field error message for the bank details holder field.
         */
        addBankDetailsHolderEmptyError: function() {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.bankDetailsHolderSelector,
                payolutionElvAccountFormHolderError
            );
        },

        /**
         * Adds the empty field error message for the bank details iban field.
         */
        addBankDetailsIbanInvalidError: function() {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.bankDetailsIbanSelector,
                payolutionElvAccountFormIbanError
            );
        },

        /**
         * Adds the empty field error message for the bank details bic field.
         */
        addBankDetailsBicEmptyError: function() {
            var me = this;
            var opts = me.opts;

            me.addErrorBySelector(
                opts.bankDetailsBicSelector,
                payolutionElvAccountFormBicError
            );
        },

        /**
         * Check if the form is on the account page
         */
        isAccountForm: function () {
            var me = this;

            return me.$el.attr('name') === 'frmRegister';
        },

        /**
         * Submits shipping payment form.
         */
        submitShippingPaymentForm: function () {
            var me = this;

            me.$el.submit();
        }
    });
})(jQuery, window);

window.StateManager.addPlugin('#shippingPaymentForm, form[name=frmRegister]', 'payolutionPayment');
