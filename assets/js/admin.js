import { initializeMutations, protectLegacyAjax } from './mutations.mjs';
import { initializeWidgets } from './widgets.js';
import Chart from 'chart.js/auto';
import './datagrid.js';
import { initializeThemeSwitcher } from './theme.js';
import 'summernote/dist/lang/summernote-cs-CZ.js';

initializeMutations();

function initializeAdmin() {
    initializeThemeSwitcher();
    const $ = window.jQuery;
    protectLegacyAjax($);
    initializeWidgets();
    $.nette.init();
    $.nette.ext('admin-widgets', { success: () => initializeWidgets() });
    $(document).ajaxComplete(() => initializeWidgets());
    $(document).on('click', '.filemanager .file-actions .delete[data-confirm]', function (event) {
        if (!window.confirm(this.dataset.confirm)) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    });
    $('#fileManagerModal').on('show.bs.modal', function (event) {
        const link = event.relatedTarget;
        if (link?.href) $(this).find('.modal-content').load(link.href);
    });
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
        if (element.title || element.dataset.bsTitle) window.bootstrap.Tooltip.getOrCreateInstance(element);
    });
    document.querySelectorAll('.helper, .about, .pop').forEach(element => {
        window.bootstrap.Popover.getOrCreateInstance(element, {
            content: element.dataset.content || '',
            trigger: element.classList.contains('helper') ? 'focus' : element.classList.contains('about') ? 'hover focus' : 'click'
        });
    });
    $(document).on('click', 'a[data-gallery]', function (event) {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        const links = Array.from(document.querySelectorAll('a[data-gallery]')).filter(link => link.dataset.gallery === this.dataset.gallery);
        window.blueimp.Gallery(links, { index: this, event });
    });
    document.querySelectorAll('.wysiwyg').forEach(element => {
        $(element).summernote({
            lang: document.documentElement.lang?.startsWith('cs') ? 'cs-CZ' : 'en-US',
            height: 240,
            dialogsInBody: true,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'video']],
                ['view', ['fullscreen', 'codeview', 'undo', 'redo']]
            ],
            callbacks: {
                onPaste(event) {
                    const text = (event.originalEvent || event).clipboardData?.getData('Text');
                    if (text !== undefined) {
                        event.preventDefault();
                        $(element).summernote('pasteHTML', $('<div>').text(text).html());
                    }
                }
            }
        });
    });
    document.querySelectorAll('.codemirror').forEach(element => {
        window.CodeMirror.fromTextArea(element, { lineNumbers: true, mode: element.dataset.codemirrorMode || 'htmlmixed' });
    });
    document.querySelectorAll('.graph').forEach(element => {
        const data = JSON.parse(element.dataset.data || '[]');
        const canvas = document.createElement('canvas');
        element.append(canvas);
        new Chart(canvas, {
            type: 'line',
            data: { labels: data.map(row => row.period), datasets: [{ label: window.Tools.translate('amount'), data: data.map(row => row.amount), borderColor: '#206bc4', backgroundColor: '#206bc420', fill: true }] },
            options: { responsive: true }
        });
    });
    document.querySelectorAll('.selfpost').forEach(element => {
        element.addEventListener('change', () => document.querySelector(element.dataset.target)?.requestSubmit());
    });
    $(document).on('change', '.invalidator', function () {
        const input = $(this);
        $.nette.ajax({ url: input.data('invalidate-url'), type: 'GET', data: { [input.data('invalidate-parameter') || 'value']: input.val() } });
    });
    document.querySelectorAll('.nav-tabs a[data-bs-toggle="tab"]').forEach(link => {
        if (link.hash === location.hash) window.bootstrap.Tab.getOrCreateInstance(link).show();
        link.addEventListener('shown.bs.tab', () => history.replaceState(null, '', link.hash));
    });
    document.getElementById('forgot-password-link')?.addEventListener('click', event => {
        event.preventDefault();
        const reset = document.getElementById('password-reset-form');
        reset.hidden = false;
        reset.querySelector('input[type="email"]')?.focus();
    });
    document.querySelector('[data-password-reset-close]')?.addEventListener('click', () => {
        document.getElementById('password-reset-form').hidden = true;
    });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeAdmin);
else initializeAdmin();
