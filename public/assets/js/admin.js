(function () {
    'use strict';

    var sidebar = document.getElementById('sidebar');
    var burger = document.getElementById('sidebarToggle');

    if (burger && sidebar) {
        burger.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });
    }

    // Confirm destructive submits.
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Add / remove rows in crew, quote and content repeaters.
    document.querySelectorAll('[data-repeater-add]').forEach(function (button) {
        button.addEventListener('click', function () {
            var repeater = button.previousElementSibling;
            if (!repeater || !repeater.hasAttribute('data-repeater')) {
                return;
            }

            var rows = repeater.querySelectorAll('[data-repeater-row]');
            if (!rows.length) {
                return;
            }

            var clone = rows[rows.length - 1].cloneNode(true);
            clone.querySelectorAll('input').forEach(function (input) {
                input.value = '';
            });
            repeater.appendChild(clone);
        });
    });

    document.addEventListener('click', function (event) {
        var remove = event.target.closest('[data-repeater-remove]');
        if (!remove) {
            return;
        }

        var row = remove.closest('[data-repeater-row]');
        var repeater = row ? row.parentElement : null;

        if (row && repeater && repeater.querySelectorAll('[data-repeater-row]').length > 1) {
            row.remove();
        } else if (row) {
            row.querySelectorAll('input').forEach(function (input) {
                input.value = '';
            });
        }
    });

    // Picker tiles reflect the selected radio / checkbox.
    document.querySelectorAll('.picker').forEach(function (picker) {
        picker.addEventListener('change', function () {
            picker.querySelectorAll('.picker__item').forEach(function (item) {
                var input = item.querySelector('input');
                item.classList.toggle('is-selected', Boolean(input && input.checked));
            });
        });
    });

    // Flash messages fade out after a few seconds.
    window.setTimeout(function () {
        document.querySelectorAll('.flash').forEach(function (flash) {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            window.setTimeout(function () {
                flash.remove();
            }, 420);
        });
    }, 6000);
})();
