(function () {
    'use strict';

    var vendorBase = document.currentScript.src.replace(/js\/form-controls\.js.*$/, 'vendor/');
    var loading = {};

    function loadLibrary(name, css, js) {
        if (!loading[name]) {
            loading[name] = new Promise(function (resolve, reject) {
                var link = document.createElement('link');
                var theme = document.querySelector('link[href*="css/theme.css"]');
                link.rel = 'stylesheet';
                link.href = vendorBase + css;
                document.head.insertBefore(link, theme);

                var script = document.createElement('script');
                script.src = vendorBase + js;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        return loading[name];
    }

    var selectSelector = 'select:not(.no-custom-select)';
    var pickerSelector = 'input[type="date"]:not(.no-custom-picker), ' +
        'input[type="time"]:not(.no-custom-picker), ' +
        'input[type="datetime-local"]:not(.no-custom-picker)';

    function selectPlaceholder(select) {
        var blankOption = Array.from(select.options).find(function (option) {
            return option.value === '';
        });

        return blankOption ? blankOption.text.trim() : null;
    }

    function enhanceSelects(root) {
        if (!window.jQuery || !root.querySelector(selectSelector)) {
            return;
        }

        if (!jQuery.fn.select2) {
            loadLibrary('select2', 'select2/select2.min.css', 'select2/select2.full.min.js').then(function () {
                enhanceSelects(root);
            });

            return;
        }

        root.querySelectorAll(selectSelector).forEach(function (select) {
            if (select.size > 1 || select.classList.contains('select2-hidden-accessible')) {
                return;
            }

            var options = {
                width: '100%',
                minimumResultsForSearch: select.options.length > 5 ? 0 : Infinity
            };
            var placeholder = selectPlaceholder(select);
            var modal = select.closest('.modal');
            var hasColorOptions = Array.from(select.options).some(function (option) {
                return option.dataset.color;
            });

            function renderOption(option) {
                if (!option.element || !option.element.dataset.color) {
                    return option.text;
                }

                var item = document.createElement('span');
                item.className = 'select-option-with-color';

                var swatch = document.createElement('span');
                swatch.className = 'select-option-color select-option-color-' + option.element.dataset.color;
                swatch.setAttribute('aria-hidden', 'true');

                item.appendChild(swatch);
                item.appendChild(document.createTextNode(option.text));

                return jQuery(item);
            }

            if (placeholder) {
                options.placeholder = placeholder;
                options.allowClear = !select.required;
            }

            if (modal) {
                options.dropdownParent = jQuery(modal);
            }

            if (hasColorOptions) {
                options.templateResult = renderOption;
                options.templateSelection = renderOption;
            }

            jQuery(select).select2(options);
        });
    }

    function pickerOptions(input) {
        var options = {
            allowInput: true,
            disableMobile: true
        };

        if (input.type === 'date') {
            options.dateFormat = 'Y-m-d';
        } else if (input.type === 'time') {
            options.enableTime = true;
            options.noCalendar = true;
            options.time_24hr = true;
            options.dateFormat = 'H:i';
        } else {
            options.enableTime = true;
            options.time_24hr = true;
            options.dateFormat = 'Y-m-d\\TH:i';
        }

        if (input.min) {
            options.minDate = input.min;
        }

        if (input.max) {
            options.maxDate = input.max;
        }

        return options;
    }

    function enhancePickers(root) {
        if (!root.querySelector(pickerSelector)) {
            return;
        }

        if (!window.flatpickr) {
            loadLibrary('flatpickr', 'flatpickr/flatpickr.min.css', 'flatpickr/flatpickr.min.js').then(function () {
                enhancePickers(root);
            });

            return;
        }

        root.querySelectorAll(pickerSelector).forEach(function (input) {
            if (!input._flatpickr) {
                window.flatpickr(input, pickerOptions(input));
            }
        });
    }

    function enhance(root) {
        enhanceSelects(root);
        enhancePickers(root);
    }

    document.addEventListener('DOMContentLoaded', function () {
        enhance(document);
    });

    document.addEventListener('shown.bs.modal', function (event) {
        enhance(event.target);
    });

    window.CZVRFormControls = {
        refresh: function (root) {
            enhance(root || document);
        }
    };
}());
