jQuery(document).ready(function ($) {
    // Elementor syncs a control into the edited element's model by listening for input
    // events on its input[data-setting]. jQuery's .val() sets the property without
    // firing one, so a range chosen here reached the field but never the model: the
    // editor saw no change, and the value was silently dropped on save. Every write to
    // the field goes through here so the event is never forgotten again.
    function setValue($input, value) {
        $input.val(value).trigger('input');
    }

    // The exact shape this control stores, which is also what the picker is configured to
    // read: two YYYY-MM-DD dates around daterangepicker's default ' - ' separator.
    var STORED_RANGE = /^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/;

    function initializeDatepickers($context) {
        var $root = $context && $context.length ? $context : $(document);
        var datepickerElements = $root.find('input[data-setting="daterange"]').addBack('input[data-setting="daterange"]');

        datepickerElements.each(function () {
            if ($(this).data('daterangepicker')) {
                return;
            }
            // Elementor renders the field with the saved range, and daterangepicker reads the
            // field on construction to preselect it. Clearing unconditionally, as this did,
            // blanked a stored range every time the panel opened: the range was still saved and
            // still applied to the feed, but the editor showed nothing. Only a value the picker
            // cannot read is dropped, so it never opens on a date nobody chose.
            //
            // This clear stays a plain .val(): it is not the user changing the setting, and
            // reporting it as one would write the blank back over the stored range.
            if (!STORED_RANGE.test(String($(this).val() || '').trim())) {
                $(this).val('');
            }
            $(this).daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
                opens: 'left'
            });

            // Apply is the only thing that writes the setting. daterangepicker would also
            // report a range through the callback argument, which it invokes from hide() --
            // including when the panel is dismissed by clicking away mid-selection. Listening
            // to both wrote the value twice for a single Apply, giving Elementor two identical
            // changes to record, and committed ranges the user never applied.
            $(this).on('apply.daterangepicker', function(ev, picker) {
                setValue($(this), picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            // Cancel deliberately has no handler. It means "discard what I just picked", and
            // daterangepicker already restores its own dates, so there is nothing to undo here.
            // Clearing the field on cancel, as this once did, now that the setting is really
            // written would delete a stored range for anyone who opened the picker and thought
            // better of it. A range is cleared by emptying the field.
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
