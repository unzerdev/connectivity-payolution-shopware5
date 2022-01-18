;(function ($, window, StateManager) {
    $.plugin('payolutionPaymentInstallment', {

        defaults: {
            //Classes
            installmentActive: 'payolutionRateActive',
            installmentLastELement: 'payolutionLastElement',
            installmentColumn: 'payolutionInstallmentSelection--payolutionInstallmentColumn',
            installmentPriceColumn: 'payolutionInstallmentColumnPrice--payolutionInstallmentColumnPriceColumn',
            installmentShortClass: 'installment--column',
            installmentOverviewClass: 'payolutionInstallmentOverview--element',
            installmentPlanList: 'payolutionInstallmentPlan--payolutionInstallmentPlanList',
            installmentPlanListLeft: 'payolutionInstallmentPlanList--left',
            installmentPlanListMiddle: 'payolutionInstallmentPlanList--middle',
            installmentPlanListRight: 'payolutionInstallmentPlanList--right',
            installmentOverview: 'payolutionInstallmentInfoModal--payolutionInstallmentOverview',
            installmentOverviewRates: 'payolutionInstallmentOverview--rates',
            installmentOverviewFinanceAmount: 'payolutionInstallmentOverview--financeAmount',
            installmentOverviewTotalAmount: 'payolutionInstallmentOverview--totalAmount',
            installmentOverviewNominal: 'payolutionInstallmentOverview--nominalZins',
            installmentOverviewEffective: 'payolutionInstallmentOverview--effectiveZins',
            installmentOverviewMonthlyAmount: 'payolutionInstallmentOverview--monthlyAmount',

            // Selector
            installmentPlanButton: '.payolutionInstallmentInfoModal--payolutionInstallmentButton',
            installmentSelection: '#payolutionInstallmentInfoModal--payolutionInstallmentSelection',
            installmentOverviews: '#payolutionInstallmentInfoModal--payolutionInstallmentOverview',
            installmentPlans: '#payolutionInstallmentPlan--payolutionInstallmentPlanListBox',
            installmentPlanContainer: '#payolutionInstallmentInfoModal--payolutionInstallmentPlan',
            installmentColumnSelector: '.installment--column',
            installmentPlanButtonSelector: '.payolutionInstallmentInfoModal--payolutionInstallmentButton',
            installmentSelectionsSelector: '#payolutionInstallmentInfoModal--payolutionInstallmentSelection',
            installmentOverViewsSelector: '#payolutionInstallmentInfoModal--payolutionInstallmentOverview',
            installmentPlansSelector: '#payolutionInstallmentPlan--payolutionInstallmentPlanListBox',
            installmentData: '.payolution-installment--detail',
            installmentPrice: '.payolutionInstallmentPrice'
        },

        /**
         * Init plugin.
         */
        init: function () {
            var me = this;

            me.registerEvents();
            me.fillInstallmentValue();
        },

        registerEvents: function () {
            var me = this;

            $.subscribe('plugin/swModal/onOpenAjax', $.proxy(me.onModalOpen, me));
        },

        /**
         * On Modal open
         *
         * @param event
         * @param modal
         *
         * @return void
         */
        onModalOpen: function (event, modal) {
            var me = this,
                $planButton = $(me.opts.installmentPlanButtonSelector);

            if (modal.options.additionalClass === 'payolution-installment--modal') {
                me.initInstallmentPlans();
                $planButton.click(function () {
                    me.onPlanButtonClick();
                });

                $(me.opts.installmentColumnSelector).click(function() {
                    me.onInstallmentSelection(this);
                });
            }
        },

        /**
         * On Installment Selection
         *
         * @param element
         *
         * @return void
         */
        onInstallmentSelection: function (element) {
            var me = this,
                $element = $(element),
                $elements = $(me.opts.installmentColumnSelector),
                duration = $element.data('duration');

            console.log('installment click with duration ' + duration);

            $elements.removeClass(me.opts.installmentActive);
            $element.addClass(me.opts.installmentActive);
            me.showOverview(duration);
            me.setDurationToButton(duration);
            me.hidePlan();
        },


        /**
         * Init Plans
         *
         * @return void
         */
        initInstallmentPlans: function () {
            var me = this,
                loopCounter = 0,
                installments = me.getInstallments(),
                selections = '',
                overviews = '',
                plans = '',
                defaultDuration = null;

            installments.forEach(function (installment) {
                var rates = installment['installments'],
                    additionalClass = '';

                if(loopCounter === 0) {
                    defaultDuration = installments.length;
                    additionalClass = me.opts.installmentActive;
                } else if (loopCounter === (installments.length - 1)) {
                    additionalClass = me.opts.installmentLastELement;
                }

                selections += me.createSelectionHtml(installment, additionalClass);
                overviews += me.createOverviewHtml(installment);
                plans += me.createPlanHtml(rates);

                loopCounter++;
            });

            $(me.opts.installmentSelection).html(selections);
            $(me.opts.installmentOverviews).html(overviews);
            $(me.opts.installmentPlans).html(plans);

            me.showOverview(defaultDuration);
            me.setDurationToButton(defaultDuration);
        },

        /**
         * Set Duration to Button
         *
         * @param duration
         *
         * @return void
         */
        setDurationToButton: function (duration) {
            var me = this,
                $planButton = $(me.opts.installmentPlanButton);

            console.log('set button duration ' + duration);

            $planButton.data('duration', duration);
        },

        getDurationFromButton: function () {
            var me = this,
                $planButton = $(me.opts.installmentPlanButton),
                duration = $planButton.data('duration');

            console.log('get duration from button ' + duration);

            return duration;
        },

        /**
         * Create Plan Html
         *
         * @param installmentRates
         * @return {string}
         */
        createPlanHtml: function (installmentRates) {
            var me = this,
                plan = '<ul class="' + me.opts.installmentPlanList + '" ' +
                    'data-duration="' + installmentRates.length + '" style="display: none;">';

            for(var k = 0; k < installmentRates.length; k++) {
                var date = '';
                if(installmentRates[k]['dueDate'].getDate() < 10) {
                    date += '0'
                }
                date += installmentRates[k]['dueDate'].getDate()+'.';
                if(installmentRates[k]['dueDate'].getMonth()+1 < 10) {
                    date += '0'
                }
                date += installmentRates[k]['dueDate'].getMonth()+1+'.'+installmentRates[k]['dueDate'].getUTCFullYear();
                plan += '<li><div class="' + me.opts.installmentPlanListLeft + '">'+(k+1)+''+planRate+'</div>'+
                    '<div class="' + me.opts.installmentPlanListMiddle + '">' + me.normalizePrice(installmentRates[k]['amount']) + ' ' + me.getCurrencySymbol() + '</div>'+
                    '<div class="' + me.opts.installmentPlanListRight + '">('+planPaymentTo+' '+date+')</div><div class="clearfix"></div></li>';
            }
            plan += '</ul>';

            return plan;
        },

        /**
         * Create Selection Html
         *
         * @param installment
         * @param additionalClass
         *
         * @return {string}
         */
        createSelectionHtml: function (installment, additionalClass) {
            var me = this,
                installments = installment['installments'];

            return '' +
                '<div ' +
                'class="' + me.opts.installmentColumn + ' ' + me.opts.installmentShortClass + ' ' + additionalClass + '"'
                + 'data-duration="' + installments.length +
                '">'+
                '<div' +
                'class="' + me.opts.installmentPriceColumn +
                '">' +
                me.normalizePrice(installment['installmentAmount']) +' ' + me.getCurrencySymbol() +' ' +
                selectionInstallmentsPerMonth + ' ' + installments.length + ' ' +
                selectionInstallments +
                '</div></div>';
        },

        /**
         * Create Overview Html
         *
         * @param installment
         *
         * @return {string}
         */
        createOverviewHtml: function (installment) {
            var me = this,
                installments = installment['installments'];

            return  '' +
                '<div class="' + me.opts.installmentOverview + '" data-duration="' + installments.length + '" class="display:none">'+
                '<div class="' + me.opts.installmentOverviewRates + ' ' + me.opts.installmentOverviewClass  + '"><div>'+overviewCountInstallments+'</div><div>'+installments.length+'</div></div>'+
                '<div class="' + me.opts.installmentOverviewFinanceAmount + ' ' + me.opts.installmentOverviewClass  + '"><div>'+overviewFinanceAmount+'</div><div>'+ me.normalizePrice(me.getArticlePrice()) +' '+ me.getCurrencySymbol()+'</div></div>'+
                '<div class="' + me.opts.installmentOverviewTotalAmount + ' ' + me.opts.installmentOverviewClass  + '"><div>'+overviewTotalAmount+'</div><div>'+ me.normalizePrice(installment['totalAmount']) +' '+me.getCurrencySymbol()+'</div></div>'+
                '<div class="' + me.opts.installmentOverviewNominal + ' ' + me.opts.installmentOverviewClass + ' "><div>'+overviewNominalZins+'</div><div>'+installment['interestRate'].toString().replace('.',',')+''+overviewNominalZinsProzentualSign+'</div></div>'+
                '<div class="' + me.opts.installmentOverviewEffective + ' ' + me.opts.installmentOverviewClass + '"><div>'+overviewEffectiveZins+'</div><div>'+installment['effectiveInterest'].toString().replace('.',',')+''+overviewEffectiveZinsProzentualSign+'</div></div>'+
                '<div class="' + me.opts.installmentOverviewMonthlyAmount + ' ' + me.opts.installmentOverviewClass + '"><div>'+overviewMonthlyInstallment+'</div><div>'+me.normalizePrice(installment['installmentAmount']) +' '+me.getCurrencySymbol()+'</div></div></div>';
        },

        /**
         * On Plan Button Click
         *
         * @return void
         */
        onPlanButtonClick: function () {
            var me = this,
                duration = me.getDurationFromButton(),
                $planButton = $(me.opts.installmentPlanButtonSelector);

            if($planButton.text().trim() === payolutionInstallmentInfoModalPlanButtonOpen) {
                me.showPlan(duration);
            } else {
                me.hidePlan();
            }
        },

        /**
         * Normalize Price
         *
         * @param amount
         *
         * @return {string}
         */
        normalizePrice: function (amount ) {
            return parseFloat(amount).toFixed(2).toString().replace('.',',');
        },

        /**
         * Show overview
         *
         * @param duration
         *
         * @return void
         */
        showOverview: function (duration) {
            var me = this,
                $overviewElements = $('.' + me.opts.installmentOverview),
                $overviewElement = $(me.opts.installmentOverviews);

            console.log('show overview ' + duration);

            $overviewElements.css('display', 'none');
            $overviewElement.find('[data-duration=' + duration + ']').css('display', 'inline-block');
        },

        /**
         * Show Plan
         *
         * @param duration
         *
         * @return void
         */
        showPlan: function (duration) {
            var me = this,
                $planButton = $(me.opts.installmentPlanButtonSelector),
                $planElement = $(me.opts.installmentPlans).find('[data-duration=' + duration + ']'),
                $planElements = $('.' + me.opts.installmentPlanList),
                $containerElement = $(me.opts.installmentPlanContainer);

            console.log('show plan');

            $planButton.text(payolutionInstallmentInfoModalPlanButtonClose);
            $planElements.css('display', 'none');
            $containerElement.css('display', '');
            $planElement.css('display', '');
        },

        /**
         * Hide Plan
         *
         * @return void
         */
        hidePlan: function () {
            var me = this,
                $planButton = $(me.opts.installmentPlanButtonSelector),
                $containerElement = $(me.opts.installmentPlanContainer),
                $planElements = $('.' + me.opts.installmentPlanList);

                console.log('hide plan');

            $planButton.text(payolutionInstallmentInfoModalPlanButtonOpen);
            $containerElement.css('display', 'none');
            $planElements.css('display', 'none');
        },

        /**
         * Fill Installment
         *
         * @return void
         */
        fillInstallmentValue: function () {
            var me = this,
                $priceObject = $(me.opts.installmentPrice),
                amounts = [];

            me.getInstallments().forEach(function (installment) {
                amounts.push(installment.installmentAmount);
            });

            $priceObject.text(String(Math.min.apply(Math, amounts)).replace('.', ','));
        },

        /**
         * Get Installments
         *
         * @return {Array}
         */
        getInstallments: function () {
            var me = this,
                durations = Payolution.getAvailableDurations(),
                currencyIso = me.getCurrencyIso(),
                price = me.getArticlePrice(),
                installments = [];

            for (var i = 0; i < durations.length; i++) {
                installments.push(Payolution.calculateInstallmentFixedCurrency(price, durations[durations.length - (i + 1)], currencyIso));
            }

            return installments;
        },

        /**
         * Get ArticlePrice
         *
         * @return {String}
         */
        getArticlePrice: function () {
            var me = this,
                installmentSelector = $(me.opts.installmentData);

            var price = installmentSelector.data('price');

            if ((Object.prototype.toString.call(price) !== '[object String]')
                && !!(price % 1)) {

                return;
            }

            return price.replace(',', '.');
        },

        /**
         * Get Currency Iso
         *
         * @return {String}
         */
        getCurrencyIso: function () {
            var me = this,
                installmentSelector = $(me.opts.installmentData);

            return installmentSelector.data('currency');
        },

        /**
         * Get Currency Symbol
         *
         * @return {String}
         */
        getCurrencySymbol: function () {
            var me = this,
                installmentSelector = $(me.opts.installmentData);

            return installmentSelector.data('currency-symbol');
        }
    });

    StateManager.addPlugin('.payolution-installment--detail, #payolutionInstallmentInfoModal', 'payolutionPaymentInstallment', {});
})(jQuery, window, StateManager);


