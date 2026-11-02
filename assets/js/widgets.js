import flatpickr from 'flatpickr';
import { Czech } from 'flatpickr/dist/l10n/cs.js';
import Select from 'tom-select';

export function initializeDateInput(input, grid = false) {
    if (input._flatpickr) return;
    const timeOnly = input.classList.contains('time');
    const withTime = timeOnly || input.classList.contains('datetime');
    const dateTokens = { yyyy: 'Y', yy: 'y', mm: 'm', m: 'n', dd: 'd', d: 'j' };
    const dateFormat = grid
        ? (input.dataset.dateFormat || 'yyyy-mm-dd').replace(/yyyy|yy|mm|dd|m|d/g, token => dateTokens[token])
        : (input.dataset.format || (timeOnly ? 'H:i' : withTime ? 'Y-m-d H:i' : 'Y-m-d'));
    flatpickr(input, {
        dateFormat,
        enableTime: withTime,
        noCalendar: timeOnly,
        time_24hr: true,
        allowInput: true,
        disableMobile: true,
        locale: document.documentElement.lang?.startsWith('cs') ? Czech : 'default'
    });
}

export function initializeWidgets(root = document) {
    root.querySelectorAll('.date, .datetime, .time').forEach(input => {
        if (input instanceof HTMLInputElement && !input.closest('[data-datagrid-name]')) initializeDateInput(input);
    });
    root.querySelectorAll('select').forEach(select => {
        if (select.closest('[data-datagrid-name]') || select.tomselect || select.hidden || select.dataset.nativeSelect !== undefined) return;
        new Select(select, { create: false, allowEmptyOption: true, plugins: select.multiple ? ['remove_button'] : [] });
    });
}
