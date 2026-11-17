document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-availability-editor]').forEach((editor) => {
        const rows = editor.querySelector('[data-availability-rows]');
        const template = editor.querySelector('[data-availability-template]');
        const addButton = editor.querySelector('[data-add-availability]');

        if (!rows || !template || !addButton) {
            return;
        }

        const reindex = () => {
            rows.querySelectorAll('[data-availability-row]').forEach((row, index) => {
                row.querySelectorAll('[name]').forEach((field) => {
                    field.name = field.name.replace(/availability\[\d+\]/, `availability[${index}]`);
                });
                row.querySelectorAll('[id]').forEach((field) => {
                    field.id = field.id
                        .replace(/Day\d+(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)$/, `Day${index}$1`)
                        .replace(/(Start|End)\d+$/, `$1${index}`);
                });
                row.querySelectorAll('label[for]').forEach((label) => {
                    label.htmlFor = label.htmlFor
                        .replace(/Day\d+(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)$/, `Day${index}$1`)
                        .replace(/(Start|End)\d+$/, `$1${index}`);
                });
            });
        };

        addButton.addEventListener('click', () => {
            const index = rows.querySelectorAll('[data-availability-row]').length;
            rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index)));
            const newRow = rows.querySelector('[data-availability-row]:last-child');
            window.CZVRFormControls?.refresh(newRow);
            newRow?.querySelector('input[type="checkbox"]')?.focus();
        });

        rows.addEventListener('click', (event) => {
            const presetButton = event.target.closest('[data-days-preset]');

            if (presetButton) {
                const presets = {
                    weekdays: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    weekend: ['saturday', 'sunday'],
                    all: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                };
                const selectedDays = presets[presetButton.dataset.daysPreset] || [];
                presetButton.closest('[data-availability-row]')
                    ?.querySelectorAll('input[type="checkbox"]')
                    .forEach((checkbox) => {
                        checkbox.checked = selectedDays.includes(checkbox.value);
                    });

                return;
            }

            const removeButton = event.target.closest('[data-remove-availability]');

            if (!removeButton || rows.querySelectorAll('[data-availability-row]').length === 1) {
                return;
            }

            removeButton.closest('[data-availability-row]')?.remove();
            reindex();
        });
    });
});
