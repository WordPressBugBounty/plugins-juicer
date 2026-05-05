jQuery(document).ready(function ($) {
    function initializeDatepickers($context) {
        var $root = $context && $context.length ? $context : $(document);
        var datepickerElements = $root.find('input[data-setting="daterange"]').addBack('input[data-setting="daterange"]');

        datepickerElements.each(function () {
            if ($(this).data('daterangepicker')) {
                return;
            }
            $(this).val('');
            $(this).daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
                opens: 'left'
            }, function(start, end, label) {
                $(this.element).val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            });

            $(this).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $(this).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    }

    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction('frontend/element_ready/juicer_widget.default', function ($scope) {
                initializeDatepickers($scope);
            });
        });
    }

    if (typeof elementor !== 'undefined' && elementor.hooks) {
        elementor.hooks.addAction('panel/open_editor/widget/juicer_widget', function (panel, model, view) {
            initializeDatepickers(panel.$el || $(document));
        });
    }
});
