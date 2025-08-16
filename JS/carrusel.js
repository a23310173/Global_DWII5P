// carrusel.js
(function () {
    const root = document.getElementById('carrusel-servicios');
    if (!root) return;

    const track = root.querySelector('.carrusel-track');
    const items = Array.from(root.querySelectorAll('.carrusel-item'));
    const prevBtn = root.querySelector('.carrusel-prev');
    const nextBtn = root.querySelector('.carrusel-next');
    let index = 0;

    function update() {
        const offset = -index * 100;
        track.style.transform = `translateX(${offset}%)`;
    }

    function next() {
        index = (index + 1) % items.length;
        update();
    }

    function prev() {
        index = (index - 1 + items.length) % items.length;
        update();
    }

    // Eventos botones
    nextBtn.addEventListener('click', next);
    prevBtn.addEventListener('click', prev);

    // Pase automático cada 5 segundos
    setInterval(next, 5000);

    // Configuración inicial sin CSS
    track.style.display = 'flex';
    track.style.width = `${items.length * 100}%`;
    items.forEach(item => {
        item.style.flex = '0 0 100%';
    });

    update();
})();
