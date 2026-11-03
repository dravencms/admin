export function resolveAclSelection(explicit, graph) {
    const effective = new Set();
    const requiredBy = new Map();
    function visit(id, parent, path = new Set()) {
        if (!Object.prototype.hasOwnProperty.call(graph, id)) throw new Error(`Unknown permission ${id}`);
        if (path.has(id)) throw new Error(`Permission dependency cycle at ${id}`);
        effective.add(id);
        if (parent !== id) {
            if (!requiredBy.has(id)) requiredBy.set(id, new Set());
            requiredBy.get(id).add(parent);
        }
        const next = new Set(path).add(id);
        for (const dependency of graph[id]) visit(dependency, parent, next);
    }
    for (const id of explicit) visit(id, id);
    return {effective, requiredBy};
}

export function initializeAclPickers(root = document) {
    root.querySelectorAll('[data-acl-picker]').forEach(picker => {
        if (picker.dataset.aclInitialized) return;
        const graph = JSON.parse(picker.dataset.aclGraph);
        const labels = JSON.parse(picker.dataset.aclLabels);
        const initial = JSON.parse(picker.dataset.aclExplicit);
        let explicit = new Set(initial);
        function render() {
            const {effective, requiredBy} = resolveAclSelection(explicit, graph);
            picker.querySelectorAll('[data-acl-row]').forEach(row => {
                const id = row.dataset.aclRow;
                const input = row.querySelector('[data-acl-permission]');
                const implied = effective.has(id) && !explicit.has(id);
                input.checked = effective.has(id);
                input.disabled = implied;
                const reasons = [...(requiredBy.get(id) || [])].map(key => labels[key]);
                const hint = row.querySelector('[data-acl-required]');
                hint.textContent = reasons.length ? picker.dataset.aclRequiredLabel.replace('%permissions%', reasons.join(', ')) : '';
                hint.hidden = reasons.length === 0;
                row.querySelector('[data-acl-keep]').hidden = !implied;
            });
        }
        picker.addEventListener('change', event => {
            const input = event.target;
            if (!input.matches('[data-acl-permission]')) return;
            if (input.checked) explicit.add(input.dataset.aclPermission);
            else explicit.delete(input.dataset.aclPermission);
            render();
        });
        picker.addEventListener('click', event => {
            const button = event.target.closest('[data-acl-keep]');
            if (!button) return;
            explicit.add(button.dataset.aclKeep);
            render();
        });
        picker.closest('form')?.addEventListener('reset', () => {
            queueMicrotask(() => { explicit = new Set(initial); render(); });
        });
        picker.dataset.aclInitialized = 'true';
        render();
    });
}
