jQuery(document).ready(function ($) {
    function initializeDatepickers() {
        var datepickerElements = $('input[data-setting="daterange"]');

        datepickerElements.each(function () {
            $(this).val(''); // Ensure the field is empty by default
            $(this).daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false, // Prevent automatic update of the input value
                opens: 'left'
            }, function(start, end, label) {
                $(this.element).val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            });

            // Update the input field manually on apply
            $(this).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            // Clear the input field on cancel
            $(this).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    }

    // Initialize datepickers on document ready
    initializeDatepickers();

    // Ensure datepickers are initialized when Elementor editor is ready
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            initializeDatepickers();
        });
    });

    // Ensure datepickers are initialized when Elementor controls are changed
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        initializeDatepickers();
    });
});
