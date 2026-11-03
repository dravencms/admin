import {test} from 'node:test';
import assert from 'node:assert/strict';
import {resolveAclSelection} from '../js/acl-permissions.mjs';

const graph = {
    'file.read': [],
    'file.upload': ['file.read'],
    'gallery.edit': ['file.upload'],
    'article.edit': ['file.read']
};
test('transitive prerequisites are enabled without mutating explicit selections', () => {
    const explicit = new Set(['gallery.edit']);
    const state = resolveAclSelection(explicit, graph);
    assert.deepEqual([...state.effective], ['gallery.edit', 'file.upload', 'file.read']);
    assert.deepEqual([...explicit], ['gallery.edit']);
    assert.deepEqual([...state.requiredBy.get('file.read')], ['gallery.edit']);
});
test('shared prerequisites survive while another selected permission needs them', () => {
    assert.deepEqual([...resolveAclSelection(new Set(['article.edit']), graph).effective], ['article.edit', 'file.read']);
    assert.deepEqual([...resolveAclSelection(new Set(), graph).effective], []);
});
test('independent assignments survive removing their parent permission', () => {
    assert.deepEqual([...resolveAclSelection(new Set(['file.read']), graph).effective], ['file.read']);
});
test('unknown IDs and dependency cycles fail', () => {
    assert.throws(() => resolveAclSelection(new Set(['missing.permission']), graph));
    assert.throws(() => resolveAclSelection(new Set(['a']), {a: ['b'], b: ['a']}));
});
