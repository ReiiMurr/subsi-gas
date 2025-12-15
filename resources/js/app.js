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

function initPublicLivewireBridge() {
    const attach = () => {
        if (!window.Livewire || typeof window.Livewire.on !== 'function') {
            return;
        }

        if (window.__subsiGasPublicListenerAdded) {
            return;
        }

        window.Livewire.on('public-locations-updated', ({ locations }) => {
            window.__subsiGasPublicLastLocations = locations;

            if (Array.isArray(locations) && typeof window.__subsiGasPublicSetMarkers === 'function') {
                window.__subsiGasPublicSetMarkers(locations);
            }
        });

        window.__subsiGasPublicListenerAdded = true;
    };

    window.addEventListener('livewire:init', attach);
    attach();
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

function pinIcon(color, label = '') {
    const safeLabel = (label ?? '').toString();
    const text = safeLabel
        ? `<text x="12" y="10.5" text-anchor="middle" font-size="7" font-weight="700" fill="#fff" font-family="system-ui, -apple-system, Segoe UI, Roboto, Arial">${safeLabel}</text>`
        : '';

    return window.L.divIcon({
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        popupAnchor: [0, -26],
        html: `
                <div style="width:28px;height:28px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="${color}" d="M12 2c-3.87 0-7 3.13-7 7 0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
                        ${text}
                    </svg>
                </div>
            `.trim(),
    });
}

function haversineDistanceKm(lat1, lng1, lat2, lng2) {
    const toRad = (v) => (v * Math.PI) / 180;
    const R = 6371;

    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) *
            Math.cos(toRad(lat2)) *
            Math.sin(dLng / 2) *
            Math.sin(dLng / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function formatDistance(distanceKm) {
    if (!Number.isFinite(distanceKm)) {
        return null;
    }

    if (distanceKm < 1) {
        return `${Math.round(distanceKm * 1000)} m`;
    }

    return `${distanceKm.toFixed(2)} km`;
}

function ensurePublicLocationsListener(map, markersLayer) {
    window.__subsiGasPublicMarkersLayer = markersLayer;

    const setMarkers = (locations) => {
        const layer = window.__subsiGasPublicMarkersLayer;
        if (!layer) {
            return;
        }

        layer.clearLayers();

        locations.forEach((loc) => {
            if (!Number.isFinite(loc?.latitude) || !Number.isFinite(loc?.longitude)) {
                return;
            }

            const color = markerColor(loc.stock);

            let distanceKm = Number.isFinite(loc?.distance) ? loc.distance : null;
            if (!Number.isFinite(distanceKm)) {
                const pos = window.__subsiGasUserPosition;
                if (pos && Number.isFinite(pos.latitude) && Number.isFinite(pos.longitude)) {
                    distanceKm = haversineDistanceKm(
                        pos.latitude,
                        pos.longitude,
                        loc.latitude,
                        loc.longitude,
                    );
                }
            }

            const formattedDistance = formatDistance(distanceKm);

            const meta = [
                `<div style="opacity:.75;font-size:12px;margin-top:2px">Stock: ${loc.stock}</div>`,
            ];

            if (formattedDistance) {
                meta.push(
                    `<div style="opacity:.75;font-size:12px;margin-top:2px">Jarak: ${formattedDistance}</div>`,
                );
            }

            window.L.marker([loc.latitude, loc.longitude], {
                icon: pinIcon(color),
            })
                .addTo(layer)
                .bindPopup(
                    `<div style="min-width: 220px"><div style="font-weight: 600">${loc.name}</div>${meta.join('')}</div>`,
                );
        });
    };

    window.__subsiGasPublicSetMarkers = setMarkers;

    if (Array.isArray(window.__subsiGasPublicLastLocations)) {
        setMarkers(window.__subsiGasPublicLastLocations);
    }
}

function ensurePublicDirections(map) {
    window.__subsiGasPublicDirectionsMap = map;
    window.__subsiGasPublicRouteLayer = window.L.layerGroup().addTo(map);
    window.__subsiGasPublicUserLayer = window.L.layerGroup().addTo(map);
    window.__subsiGasPublicHasCenteredToUser = false;

    if (window.__subsiGasPublicDirectionsAdded) {
        return;
    }

    const isGeoAllowedContext = () => {
        if (window.isSecureContext) {
            return true;
        }

        const host = window.location.hostname;
        return host === 'localhost' || host === '127.0.0.1';
    };

    const setUserMarker = (lat, lng) => {
        const layer = window.__subsiGasPublicUserLayer;
        if (!layer) {
            return;
        }

        layer.clearLayers();

        window.L.circleMarker([lat, lng], {
            radius: 8,
            color: '#2563eb',
            fillColor: '#2563eb',
            fillOpacity: 0.9,
        })
            .addTo(layer)
            .bindPopup('Lokasi Anda');
    };

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
        const activeMap = window.__subsiGasPublicDirectionsMap;
        const routeLayer = window.__subsiGasPublicRouteLayer;
        if (!activeMap || !routeLayer) {
            return;
        }

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

            activeMap.fitBounds(polyline.getBounds(), { padding: [24, 24] });
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
        if (!wrapper || window.__subsiGasGeoInitialized) {
            return;
        }

        if (!navigator.geolocation) {
            window.__subsiGasGeoInitialized = true;
            return;
        }

        if (!isGeoAllowedContext()) {
            window.__subsiGasGeoInitialized = true;
            return;
        }

        const onPos = (pos) => {
            const latitude = pos.coords.latitude;
            const longitude = pos.coords.longitude;

            window.__subsiGasUserPosition = { latitude, longitude, ts: Date.now() };
            setUserMarker(latitude, longitude);
            dispatchToLivewire('geo-position', { latitude, longitude });

            const activeMap = window.__subsiGasPublicDirectionsMap;
            if (!activeMap) {
                return;
            }

            if (!window.__subsiGasPublicHasCenteredToUser) {
                activeMap.setView([latitude, longitude], 13);
                window.__subsiGasPublicHasCenteredToUser = true;
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

        window.__subsiGasGeoInitialized = true;
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

    if (window.__subsiGasPublicMap && typeof window.__subsiGasPublicMap.remove === 'function') {
        window.__subsiGasPublicMap.remove();
    }

    const map = window.L.map(canvas).setView([-6.2, 106.816666], 12);
    tileLayer().addTo(map);

    window.__subsiGasPublicMap = map;

    const markersLayer = window.L.layerGroup().addTo(map);

    ensurePublicLocationsListener(map, markersLayer);
    ensurePublicDirections(map);

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

initPublicLivewireBridge();
