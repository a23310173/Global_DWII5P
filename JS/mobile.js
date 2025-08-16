// mobile.js
(function() {
    const esMovil = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if (esMovil) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '../CSS/mobile.css';
        document.head.appendChild(link);
        console.log("CSS móvil cargado");
    } else {
        console.log("Modo PC");
    }
})();
