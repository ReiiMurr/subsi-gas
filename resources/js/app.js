function whenLeafletReady(callback, attempt = 0) {
    if (window.L && typeof window.L.map === 'function') {
        callback();
        return;
    }

    if (attempt > 50) {
        return;
    }

    setTimeout(() => whenLeafletReady(callback, attempt + 1), 50);
}

function tileLayer() {
    const token = window.SUBSI_GAS_MAPBOX_TOKEN;

    if (token) {
        return window.L.tileLayer(
            `https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=${token}`,
            {
                tileSize: 512,
                zoomOffset: -1,
                attribution:
                    '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            },
        );
    }

    return window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    });
}

function dispatchToLivewire(eventName, payload) {
    if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
        window.Livewire.dispatch(eventName, payload);
    }
}

function initMapPickers() {
    const pickers = document.querySelectorAll('[data-map-picker]');

    pickers.forEach((wrapper) => {
        if (wrapper.dataset.initialized === '1') {
            return;
        }

        const canvas = wrapper.querySelector('[data-map-picker-canvas]');
        if (!canvas) {
            return;
        }

        const lat = parseFloat(wrapper.dataset.lat);
        const lng = parseFloat(wrapper.dataset.lng);
        const startLat = Number.isFinite(lat) ? lat : -6.2;
        const startLng = Number.isFinite(lng) ? lng : 106.816666;

        const map = window.L.map(canvas).setView([startLat, startLng], 14);
        tileLayer().addTo(map);

        const marker = window.L.marker([startLat, startLng], {
            draggable: true,
        }).addTo(map);

        const update = (coords) => {
            marker.setLatLng(coords);

            dispatchToLivewire('map-picked', {
                latitude: coords.lat,
                longitude: coords.lng,
            });
        };

        marker.on('dragend', () => {
            update(marker.getLatLng());
        });

        map.on('click', (e) => {
            update(e.latlng);
        });

        wrapper.dataset.initialized = '1';
    });
}

function markerColor(stock) {
    if (stock <= 5) return '#ef4444';
    if (stock <= 20) return '#f59e0b';
    return '#22c55e';
}

function ensurePublicLocationsListener(map, markersLayer) {
    if (window.__subsiGasPublicListenerAdded) {
        return;
    }

    const setMarkers = (locations) => {
        markersLayer.clearLayers();

        locations.forEach((loc) => {
            const color = markerColor(loc.stock);

            window.L.circleMarker([loc.latitude, loc.longitude], {
                radius: 8,
                color,
                fillColor: color,
                fillOpacity: 0.95,
            })
                .addTo(markersLayer)
                .bindPopup(
                    `<div style="min-width: 200px"><div style="font-weight: 600">${loc.name}</div><div style="opacity:.75;font-size:12px;margin-top:2px">Stock: ${loc.stock}</div></div>`,
                );
        });
    };

    const attach = () => {
        if (!window.Livewire || typeof window.Livewire.on !== 'function') {
            return;
        }

        window.Livewire.on('public-locations-updated', ({ locations, center }) => {
            if (Array.isArray(locations)) {
                setMarkers(locations);
            }
        });

        window.__subsiGasPublicListenerAdded = true;
    };

    window.addEventListener('livewire:init', attach);
    attach();
}

function ensurePublicDirections(map) {
    if (window.__subsiGasPublicDirectionsAdded) {
        return;
    }

    const routeLayer = window.L.layerGroup().addTo(map);
    const userLayer = window.L.layerGroup().addTo(map);
    let hasCenteredToUser = false;

    const isGeoAllowedContext = () => {
        if (window.isSecureContext) {
            return true;
        }

        const host = window.location.hostname;
        return host === 'localhost' || host === '127.0.0.1';
    };

    const setUserMarker = (lat, lng) => {
        userLayer.clearLayers();

        window.L.circleMarker([lat, lng], {
            radius: 8,
            color: '#2563eb',
            fillColor: '#2563eb',
            fillOpacity: 0.9,
        })
            .addTo(userLayer)
            .bindPopup('Lokasi Anda');
    };

    const pinIcon = (color, label) =>
        window.L.divIcon({
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -26],
            html: `
                <div style="width:28px;height:28px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="${color}" d="M12 2c-3.87 0-7 3.13-7 7 0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
                        <text x="12" y="10.5" text-anchor="middle" font-size="7" font-weight="700" fill="#fff" font-family="system-ui, -apple-system, Segoe UI, Roboto, Arial">${label}</text>
                    </svg>
                </div>
            `.trim(),
        });

    const toFriendlyGeoError = (err) => {
        const type = err?.type || err?.message;

        if (type === 'insecure_context' || type === 'geolocation_insecure_context') {
            return 'GPS tidak bisa diakses karena halaman dibuka lewat HTTP (IP/local network). Gunakan HTTPS (misalnya ngrok) atau buka via localhost.';
        }

        if (type === 'not_supported' || type === 'geolocation_not_supported') {
            return 'Browser tidak mendukung GPS (geolocation).';
        }

        if (!isGeoAllowedContext()) {
            return 'GPS tidak bisa diakses karena halaman dibuka lewat HTTP (IP/local network). Gunakan HTTPS (misalnya ngrok) atau buka via localhost.';
        }

        if (!err || typeof err.code !== 'number') {
            return 'GPS tidak tersedia. Pastikan lokasi diaktifkan.';
        }

        if (err.code === 1) {
            return 'Akses lokasi ditolak. Silakan izinkan akses lokasi (GPS) di pengaturan browser.';
        }

        if (err.code === 2) {
            return 'Lokasi tidak bisa dideteksi. Coba nyalakan GPS dan aktifkan koneksi internet.';
        }

        if (err.code === 3) {
            return 'GPS timeout. Coba lagi ya.';
        }

        return 'GPS tidak tersedia. Pastikan lokasi diaktifkan.';
    };

    const getCachedPosition = () => {
        const pos = window.__subsiGasUserPosition;
        if (!pos || !Number.isFinite(pos.latitude) || !Number.isFinite(pos.longitude)) {
            return null;
        }

        if (typeof pos.ts === 'number' && Date.now() - pos.ts > 60_000) {
            return null;
        }

        return pos;
    };

    const requestDevicePosition = async () => {
        const cached = getCachedPosition();
        if (cached) {
            return cached;
        }

        if (!navigator.geolocation) {
            throw { type: 'not_supported' };
        }

        if (!isGeoAllowedContext()) {
            throw { type: 'insecure_context' };
        }

        const requestOnce = (options) =>
            new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const latitude = pos.coords.latitude;
                        const longitude = pos.coords.longitude;

                        const payload = { latitude, longitude, ts: Date.now() };
                        window.__subsiGasUserPosition = payload;
                        resolve(payload);
                    },
                    (err) => {
                        window.__subsiGasGeoError = err;
                        reject(err);
                    },
                    options,
                );
            });

        try {
            return await requestOnce({
                enableHighAccuracy: false,
                timeout: 20000,
                maximumAge: 60000,
            });
        } catch (err) {
            if (err?.code === 2 || err?.code === 3) {
                return await requestOnce({
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0,
                });
            }

            throw err;
        }
    };

    const fetchRoute = async (from, to) => {
        const url = `https://router.project-osrm.org/route/v1/driving/${from.longitude},${from.latitude};${to.longitude},${to.latitude}?overview=full&geometries=geojson`;
        const res = await fetch(url);
        if (!res.ok) {
            throw new Error('routing_failed');
        }

        const json = await res.json();
        const route = json?.routes?.[0];
        if (!route?.geometry?.coordinates) {
            throw new Error('routing_failed');
        }

        return route.geometry.coordinates;
    };

    const drawRoute = async (lat, lng, name) => {
        let from;
        try {
            from = await requestDevicePosition();
        } catch (e) {
            alert(toFriendlyGeoError(e));
            return;
        }

        const to = {
            latitude: lat,
            longitude: lng,
        };

        routeLayer.clearLayers();

        window.L.marker([from.latitude, from.longitude], {
            icon: pinIcon('#2563eb', 'A'),
        })
            .addTo(routeLayer)
            .bindPopup('Lokasi Anda');

        window.L.marker([to.latitude, to.longitude], {
            icon: pinIcon('#ef4444', 'B'),
        })
            .addTo(routeLayer)
            .bindPopup(name ? `Tujuan: ${name}` : 'Tujuan');

        try {
            const coords = await fetchRoute(from, to);
            const latlngs = coords.map(([cLng, cLat]) => [cLat, cLng]);
            const polyline = window.L.polyline(latlngs, {
                color: '#2563eb',
                weight: 5,
                opacity: 0.9,
            }).addTo(routeLayer);

            map.fitBounds(polyline.getBounds(), { padding: [24, 24] });
        } catch (e) {
            alert('Gagal membuat rute. Coba lagi ya.');
        }
    };

    const handleRouteClick = (el) => {
        const lat = parseFloat(el.dataset.routeLat);
        const lng = parseFloat(el.dataset.routeLng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        drawRoute(lat, lng, el.dataset.routeName || '');
    };

    document.addEventListener('click', (e) => {
        const el = e.target?.closest?.('[data-route-lat][data-route-lng]');
        if (!el) return;

        e.preventDefault();
        handleRouteClick(el);
    });

    const initGeolocation = () => {
        const wrapper = document.querySelector('[data-public-landing]');
        if (!wrapper || wrapper.dataset.geoInitialized === '1') {
            return;
        }

        if (!navigator.geolocation) {
            wrapper.dataset.geoInitialized = '1';
            return;
        }

        if (!isGeoAllowedContext()) {
            wrapper.dataset.geoInitialized = '1';
            return;
        }

        const onPos = (pos) => {
            const latitude = pos.coords.latitude;
            const longitude = pos.coords.longitude;

            window.__subsiGasUserPosition = { latitude, longitude, ts: Date.now() };
            setUserMarker(latitude, longitude);

            if (!hasCenteredToUser) {
                map.setView([latitude, longitude], 13);
                hasCenteredToUser = true;
            }
        };

        navigator.geolocation.getCurrentPosition(onPos, () => {}, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 30000,
        });

        navigator.geolocation.watchPosition(onPos, () => {}, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 30000,
        });

        wrapper.dataset.geoInitialized = '1';
    };

    initGeolocation();
    window.__subsiGasPublicDirectionsAdded = true;
}

function initPublicMap() {
    const wrapper = document.querySelector('[data-public-map]');
    if (!wrapper || wrapper.dataset.initialized === '1') {
        return;
    }

    const canvas = wrapper.querySelector('[data-public-map-canvas]');
    if (!canvas) {
        return;
    }

    const map = window.L.map(canvas).setView([-6.2, 106.816666], 12);
    tileLayer().addTo(map);

    const markersLayer = window.L.layerGroup().addTo(map);

    ensurePublicLocationsListener(map, markersLayer);
    ensurePublicDirections(map);

    window.__subsiGasPublicMap = map;

    wrapper.dataset.initialized = '1';
}

function initAllMaps() {
    whenLeafletReady(() => {
        initMapPickers();
        initPublicMap();
    });
}

document.addEventListener('DOMContentLoaded', initAllMaps);
document.addEventListener('livewire:navigated', initAllMaps);
