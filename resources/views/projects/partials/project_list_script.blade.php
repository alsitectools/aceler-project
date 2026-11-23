{{--
    Isotope + orden + filtros (pestañas de estado y desplegables) del listado de proyectos.
    Lo usan Proyectos y Mis proyectos dentro de @push('scripts').
    Cada filtro activo es una clase que la fila (.pl-row) debe tener:
      status: Ongoing | OnHold | Finished (o «pl-active» = Ongoing u OnHold)   type: type-N   del: del-xx   ws: workspace-slug
--}}
<script>
    $(document).ready(function() {
        if (typeof $.fn.isotope === 'undefined') {
            console.error('Isotope is not loaded');
            return;
        }

        // ---------- Orden (se recuerda en este navegador) ----------
        var SORT_KEY = 'aceler.projects.sort';
        var sortOptions = {
            'recent': { sortBy: 'updated', sortAscending: false },
            'name-asc': { sortBy: 'name', sortAscending: true },
            'name-desc': { sortBy: 'name', sortAscending: false }
        };
        var currentSort = 'recent';
        try {
            var savedSort = window.localStorage.getItem(SORT_KEY);
            if (savedSort && sortOptions[savedSort]) currentSort = savedSort;
        } catch (e) {}

        function paintSortControl(key) {
            var $item = $('.pl-sort-menu [data-sort="' + key + '"]');
            $('.pl-sort-menu .dropdown-item').removeClass('active');
            $item.addClass('active');
            $('#btnSortProjects').attr('title', $.trim($item.text()));
        }
        paintSortControl(currentSort);

        // ---------- Filtros ----------
        // Estado: por defecto «Activos» (en curso + en espera); se recuerda la última pestaña elegida
        var STATUS_KEY = 'aceler.projects.statusTab';
        var statusValues = $('.pl-tabs .pl-tab').map(function() { return String($(this).data('value')); }).get();
        var initialStatus = statusValues.indexOf('pl-active') !== -1 ? 'pl-active' : '*';
        try {
            var savedStatus = window.localStorage.getItem(STATUS_KEY);
            if (savedStatus && statusValues.indexOf(savedStatus) !== -1) initialStatus = savedStatus;
        } catch (e) {}
        var state = { status: initialStatus, type: '*', del: '*', ws: '*' };
        $('.pl-tabs .pl-tab').removeClass('active').filter(function() {
            return String($(this).data('value')) === initialStatus;
        }).addClass('active');

        // ¿La fila tiene ese valor? «pl-active» = en curso o en espera
        function hasValue(el, value) {
            if (value === 'pl-active') return el.classList.contains('Ongoing') || el.classList.contains('OnHold');
            return el.classList.contains(value);
        }

        function matches(el, skipFacet) {
            for (var facet in state) {
                if (facet === skipFacet || state[facet] === '*') continue;
                if (!hasValue(el, state[facet])) return false;
            }
            return true;
        }

        var $grid = $('.grid').isotope({
            itemSelector: '.All',
            percentPosition: true,
            masonry: { columnWidth: '.All' },
            getSortData: {
                name: '[data-project-name]',
                updated: '[data-updated] parseInt'
            },
            sortBy: sortOptions[currentSort].sortBy,
            sortAscending: sortOptions[currentSort].sortAscending,
            filter: function(itemElem) {
                return matches(itemElem || this);
            }
        });
        window._projGrid = $grid;

        // Contador total junto al título
        var $plCount = $('<span class="pl-count"></span>');
        $('.page-header-title h4').first().append($plCount);

        // Números de cada opción: cuántas filas quedarían eligiéndola, con el resto de filtros aplicados
        function updateCounts() {
            var rows = $('.pl-row').get();
            $plCount.text(rows.length);
            $('[data-facet]').each(function() {
                var facet = $(this).data('facet');
                $(this).find('[data-value]').each(function() {
                    var value = $(this).data('value');
                    var n = rows.filter(function(r) {
                        return matches(r, facet) && (value === '*' || hasValue(r, value));
                    }).length;
                    $(this).find('.pl-n').text(n);
                    $(this).toggleClass('pl-zero', n === 0);
                });
            });
        }

        // Botón del desplegable: "Tipo" o "Tipo: Obra ✕"
        function paintFacet(facet) {
            var $wrap = $('.pl-facet[data-facet="' + facet + '"]');
            if (!$wrap.length) return;
            var value = state[facet];
            var $items = $wrap.find('[data-value]');
            $items.removeClass('active');
            if (value === '*') {
                $wrap.removeClass('on').find('.pl-facet-label').text($wrap.data('label'));
            } else {
                var $item = $items.filter('[data-value="' + value + '"]').addClass('active');
                $wrap.addClass('on').find('.pl-facet-label').text($wrap.data('label') + ': ' + $item.data('text'));
            }
        }

        // Pie de la caja: "Mostrando N proyectos"
        var $shown = $('.pl-list-shown');
        function updateShown() {
            var iso = $grid.data('isotope');
            var n = iso ? iso.filteredItems.length : $('.pl-row').length;
            $shown.text(String($shown.data('tpl') || '').replace('__N__', n));
        }
        $grid.on('arrangeComplete', updateShown);

        // Caja con scroll: termina 72px antes del final de la pantalla (hueco para CofriAI, 44px a 16px del borde)
        var $scroll = $('#plListScroll');
        var BOTTOM_GAP = 72;
        function fitListBox() {
            if (!$scroll.length) return;
            if (window.innerWidth < 768) {
                $scroll.css('max-height', '');
                return;
            }
            var $box = $scroll.closest('.pl-list-box');
            var footH = $box.find('.pl-list-foot').outerHeight() || 0;
            var top = $scroll[0].getBoundingClientRect().top + window.scrollY;
            var h = window.innerHeight - top - footH - BOTTOM_GAP;
            $scroll.css('max-height', Math.max(240, h) + 'px');
        }
        fitListBox();
        $(window).on('resize', fitListBox);
        $(window).on('load', fitListBox);

        $('.pl-to-top').on('click', function() {
            $scroll.animate({ scrollTop: 0 }, 200);
        });

        function apply() {
            $grid.isotope();
            updateCounts();
            updateShown();
        }
        window.plRefresh = apply;

        // Pestañas de estado
        $('.pl-tabs').on('click', '.pl-tab', function() {
            state.status = String($(this).data('value'));
            $('.pl-tabs .pl-tab').removeClass('active');
            $(this).addClass('active');
            try { window.localStorage.setItem(STATUS_KEY, state.status); } catch (e) {}
            apply();
        });

        // Opción de un desplegable (volver a elegir la activa la quita)
        $('.pl-facet').on('click', '.dropdown-item', function() {
            var facet = $(this).closest('.pl-facet').data('facet');
            var value = $(this).data('value');
            state[facet] = state[facet] === value ? '*' : value;
            paintFacet(facet);
            apply();
        });

        // ✕ del botón: quita el filtro sin abrir el menú
        $('.pl-facet').on('click', '.pl-facet-x', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var facet = $(this).closest('.pl-facet').data('facet');
            state[facet] = '*';
            paintFacet(facet);
            apply();
        });

        // Orden
        $('.pl-sort-menu').on('click', '[data-sort]', function() {
            var key = $(this).data('sort');
            if (!sortOptions[key]) return;
            paintSortControl(key);
            try { window.localStorage.setItem(SORT_KEY, key); } catch (e) {}
            $grid.isotope(sortOptions[key]);
        });

        updateCounts();
        updateShown();
    });
</script>
