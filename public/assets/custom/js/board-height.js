/* aCeler — alto de las vistas de tablero para que lleguen hasta abajo de la pantalla.
   Terminan 72px antes del final (el mismo margen inferior que la lista de proyectos):
   - Tablón de encargos / Mis encargos: columnas .kanban-box.fixedHeight
     (variable CSS --board-col-h; 600px si no hay script)
   - Diagrama de Gantt: #ganttLayout (variable CSS --gantt-h; 560px si no hay script;
     el modo pantalla completa tiene su propio alto y no se toca) */
(function () {
    var BOTTOM_GAP = 72;

    // Alto para que `el` (más lo que su contenedor tenga por debajo) acabe a BOTTOM_GAP del final
    function heightToBottom(el, container, min) {
        var rect = el.getBoundingClientRect();
        var below = container ? Math.max(0, container.getBoundingClientRect().bottom - rect.bottom) : 0;
        var top = rect.top + window.scrollY;
        return Math.max(min, Math.floor(window.innerHeight - top - below - BOTTOM_GAP));
    }

    function fit() {
        var root = document.documentElement;
        var desktop = window.innerWidth >= 1025;

        // Columnas del tablero (en móvil/tablet se apilan: alto por defecto)
        var box = document.querySelector('.kanban-box.fixedHeight');
        if (box) {
            if (desktop) {
                root.style.setProperty('--board-col-h', heightToBottom(box, box.closest('.card'), 360) + 'px');
            } else {
                root.style.removeProperty('--board-col-h');
            }
        }

        // Diagrama de Gantt
        var gantt = document.getElementById('ganttLayout');
        if (gantt) {
            if (desktop) {
                root.style.setProperty('--gantt-h', heightToBottom(gantt, null, 320) + 'px');
            } else {
                root.style.removeProperty('--gantt-h');
            }
        }
    }

    // Las vistas la llaman al final de su contenido, antes del primer pintado, para que las
    // columnas no aparezcan cortas y luego se alarguen; los eventos quedan de respaldo.
    window.acelerFitHeights = fit;
    document.addEventListener('DOMContentLoaded', fit);
    window.addEventListener('load', fit);
    window.addEventListener('resize', fit);
})();
