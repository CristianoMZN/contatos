(function enableThemeToggle() {
    const root = document.documentElement;
    const toggle = document.querySelector('[data-theme-toggle]');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const saved = localStorage.getItem('ux-theme');

    const applyTheme = (theme) => {
        const safeTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-bs-theme', safeTheme);
        if (toggle) {
            toggle.checked = safeTheme === 'dark';
        }
        localStorage.setItem('ux-theme', safeTheme);
    };

    applyTheme(saved ?? (prefersDark ? 'dark' : 'light'));

    toggle?.addEventListener('change', () => {
        applyTheme(toggle.checked ? 'dark' : 'light');
    });
})();

document.addEventListener('DOMContentLoaded', () => {
    const frame = document.getElementById('public-contact-frame');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const radiusFilter = document.getElementById('radiusFilter');
    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');
    const geoStatus = document.getElementById('geoStatus');
    const btnClearGeo = document.getElementById('btnClearGeo');
    const btnUseLocation = document.getElementById('btnUseLocation');
    const viewModeInputs = document.querySelectorAll('input[name="viewMode"]');

    const debounce = (fn, delay = 260) => {
        let timeout;
        return (...args) => {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(() => fn(...args), delay);
        };
    };

    const updateGeoStatus = () => {
        if (!geoStatus) {
            return;
        }

        if (latInput?.value && lngInput?.value) {
            geoStatus.textContent = `Geo ligado (${latInput.value}, ${lngInput.value})`;
            geoStatus.className = 'badge bg-success-subtle text-success-emphasis';
            return;
        }

        geoStatus.textContent = 'Geo desabilitado';
        geoStatus.className = 'badge bg-info-subtle text-info-emphasis';
    };

    const buildFrameUrl = () => {
        if (!frame) {
            return '';
        }

        const base = frame.dataset.srcBase || frame.getAttribute('src') || window.location.pathname;
        const url = new URL(base, window.location.origin);

        if (searchInput?.value) {
            url.searchParams.set('q', searchInput.value.trim());
        } else {
            url.searchParams.delete('q');
        }

        if (categoryFilter?.value) {
            url.searchParams.set('category', categoryFilter.value);
        } else {
            url.searchParams.delete('category');
        }

        const radiusValue = parseFloat(radiusFilter?.value ?? '');
        if (!Number.isNaN(radiusValue) && radiusValue > 0) {
            url.searchParams.set('radius', radiusValue.toString());
        } else {
            url.searchParams.delete('radius');
        }

        if (latInput?.value && lngInput?.value) {
            url.searchParams.set('lat', latInput.value);
            url.searchParams.set('lng', lngInput.value);
        } else {
            url.searchParams.delete('lat');
            url.searchParams.delete('lng');
        }

        const viewMode = Array.from(viewModeInputs).find((input) => input.checked)?.value;
        if (viewMode) {
            url.searchParams.set('view', viewMode);
        }

        if (frame.dataset.limit) {
            url.searchParams.set('limit', frame.dataset.limit);
        }

        return url.toString();
    };

    const reloadFrame = debounce(() => {
        if (!frame) {
            return;
        }

        const target = buildFrameUrl();
        if (!target) {
            return;
        }

        frame.setAttribute('src', target);

        if (typeof frame.reload === 'function') {
            frame.reload();
            return;
        }

        fetch(target, { headers: { 'Turbo-Frame': frame.id } })
            .then((response) => response.text())
            .then((html) => {
                frame.innerHTML = html;
            })
            .catch((error) => console.error('Erro ao atualizar frame:', error));
    });

    const attachMask = () => {
        const inputs = document.querySelectorAll('[data-mask="smart-phone"]');
        const formatPhone = (digits) => {
            const safeDigits = digits.slice(0, 11);
            if (safeDigits.length <= 10) {
                const area = safeDigits.slice(0, 2);
                const middle = safeDigits.slice(2, 6);
                const last = safeDigits.slice(6, 10);

                let formatted = '';
                if (area) formatted += `(${area}`;
                if (area.length === 2) formatted += ') ';
                if (middle) formatted += middle;
                if (last) formatted += `-${last}`;

                return formatted.trim();
            }

            const area = safeDigits.slice(0, 2);
            const middle = safeDigits.slice(2, 7);
            const last = safeDigits.slice(7, 11);

            return `(${area}) ${middle}${last ? `-${last}` : ''}`;
        };

        inputs.forEach((input) => {
            input.addEventListener('input', () => {
                const raw = input.value;
                const digitsOnly = raw.replace(/\D/g, '');

                if (digitsOnly.length < 3 || /[a-zA-Z]/.test(raw)) {
                    return;
                }

                input.value = formatPhone(digitsOnly);
            });
        });
    };

    btnUseLocation?.addEventListener('click', (event) => {
        event.preventDefault();
        if (!navigator.geolocation) {
            if (geoStatus) {
                geoStatus.textContent = 'Geo não suportado';
                geoStatus.className = 'badge bg-danger-subtle text-danger-emphasis';
            }
            return;
        }

        if (geoStatus) {
            geoStatus.textContent = 'Localizando...';
            geoStatus.className = 'badge bg-warning-subtle text-warning-emphasis';
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (latInput) {
                    latInput.value = position.coords.latitude.toFixed(6);
                }
                if (lngInput) {
                    lngInput.value = position.coords.longitude.toFixed(6);
                }
                updateGeoStatus();
                reloadFrame();
            },
            () => {
                if (geoStatus) {
                    geoStatus.textContent = 'Falha ao obter localização';
                    geoStatus.className = 'badge bg-danger-subtle text-danger-emphasis';
                }
            }
        );
    });

    btnClearGeo?.addEventListener('click', (event) => {
        event.preventDefault();
        if (latInput) {
            latInput.value = '';
        }
        if (lngInput) {
            lngInput.value = '';
        }
        updateGeoStatus();
        reloadFrame();
    });

    searchInput?.addEventListener('input', reloadFrame);
    categoryFilter?.addEventListener('change', reloadFrame);
    radiusFilter?.addEventListener('input', reloadFrame);
    viewModeInputs.forEach((input) => input.addEventListener('change', reloadFrame));

    attachMask();
    updateGeoStatus();
});
