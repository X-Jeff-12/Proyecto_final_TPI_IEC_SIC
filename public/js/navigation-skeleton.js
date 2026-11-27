(() => {
    const main = document.getElementById('app-content');
    const skeleton = document.getElementById('navigation-skeleton');
    const body = skeleton.querySelector('.skeleton-body');
    const status = document.getElementById('navigation-status');
    const topbar = document.querySelector('.topbar');
    const menu = document.getElementById('menu-toggle');

    function destinationKey(url) {
        return url.origin + url.pathname.replace(/\/+$/, '');
    }

    const destinations = new Map(
        Array.from(document.querySelectorAll('nav a[data-loading-page]'), link => [
            destinationKey(new URL(link.href)),
            link,
        ]),
    );

    const repeat = (count, render) => Array.from({ length: count }, (_, index) => render(index)).join('');
    const shape = (size = '') => `<span class="skeleton-shape ${size}"></span>`;
    const heading = () => shape('skeleton-line skeleton-section-title');
    const actions = (count = 1) => `<div class="skeleton-actions">${repeat(count, () => shape('skeleton-button'))}</div>`;
    const panel = content => `<div class="skeleton-panel">${content}</div>`;
    const notice = () => `<div class="skeleton-notice">${shape('skeleton-line')}</div>`;

    function table(columns, rows = 5, editable = false) {
        const row = (className, cell) => `<div class="skeleton-table-row ${className}">${repeat(columns, () => shape(cell))}</div>`;
        return `
            <div class="skeleton-table" style="--skeleton-columns:${columns}">
                ${row('skeleton-table-head', 'skeleton-line')}
                ${repeat(rows, () => row('', editable ? 'skeleton-input' : 'skeleton-line'))}
            </div>`;
    }

    function metrics(count, dashboard = false) {
        const colors = ['blue', 'violet', 'green', 'gold'];
        const grid = dashboard ? 'dashboard-metrics' : 'skeleton-metrics';
        return `
            <div class="${grid}">
                ${repeat(count, index => `
                    <div class="dashboard-metric metric-${colors[index % colors.length]}">
                        ${shape('skeleton-line')}${shape('skeleton-amount')}
                    </div>`)}
            </div>`;
    }

    function fields(types) {
        return `
            <div class="skeleton-fields">
                ${types.map(type => `
                    <div class="skeleton-field ${['textarea', 'wide'].includes(type) ? 'skeleton-field-wide' : ''}">
                        ${shape('skeleton-line skeleton-field-label')}
                        ${shape(type === 'textarea' || type === 'multiple' ? 'skeleton-input skeleton-textarea' : 'skeleton-input')}
                    </div>`).join('')}
            </div>`;
    }

    const shortFields = count => fields(Array(count).fill('text'));
    const textPanel = () => panel(heading() + shape('skeleton-line') + shape('skeleton-line skeleton-short-line'));
    const detailPanels = count => repeat(count, () => panel(heading()));

    function dashboard() {
        return `
            <div class="dashboard-hero skeleton-dashboard-hero">
                ${shape('skeleton-title')}
                ${shape('skeleton-subtitle')}
                ${shape('skeleton-line')}
                <div class="skeleton-actions">${repeat(3, () => shape('skeleton-chip'))}</div>
            </div>
            ${metrics(4, true)}
            <div class="dashboard-panels">
                <div class="dashboard-panel">
                    ${heading()}
                    <div class="skeleton-chart">${repeat(2, () => shape('skeleton-chart-bar'))}</div>
                </div>
                <div class="dashboard-panel">
                    ${heading()}
                    <div class="dashboard-shortcuts">${repeat(6, () => shape('skeleton-shortcut'))}</div>
                </div>
            </div>`;
    }

    function catalog(link) {
        const types = link.dataset.loadingFields.split(',');
        const sources = Number(link.dataset.loadingSources);
        const sourcesPanel = sources ? panel(heading() + fields(Array(sources).fill('multiple'))) : '';
        return panel(heading() + fields(types) + sourcesPanel + actions()) + heading() + table(7);
    }

    const layouts = {
        diagnostico: () => metrics(4) + panel(heading() + table(2)) + textPanel(),
        diario: () => panel(heading() + fields(['text', 'text', 'text', 'text', 'wide']) + table(4, 8, true) + actions()),
        mayor: () => detailPanels(7),
        balanza: () => table(3, 8) + textPanel(),
        hoja: () => table(11, 8) + textPanel(),
        estados: () => notice() + `<div class="skeleton-pair">${repeat(2, () => panel(heading() + table(2, 4) + table(2, 3)))}</div>`,
        caja: () => metrics(1) + table(6, 8),
        costos: () => metrics(4) + table(9, 4) + panel(heading() + shortFields(3) + actions()),
        evaluacion: () => notice() + panel(heading() + metrics(4) + heading() + table(3)),
        comparar: () => notice() + table(8, 3) + textPanel(),
        'activos.depreciacion': () => actions() + repeat(2, () => panel(heading() + shape('skeleton-line') + detailPanels(3))),
        sensibilidad: () => panel(heading() + metrics(4) + heading() + table(7)),
        reemplazo: () => notice() + panel(heading() + metrics(4) + heading() + table(7, 3)),
        verificacion: () => panel(heading() + fields(['text', 'text', 'text', 'text', 'textarea']) + actions() + table(4, 4)),
        'valor.tiempo': () => panel(shortFields(4) + actions()) + metrics(6) + heading() + table(7),
        fuentes: () => panel(heading() + fields(['text', 'text', 'text', 'text', 'textarea', 'wide']) + actions()) + table(8),
        empresa: () => panel(fields(['text', 'text', 'text', 'wide', 'wide', ...Array(16).fill('text')]) + heading() + fields(['multiple', 'multiple']) + actions()),
        reportes: () => actions(2) + textPanel() + panel(heading() + metrics(4) + shape('skeleton-line')) + detailPanels(3),
        historial: () => table(3, 9) + actions(2),
    };

    function render(link) {
        const page = link.dataset.loadingPage;
        if (page === 'dashboard') return dashboard();

        const content = page === 'catalogo' ? catalog(link) : layouts[page]?.() ?? table(4);
        return `
            <div class="skeleton-module-heading">
                ${shape('skeleton-title')}
                ${shape('skeleton-line')}
            </div>
            ${content}`;
    }

    let showTimer;
    let resetTimer;
    let pendingLink;

    function positionSkeleton() {
        if (skeleton.hidden) return;
        const top = Math.max(0, main.getBoundingClientRect().top, topbar.getBoundingClientRect().bottom);
        skeleton.style.setProperty('--skeleton-top', `${top}px`);
    }

    function clearLoading() {
        clearTimeout(showTimer);
        clearTimeout(resetTimer);
        pendingLink?.classList.remove('is-loading');
        pendingLink = undefined;
        skeleton.hidden = true;
        main.removeAttribute('aria-busy');
        status.textContent = '';
    }

    function showLoading(link) {
        pendingLink?.classList.remove('is-loading');
        pendingLink = link;
        pendingLink.classList.add('is-loading');
        body.innerHTML = render(link);
        menu.checked = false;
        skeleton.scrollTop = 0;
        skeleton.hidden = false;
        positionSkeleton();
        main.setAttribute('aria-busy', 'true');
        status.textContent = `Cargando ${link.textContent.trim()}…`;
    }

    function startLoading(link) {
        if (!link) return;
        clearTimeout(showTimer);
        clearTimeout(resetTimer);
        if (skeleton.hidden) {
            showTimer = setTimeout(() => showLoading(link), 120);
        } else {
            showLoading(link);
        }
        // Restores the current page if the browser stops a request without replacing it.
        resetTimer = setTimeout(clearLoading, 45000);
    }

    const opensHere = target => !target || target.toLowerCase() === '_self';

    window.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest?.('a[href]');
        if (!link || !opensHere(link.target) || link.hasAttribute('download')) return;

        const url = new URL(link.href);
        const current = new URL(window.location.href);
        if (url.origin !== current.origin) return;
        if (link.getAttribute('href').includes('#') && url.pathname === current.pathname && url.search === current.search) return;
        startLoading(destinations.get(destinationKey(url)));
    });

    window.addEventListener('submit', event => {
        if (event.defaultPrevented) return;
        const form = event.target;
        const submitter = event.submitter;
        const method = submitter?.getAttribute('formmethod') || form.method;
        const target = submitter?.getAttribute('formtarget') || form.target;
        if (method.toLowerCase() !== 'get' || !opensHere(target)) return;

        const action = submitter?.getAttribute('formaction') ?? form.action;
        const url = new URL(action, document.baseURI);
        if (url.origin !== window.location.origin) return;
        startLoading(destinations.get(destinationKey(url)));
    });

    window.addEventListener('pageshow', clearLoading);
    window.addEventListener('popstate', clearLoading);
    window.addEventListener('resize', positionSkeleton);
    window.addEventListener('scroll', positionSkeleton, { passive: true });
    window.addEventListener('keydown', event => {
        if (event.key === 'Escape') clearLoading();
    });
})();
