const mutationSignals = new Set(['delete', 'up', 'down', 'filedelete', 'structuredelete', 'structuresave', 'edit', 'changestatus', 'actioncallback']);

export function isMutationUrl(href, base) {
    const url = new URL(href, base);
    if (url.origin !== new URL(base).origin) return false;
    const signal = url.searchParams.get('do')?.split('-').pop()?.toLowerCase();
    return mutationSignals.has(signal);
}

function token() { return document.querySelector('meta[name="admin-mutation-token"]')?.content; }

export function protectNaja(naja) {
    const original = naja.makeRequest.bind(naja);
    naja.makeRequest = (method, href, data, options = {}) => {
        const url = new URL(href, location.href);
        if (url.origin === location.origin) {
            if (isMutationUrl(url, location.href)) method = 'POST';
            if (method.toUpperCase() === 'POST' && token()) {
                const headers = new Headers(options.fetch?.headers || {});
                headers.set('X-Admin-CSRF', token());
                options = {...options, fetch: {...options.fetch, headers}};
            }
        }
        return original(method, href, data, options);
    };
}

export function initializeMutations() {
    // Capture before the two AJAX libraries and their confirmation handlers.
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || (!link.hasAttribute('data-admin-mutation') && !isMutationUrl(link.href, location.href))) return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin) return;
        event.preventDefault(); event.stopImmediatePropagation();
        const isDelete = /delete$/i.test(url.searchParams.get('do') || '');
        const confirmation = link.dataset.datagridConfirm || link.dataset.confirm || (isDelete ? window.Tools.translate('confirmDelete') : null);
        if (confirmation && !window.confirm(confirmation)) return;
        const form = document.createElement('form');
        form.method = 'POST'; form.action = url.href;
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = '_adminMutationToken'; input.value = token() || '';
        form.append(input); document.body.append(form); form.submit();
    }, true);
    document.addEventListener('submit', event => {
        if (event.submitter?.name.includes('group_action') && !window.confirm(window.Tools.translate('confirmBulkAction'))) {
            event.preventDefault(); event.stopImmediatePropagation();
        }
    }, true);
}

export function protectLegacyAjax($) {
    $.ajaxPrefilter(options => {
        const url = new URL(options.url || location.href, location.href);
        if (url.origin !== location.origin) return;
        if (isMutationUrl(url, location.href)) options.type = 'POST';
        if (options.type?.toUpperCase() === 'POST' && token()) {
            options.headers = {...options.headers, 'X-Admin-CSRF': token()};
        }
    });
}
