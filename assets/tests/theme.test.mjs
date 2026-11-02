import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import {runInNewContext} from 'node:vm';

const source = readFileSync(new URL('../../src/Dravencms/AdminModule/templates/theme-init.latte', import.meta.url), 'utf8');
const script = source.match(/<script[^>]*>([\s\S]*?)<\/script>/)[1];
const key = 'dravencms.admin.theme';

function createPage({dark = false, saved, blocked = false} = {}) {
    const storage = new Map(saved === undefined ? [] : [[key, saved]]);
    const media = new EventTarget();
    media.matches = dark;
    const window = new EventTarget();
    window.matchMedia = () => media;
    Object.defineProperty(window, 'localStorage', {get() {
        if (blocked) throw new Error('Storage unavailable');
        return {
            getItem: name => storage.get(name) ?? null,
            setItem: (name, value) => storage.set(name, value),
            removeItem: name => storage.delete(name)
        };
    }});
    const document = new EventTarget();
    document.documentElement = {dataset: {}, style: {}};
    runInNewContext(script, {window, document, CustomEvent});
    return {window, root: document.documentElement, storage, theme: window.DravencmsTheme, media};
}

test('system is the default and is applied immediately', () => {
    for (const dark of [false, true]) {
        const page = createPage({dark});
        assert.equal(page.theme.getPreference(), 'system');
        assert.equal(page.root.dataset.bsTheme, dark ? 'dark' : 'light');
        assert.equal(page.root.style.colorScheme, page.root.dataset.bsTheme);
        assert.equal(page.storage.has(key), false);
    }
});

test('system changes are followed only in system mode', () => {
    const page = createPage();
    page.media.matches = true;
    page.media.dispatchEvent(new Event('change'));
    assert.equal(page.root.dataset.bsTheme, 'dark');
    page.theme.setPreference('light');
    page.media.dispatchEvent(new Event('change'));
    assert.equal(page.root.dataset.bsTheme, 'light');
    assert.equal(page.storage.get(key), 'light');
    page.theme.setPreference('system');
    assert.equal(page.root.dataset.bsTheme, 'dark');
    assert.equal(page.storage.has(key), false);
});

test('saved choices override the system and invalid choices fall back to system', () => {
    assert.equal(createPage({dark: true, saved: 'light'}).root.dataset.bsTheme, 'light');
    assert.equal(createPage({saved: 'dark'}).root.dataset.bsTheme, 'dark');
    assert.equal(createPage({dark: true, saved: 'invalid'}).theme.getPreference(), 'system');
});

test('storage changes synchronize other tabs and ignore unrelated keys', () => {
    const page = createPage({dark: true});
    page.window.dispatchEvent(Object.assign(new Event('storage'), {key, newValue: 'light'}));
    assert.equal(page.root.dataset.bsTheme, 'light');
    page.window.dispatchEvent(Object.assign(new Event('storage'), {key: 'other', newValue: 'dark'}));
    assert.equal(page.root.dataset.bsTheme, 'light');
    page.window.dispatchEvent(Object.assign(new Event('storage'), {key, newValue: null}));
    assert.equal(page.theme.getPreference(), 'system');
    assert.equal(page.root.dataset.bsTheme, 'dark');
});

test('blocked storage does not prevent system defaults or in-page switching', () => {
    const page = createPage({dark: true, blocked: true});
    assert.equal(page.root.dataset.bsTheme, 'dark');
    assert.doesNotThrow(() => page.theme.setPreference('light'));
    assert.equal(page.root.dataset.bsTheme, 'light');
    page.theme.setPreference('system');
    assert.equal(page.root.dataset.bsTheme, 'dark');
});
