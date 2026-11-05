(() => {
    'use strict';
    if (window.dravenFilePickers) return;
    window.dravenFilePickers = true;
    const states = new WeakMap();
    const get = (root, name) => root.querySelector(`[data-file-picker-${name}]`);
    const state = root => {
        if (!states.has(root)) states.set(root, { version: 0, objectUrl: null });
        return states.get(root);
    };
    function reset(root) {
        const current = state(root);
        current.version++;
        if (current.objectUrl) URL.revokeObjectURL(current.objectUrl);
        current.objectUrl = null;
        get(root, 'preview').replaceChildren();
    }
    function preview(root, name, url) {
        const target = get(root, 'preview');
        target.replaceChildren();
        if (url) {
            const image = document.createElement('img');
            image.src = url; image.alt = ''; image.className = 'img-thumbnail'; image.style.maxHeight = '200px';
            target.append(image);
        }
        const caption = document.createElement('span'); caption.className = 'ms-2'; caption.textContent = name;
        target.append(caption);
    }
    async function select(root, id) {
        reset(root);
        const version = state(root).version;
        const upload = get(root, 'upload');
        if (upload) upload.value = '';
        get(root, 'id').value = id;
        const url = new URL(root.dataset.previewUrl, location.href);
        url.searchParams.set('filesStructureFilesId', id);
        if (root.dataset.fileType) url.searchParams.set('type', root.dataset.fileType);
        try {
            const response = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Preview failed');
            const file = await response.json();
            if (version === state(root).version) preview(root, file.name, file.preview);
        } catch {
            if (version === state(root).version) get(root, 'preview').textContent = root.dataset.errorLabel;
        }
        get(root, 'id').dispatchEvent(new Event('change', { bubbles: true }));
    }
    async function browse(root) {
        const modal = document.createElement('div');
        modal.className = 'modal fade'; modal.tabIndex = -1;
        modal.setAttribute('role', 'dialog'); modal.setAttribute('aria-label', get(root, 'browse').textContent);
        modal.innerHTML = '<div class="modal-dialog modal-lg"><div class="modal-content"></div></div>';
        document.body.append(modal);
        const instance = window.bootstrap.Modal.getOrCreateInstance(modal);
        const content = modal.querySelector('.modal-content');
        let version = 0;
        async function load(structureId) {
            const current = ++version;
            const url = new URL(root.dataset.browserUrl, location.href);
            if (root.dataset.fileType) url.searchParams.set('type', root.dataset.fileType);
            if (structureId) url.searchParams.set('structureId', structureId);
            try {
                const response = await fetch(url, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Browser failed');
                const html = await response.text();
                if (current === version) content.innerHTML = html;
            } catch { if (current === version) content.textContent = root.dataset.errorLabel; }
        }
        modal.addEventListener('click', event => {
            const item = event.target.closest('.fileitem');
            const folder = event.target.closest('.folder, .back');
            if (item) {
                event.preventDefault(); event.stopPropagation();
                select(root, item.dataset.filesid); instance.hide();
            } else if (folder) {
                event.preventDefault();
                load(folder.classList.contains('back') ? folder.dataset.backid : folder.dataset.structureid);
            }
        });
        modal.addEventListener('hidden.bs.modal', () => { version++; instance.dispose(); modal.remove(); }, { once: true });
        instance.show();
        await load(null);
    }
    // Delegation also supports controls introduced by AJAX and locale containers.
    document.addEventListener('click', event => {
        const button = event.target.closest('[data-file-picker-browse], [data-file-picker-clear]');
        if (!button || button.disabled) return;
        const root = button.closest('[data-file-picker]');
        if (!root) return;
        event.preventDefault();
        if (button.hasAttribute('data-file-picker-browse')) browse(root);
        else {
            reset(root); get(root, 'id').value = '';
            const upload = get(root, 'upload'); if (upload) upload.value = '';
            get(root, 'id').dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
    document.addEventListener('change', event => {
        const upload = event.target.closest('[data-file-picker-upload]');
        if (!upload) return;
        const root = upload.closest('[data-file-picker]'); reset(root);
        const file = upload.files[0];
        if (!file) return;
        get(root, 'id').value = '';
        if (file.type.startsWith('image/')) state(root).objectUrl = URL.createObjectURL(file);
        preview(root, file.name, state(root).objectUrl);
        get(root, 'id').dispatchEvent(new Event('change', { bubbles: true }));
    });
    document.addEventListener('reset', event => {
        setTimeout(() => event.target.querySelectorAll('[data-file-picker]').forEach(root => {
            const id = get(root, 'id').value;
            if (id) select(root, id); else reset(root);
        }), 0);
    });
})();
