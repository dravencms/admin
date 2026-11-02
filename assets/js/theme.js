export function initializeThemeSwitcher() {
    const theme = window.DravencmsTheme;
    if (!theme) return;
    const icons = { system: 'fa-desktop', light: 'fa-sun', dark: 'fa-moon' };
    function update() {
        const preference = theme.getPreference();
        document.querySelectorAll('[data-theme-value]').forEach(button => {
            const checked = button.dataset.themeValue === preference;
            button.setAttribute('aria-checked', String(checked));
            button.classList.toggle('active', checked);
            button.querySelector('[data-theme-check]')?.classList.toggle('d-none', !checked);
        });
        document.querySelectorAll('[data-theme-icon]').forEach(icon => {
            icon.className = 'fa ' + icons[preference];
        });
    }
    document.addEventListener('click', event => {
        const button = event.target.closest('[data-theme-value]');
        if (button) theme.setPreference(button.dataset.themeValue);
    });
    document.addEventListener('dravencms:themechange', update);
    update();
}
