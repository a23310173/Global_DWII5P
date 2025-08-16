// header-nav.js
(function () {
    const header = document.querySelector('header .header-container');
    const menu = header?.querySelector('.menu');
    if (!header || !menu) return;

    // Crea botón hamburguesa si no existe
    let btn = header.querySelector('.hamburger');
    if (!btn) {
        btn = document.createElement('button');
        btn.className = 'hamburger';
        btn.setAttribute('aria-label', 'Abrir menú');
        btn.setAttribute('aria-expanded', 'false');
        btn.innerHTML = '☰';
        header.appendChild(btn);
    }

    const ZOOM_THRESHOLD = 1.2;
    const WIDTH_FALLBACK = 1060;

    function getScaleApprox() {
        // Usa visualViewport si existe, si no, devicePixelRatio. 1 = 100%, 1.25 = 125%, etc.
        if (window.visualViewport && typeof window.visualViewport.scale === 'number') {
            return window.visualViewport.scale;
        }
        return window.devicePixelRatio || 1;
    }

    function applyCollapseState() {
        const scale = getScaleApprox();
        const shouldCollapse = (scale >= ZOOM_THRESHOLD) || (window.innerWidth <= WIDTH_FALLBACK);

        document.body.classList.toggle('nav-collapsed', !!shouldCollapse);

        // Si dejamos de estar colapsados, cierra el menú y resetea aria
        if (!shouldCollapse) {
            document.body.classList.remove('nav-open');
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    // Toggle del menú
    btn.addEventListener('click', () => {
        const isOpen = document.body.classList.toggle('nav-open');
        btn.setAttribute('aria-expanded', String(isOpen));
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!document.body.classList.contains('nav-collapsed')) return;
        if (header.contains(e.target)) return; // click dentro del header: permitido
        document.body.classList.remove('nav-open');
        btn.setAttribute('aria-expanded', 'false');
    });

    // Cerrar con Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
            document.body.classList.remove('nav-open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    // Recalcular en eventos relevantes
    window.addEventListener('resize', applyCollapseState);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', applyCollapseState);
    }
    applyCollapseState();
})();
