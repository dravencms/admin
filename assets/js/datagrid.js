import naja from 'naja';
import Select from 'tom-select';
import {
    AutosubmitPlugin, CheckboxPlugin, DatepickerPlugin, EditablePlugin,
    InlinePlugin, ItemDetailPlugin, NetteFormsPlugin, SelectpickerPlugin,
    SortableJS, SortablePlugin, TomSelect, TreeViewPlugin,
    createDatagrids
} from '@contributte/datagrid/assets/index.ts';
import { NajaAjax } from '@contributte/datagrid/assets/ajax/index.ts';
import { initializeDateInput } from './widgets.js';

function prepareGrids() {
    document.querySelectorAll('[data-datagrid-name] .ajax').forEach(element => {
        element.setAttribute('data-dravencms-grid-ajax', '');
        window.jQuery(element).off('.nette');
    });
}

function initializeGrids() {
    if (!document.querySelector('[data-datagrid-name]')) {
        return;
    }
    // Existing admin controls keep nette.ajax; Naja exclusively handles grid controls.
    prepareGrids();
    const legacy = window.jQuery.nette.ext('init');
    legacy.linkSelector = 'a.ajax:not([data-dravencms-grid-ajax])';
    legacy.formSelector = 'form.ajax:not([data-dravencms-grid-ajax])';
    legacy.buttonSelector = 'input.ajax[type="submit"]:not([data-dravencms-grid-ajax]), button.ajax[type="submit"]:not([data-dravencms-grid-ajax]), input.ajax[type="image"]:not([data-dravencms-grid-ajax])';
    naja.uiHandler.selector = '.ajax[data-dravencms-grid-ajax]';
    naja.defaultOptions.history = false;
    naja.formsHandler.netteForms = window.Nette;
    naja.snippetHandler.addEventListener('afterUpdate', prepareGrids);
    naja.initialize();
    document.addEventListener('click', event => {
        const element = event.target.closest('[data-datagrid-name] [data-datagrid-confirm]');
        if (element && !window.confirm(element.getAttribute('data-datagrid-confirm'))) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
    window.dravencmsDatagrids = createDatagrids(new NajaAjax(naja), {
        datagrid: {
            plugins: [
                new AutosubmitPlugin(), new CheckboxPlugin(), new EditablePlugin(),
                new InlinePlugin(), new ItemDetailPlugin(), new NetteFormsPlugin(window.Nette),
                new SortablePlugin(new SortableJS()),
                new DatepickerPlugin({
                    initDatepickers(elements) {
                        elements.forEach(input => initializeDateInput(input, true));
                    }
                }),
                new SelectpickerPlugin(new TomSelect(Select)), new TreeViewPlugin()
            ]
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeGrids);
} else {
    initializeGrids();
}
