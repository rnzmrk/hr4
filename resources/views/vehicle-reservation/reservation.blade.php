@extends('layouts.app')

@section('page-title', $title ?? 'Vehicle Reservation')
@section('page-subtitle', 'Reserve vehicles for upcoming trips')
@section('breadcrumbs', 'Vehicle Reservation')

@push('styles')
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        #reservationMap { min-height: 320px; height: 320px; width: 100%; border-radius: 8px; overflow: hidden; position: relative; }
        /* Compact the form height on this page only */
        .reservation-form .mb-3 { margin-bottom: 0.5rem !important; }
        .reservation-form .form-label { margin-bottom: 0.25rem; font-size: 0.9rem; }
        .reservation-form .form-control,
        .reservation-form .form-select,
        .reservation-form textarea { padding: 0.375rem 0.5rem; }
        .reservation-form textarea { min-height: 2.25rem; }
        /* Keep default container width; no custom max-width so it aligns with the table card */
    </style>
@endpush

@section('content')
<div class="page-header-container mb-4">
  <div class="d-flex justify-content-between align-items-center page-header">
    <div class="d-flex align-items-center">
      <div class="dashboard-logo me-3">
        <img src="{{ asset('images/logo.png') }}" alt="Jetlouge Travels" class="logo-img">
      </div>
      <div>
        <h2 class="fw-bold mb-1">Vehicle Reservation</h2>
        <p class="text-muted mb-0">Reserve vehicles for upcoming trips</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 order-1 mb-4">
    <div class="card h-100">
      <div class="card-header fw-semibold">New Reservation</div>
      <div class="card-body p-2">
        <form id="reservationForm" class="reservation-form">
          <input type="hidden" name="requester_id" value="{{ Auth::id() ?? 0 }}" />
          <div class="mb-3">
            <label class="form-label">Trip Purpose</label>
            <input type="text" class="form-control" name="trip_purpose" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Start Location</label>
            <input type="text" class="form-control" name="start_location" required />
            <input type="hidden" name="start_lat" />
            <input type="hidden" name="start_lng" />
          </div>
          <div class="mb-3">
            <label class="form-label">Destination</label>
            <input type="text" class="form-control" name="destination" required />
            <input type="hidden" name="destination_lat" />
            <input type="hidden" name="destination_lng" />
          </div>
          <div class="mb-3">
            <label class="form-label">Map</label>
            <div id="reservationMap" class="border"></div>
            <div class="form-text">Type an address and tab out to pin it, or click the map while a field is focused to set its pin. Blue: Start, Red: Destination.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Preferable Vehicle</label>
            <select class="form-select" name="preferred_vehicle" required>
              <option value="">No preference</option>
              <option value="sedan">Sedan</option>
              <option value="suv">SUV</option>
              <option value="van">Van</option>
              <option value="bus">Bus</option>
              <option value="truck">Truck</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Reservation Date</label>
              <input type="date" class="form-control" name="reservation_date" min="{{ date('Y-m-d') }}" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Departure Time</label>
              <input type="time" class="form-control" name="departure_time" required />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Expected Return Time</label>
              <input type="time" class="form-control" name="return_time" />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Passengers</label>
              <input type="number" min="1" class="form-control" name="passenger_count" value="1" required />
            </div> 
          </div>
          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select class="form-select" name="priority">
              <option value="normal" selected>Normal</option>
              <option value="high">High</option>
              <option value="low">Low</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="3"></textarea>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary" id="btnSubmitReservation">
              <i class="fas fa-paper-plane me-2"></i>Submit Reservation
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btnCancelReservation">Cancel</button>
            <span id="reservationFeedback" class="small text-muted d-none" aria-live="polite"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/table-pagination.js') }}" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script>
        // VRDS Reservation page interactions wired to backend API (now Trip-centric)
        (function () {
          'use strict';

          document.addEventListener('DOMContentLoaded', function () {
            if (window.__vrdsReservationInitialized) {
              try { console.warn('[VRDS] Reservation script already initialized; skipping duplicate init'); } catch(_){}
              return;
            }
            window.__vrdsReservationInitialized = true;
            if (window && window.console) console.log('[VRDS] Reservation script init');
            const form = document.getElementById('reservationForm');
            const feedback = document.getElementById('reservationFeedback');
            const btnCancel = document.getElementById('btnCancelReservation');
            const btnSubmit = document.getElementById('btnSubmitReservation');
            // Address fields and hidden lat/lngs
            const startInput = document.querySelector('[name="start_location"]');
            const destInput = document.querySelector('[name="destination"]');
            const startLatInput = document.querySelector('[name="start_lat"]');
            const startLngInput = document.querySelector('[name="start_lng"]');
            const destLatInput = document.querySelector('[name="destination_lat"]');
            const destLngInput = document.querySelector('[name="destination_lng"]');
            const dateInput = document.querySelector('[name="reservation_date"]');
            const timeInput = document.querySelector('[name="departure_time"]');
            const mapEl = document.getElementById('reservationMap');
            let submitController = null;

            // Leaflet map state
            let map = window.__vrdsResvMap || null;
            let startGroup = window.__vrdsResvStartGroup || null;
            let destGroup = window.__vrdsResvDestGroup || null;
            let startMarker = window.__vrdsResvStartMarker || null;
            let destMarker = window.__vrdsResvDestMarker || null;
            let routeLine = window.__vrdsResvRouteLine || null;
            let routeGroup = window.__vrdsResvRouteGroup || null;
            let routeDrawSeq = 0;
            let lastFocused = null;
            let suppressStartBlurOnce = false;
            let suppressDestBlurOnce = false;
            let suppressResetToastOnce = false;
            let startTypingTimer = null;
            let destTypingTimer = null;
            let startGeocodeSeq = 0;
            let destGeocodeSeq = 0;

            function isCoordsSet() {
              return !!(startLatInput?.value && startLngInput?.value && destLatInput?.value && destLngInput?.value);
            }

            function updateSubmitState() {
              const hasDate = !!(dateInput && dateInput.value);
              const hasTime = !!(timeInput && timeInput.value);
              const hasStart = !!(startInput && startInput.value && startInput.value.trim());
              const hasDest = !!(destInput && destInput.value && destInput.value.trim());
              if (btnSubmit) btnSubmit.disabled = !(hasDate && hasTime && hasStart && hasDest);
            }

            function buildHeaders(json = true) {
              const headers = { 'Accept': 'application/json' };
              if (json) headers['Content-Type'] = 'application/json';
              const tokenMeta = document.querySelector('meta[name="api-token"]');
              if (tokenMeta && tokenMeta.content) headers['Authorization'] = 'Bearer ' + tokenMeta.content;
              return headers;
            }

            const __ESCAPE_MAP = { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' };
            function escapeHtml(input) {
              if (input == null) return '';
              return String(input).replace(/[&<>"']/g, ch => __ESCAPE_MAP[ch] || ch);
            }

            // Geocoding helper
            let __geoLastAt = 0;
            let __geoFailCount = 0;
            let __geoBackoffUntil = 0;
            let __geoNotifiedBackoff = false;
            const GEO_MIN_INTERVAL_MS = 2000;
            const GEO_BACKOFF_MS = 15000;
            
            async function geocodeAddress(q) {
              const query = (q || '').toString().trim();
              if (!query) return null;
              try {
                const now = Date.now();
                if (now < __geoBackoffUntil) return null;
                const since = now - __geoLastAt;
                if (since < GEO_MIN_INTERVAL_MS) {
                  await new Promise(r => setTimeout(r, GEO_MIN_INTERVAL_MS - since));
                }
                
                // Try the provided endpoint first
                const url = `https://logistics2.jetlougetravels-ph.com/api/geo.php/search?q=${encodeURIComponent(query)}&limit=1`;
                let res, data;
                try {
                  res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                  data = await res.json();
                } catch (errX) {
                  data = null;
                }
                __geoLastAt = Date.now();
                if (data && data.success && Array.isArray(data.data) && data.data.length) {
                  const item = data.data[0];
                  const lat = parseFloat(item.lat);
                  const lon = parseFloat(item.lon);
                  if (isFinite(lat) && isFinite(lon)) {
                    __geoFailCount = 0;
                    return { lat, lon, display_name: item.display_name };
                  }
                }
                
                // Fallback to Nominatim
                try {
                  const nomUrl = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`;
                  const nomRes = await fetch(nomUrl, { headers: { 'Accept': 'application/json' } });
                  const nomData = await nomRes.json();
                  if (Array.isArray(nomData) && nomData.length) {
                    const item2 = nomData[0];
                    const lat2 = parseFloat(item2.lat);
                    const lon2 = parseFloat(item2.lon);
                    if (isFinite(lat2) && isFinite(lon2)) {
                      __geoFailCount = 0;
                      return { lat: lat2, lon: lon2, display_name: item2.display_name || query };
                    }
                  }
                } catch(_) {}
                
                __geoFailCount++;
                if (__geoFailCount >= 2) {
                  __geoBackoffUntil = Date.now() + GEO_BACKOFF_MS;
                  if (!__geoNotifiedBackoff) {
                    try { if (window.pushNotification) window.pushNotification('warning', 'Address lookup is temporarily busy. You can keep typing or submit without pins; coordinates will be assigned later.'); } catch(_) {}
                    __geoNotifiedBackoff = true;
                  }
                }
              } catch (_) {}
              return null;
            }

            function ensureMap() {
              if (!mapEl || !window.L || typeof window.L.map !== 'function') return null;
              if (!map) {
                mapEl.innerHTML = '';
                
                map = L.map(mapEl).setView([0, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                try {
                  startGroup = L.layerGroup().addTo(map);
                  destGroup = L.layerGroup().addTo(map);
                  routeGroup = L.layerGroup().addTo(map);
                } catch(_){}
                try {
                  window.__vrdsResvMap = map;
                  window.__vrdsResvStartGroup = startGroup;
                  window.__vrdsResvDestGroup = destGroup;
                  window.__vrdsResvRouteGroup = routeGroup;
                } catch(_){}
                try {
                  const refreshSize = () => { try { map.invalidateSize(); } catch(_){} };
                  setTimeout(refreshSize, 200);
                  window.addEventListener('load', refreshSize);
                  window.addEventListener('resize', refreshSize);
                } catch(_){ }
                try { map.off('click'); } catch(_){}
                map.on('click', async (e) => {
                  const { lat, lng } = e.latlng || {};
                  if (!isFinite(lat) || !isFinite(lng)) return;
                  let label = await reverseGeocode(lat, lng);
                  if (!label) label = 'Picked on map';
                  let target = lastFocused;
                  if (!target) {
                    const startVal = (startInput && startInput.value) ? startInput.value.trim() : '';
                    const destVal = (destInput && destInput.value) ? destInput.value.trim() : '';
                    if (!startVal) target = 'start';
                    else if (!destVal) target = 'dest';
                    else target = 'start';
                    lastFocused = target;
                  }
                  if (target === 'start') {
                    setStartPoint(lat, lng, label);
                    if (typeof label === 'string' && startInput) {
                      suppressStartBlurOnce = true;
                      startInput.value = label;
                      try { startInput.dispatchEvent(new Event('input', { bubbles: true })); } catch(_){ }
                      try { startInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(_){ }
                    }
                  } else if (target === 'dest') {
                    setDestPoint(lat, lng, label);
                    if (typeof label === 'string' && destInput) {
                      suppressDestBlurOnce = true;
                      destInput.value = label;
                      try { destInput.dispatchEvent(new Event('input', { bubbles: true })); } catch(_){ }
                      try { destInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(_){ }
                    }
                  }
                });
              }
              return map;
            }

            async function setStartPoint(lat, lng, label) {
              ensureMap();
              if (startLatInput) startLatInput.value = String(lat);
              if (startLngInput) startLngInput.value = String(lng);
              if (label && startInput) { 
                try { startInput.value = label; } catch(_){ }
                try { startInput.dispatchEvent(new Event('input', { bubbles: true })); } catch(_){ }
                try { startInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(_){ }
              }
              if (map && window.L) {
                try { 
                  if (!startMarker) {
                    startMarker = L.circleMarker([lat, lng], { radius: 8, color: '#0d6efd', fillColor: '#0d6efd', fillOpacity: 0.85 })
                      .bindPopup(`<b>Start</b>${label ? `<br>${escapeHtml(label)}` : ''}`);
                  }
                  startMarker.setLatLng([lat, lng]); 
                  if (label) startMarker.setPopupContent(`<b>Start</b><br>${escapeHtml(label)}`); 
                  if (startGroup) startGroup.clearLayers(); 
                  (startGroup || map).addLayer(startMarker); 
                  window.__vrdsResvStartMarker = startMarker; 
                } catch(_){ }
              }
              updateSubmitState();
              await drawOrUpdateRoute();
              fitBounds();
            }

            async function setDestPoint(lat, lng, label) {
              ensureMap();
              if (destLatInput) destLatInput.value = String(lat);
              if (destLngInput) destLngInput.value = String(lng);
              if (label && destInput) { 
                try { destInput.value = label; } catch(_){ }
                try { destInput.dispatchEvent(new Event('input', { bubbles: true })); } catch(_){ }
                try { destInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(_){ }
              }
              if (map && window.L) {
                try { 
                  if (!destMarker) {
                    destMarker = L.circleMarker([lat, lng], { radius: 8, color: '#dc3545', fillColor: '#dc3545', fillOpacity: 0.85 })
                      .bindPopup(`<b>Destination</b>${label ? `<br>${escapeHtml(label)}` : ''}`);
                  }
                  destMarker.setLatLng([lat, lng]); 
                  if (label) destMarker.setPopupContent(`<b>Destination</b><br>${escapeHtml(label)}`); 
                  if (destGroup) destGroup.clearLayers(); 
                  (destGroup || map).addLayer(destMarker); 
                  window.__vrdsResvDestMarker = destMarker; 
                } catch(_){ }
              }
              updateSubmitState();
              await drawOrUpdateRoute();
              fitBounds();
            }

            function fitBounds() {
              if (!map) return;
              if (routeLine) {
                try { map.fitBounds(routeLine.getBounds(), { padding: [20, 20] }); return; } catch(_){}
              }
              const pts = [];
              if (startMarker) pts.push(startMarker.getLatLng());
              if (destMarker) pts.push(destMarker.getLatLng());
              if (pts.length === 1) { map.setView(pts[0], 12); }
              else if (pts.length >= 2) { try { map.fitBounds(pts, { padding: [20, 20] }); } catch(_){} }
            }

            async function fetchRoadRoute(start, end) {
              try {
                const slat = Number(start.lat), slon = Number(start.lng);
                const elat = Number(end.lat), elon = Number(end.lng);
                if (!Number.isFinite(slat) || !Number.isFinite(slon) || !Number.isFinite(elat) || !Number.isFinite(elon)) return null;
                const url = `https://router.project-osrm.org/route/v1/driving/${slon},${slat};${elon},${elat}?overview=full&geometries=geojson`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (!data || !Array.isArray(data.routes) || !data.routes.length) return null;
                const coords = data.routes[0]?.geometry?.coordinates || [];
                if (!coords.length) return null;
                return coords.map(c => [c[1], c[0]]);
              } catch (_) { return null; }
            }

            async function drawOrUpdateRoute() {
              if (!map) return;
              const mySeq = ++routeDrawSeq;
              if (routeLine) { try { map.removeLayer(routeLine); } catch(_){} routeLine = null; window.__vrdsResvRouteLine = null; }
              try { if (routeGroup) routeGroup.clearLayers(); } catch(_){ }
              const sLL = startMarker && startMarker.getLatLng ? startMarker.getLatLng() : null;
              const dLL = destMarker && destMarker.getLatLng ? destMarker.getLatLng() : null;
              if (!sLL || !dLL) return;
              let lineCoords = await fetchRoadRoute({ lat: sLL.lat, lng: sLL.lng }, { lat: dLL.lat, lng: dLL.lng });
              if (mySeq !== routeDrawSeq) return;
              if (!Array.isArray(lineCoords) || lineCoords.length < 2) {
                lineCoords = [ [sLL.lat, sLL.lng], [dLL.lat, dLL.lng] ];
              }
              routeLine = L.polyline(lineCoords, { color: '#0d6efd', weight: 4, opacity: 0.9 });
              try { (routeGroup || map).addLayer(routeLine); } catch(_) { try { routeLine.addTo(map); } catch(_){} }
              try { window.__vrdsResvRouteLine = routeLine; } catch(_){ }
            }

            async function api(url, options = {}) {
              const hasBody = options && Object.prototype.hasOwnProperty.call(options, 'body') && options.body != null;
              const base = { headers: buildHeaders(hasBody), credentials: 'same-origin' };
              const res = await fetch(url, Object.assign(base, options));
              const status = res.status;
              const text = await res.text();
              try { return { ok: res.ok, status, json: JSON.parse(text) }; }
              catch (e) {
                if (window && window.console) console.error('API parse error', e, { url, status, text });
                return { ok: res.ok, status, json: { success: false, message: text } };
              }
            }

            function setFeedback(msg, ok = true) {
              try {
                if (window.pushNotification && typeof window.pushNotification === 'function') {
                  window.pushNotification(ok ? 'success' : 'danger', msg || (ok ? 'OK' : 'Error'));
                }
              } catch (e) {}
              if (feedback) {
                feedback.textContent = '';
                if (!feedback.classList.contains('d-none')) feedback.classList.add('d-none');
              }
            }

            if (form) {
              const fieldLabels = {
                trip_purpose: 'Trip Purpose',
                start_location: 'Start Location',
                destination: 'Destination',
                reservation_date: 'Reservation Date',
                departure_time: 'Departure Time',
                passenger_count: 'Passengers'
              };

              form.querySelectorAll('[name][required]').forEach(ctrl => {
                const handler = () => {
                  if (ctrl.value) {
                    ctrl.classList.remove('is-invalid');
                    ctrl.classList.add('is-valid');
                  } else {
                    ctrl.classList.remove('is-valid');
                  }
                };
                ctrl.addEventListener('input', handler);
                ctrl.addEventListener('change', handler);
              });

              form.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (submitController) return;
                
                const requiredControls = form.querySelectorAll('[name][required]');
                let valid = true;
                const missing = [];
                let firstInvalid = null;
                requiredControls.forEach(ctrl => {
                  const hasVal = !!ctrl.value;
                  if (!hasVal) {
                    ctrl.classList.add('is-invalid');
                    valid = false;
                    if (!firstInvalid) firstInvalid = ctrl;
                    const nm = (ctrl.getAttribute('name') || '').toString();
                    if (nm) missing.push(fieldLabels[nm] || nm);
                  } else {
                    ctrl.classList.remove('is-invalid');
                    ctrl.classList.add('is-valid');
                  }
                });
                
                if (!valid) {
                  if (window.pushNotification) {
                    const msg = 'Please complete required fields: ' + missing.join(', ');
                    try { window.pushNotification('warning', msg); } catch(_) {}
                  }
                  try {
                    if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus();
                    if (firstInvalid && firstInvalid.scrollIntoView) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  } catch(_) {}
                  return;
                }
                
                // Validate reservation date is not in the past
                const reservationDate = form.querySelector('[name="reservation_date"]');
                if (reservationDate && reservationDate.value) {
                  const selectedDate = new Date(reservationDate.value);
                  const today = new Date();
                  today.setHours(0, 0, 0, 0);
                  
                  if (selectedDate < today) {
                    reservationDate.classList.add('is-invalid');
                    if (window.pushNotification) {
                      try { window.pushNotification('warning', 'Reservation date cannot be in the past'); } catch(_) {}
                    }
                    try {
                      if (reservationDate && typeof reservationDate.focus === 'function') reservationDate.focus();
                      if (reservationDate && reservationDate.scrollIntoView) reservationDate.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } catch(_) {}
                    return;
                  }
                }

                const fd = new FormData(form);
                const payload = {
                  trip_purpose: (fd.get('trip_purpose') || '').toString(),
                  start_location: (fd.get('start_location') || '').toString(),
                  destination: (fd.get('destination') || '').toString(),
                  reservation_date: (fd.get('reservation_date') || '').toString(),
                  departure_time: (fd.get('departure_time') || '') ? (fd.get('departure_time') + '') : null,
                  return_time: (fd.get('return_time') || '') ? (fd.get('return_time') + '') : null,
                  passenger_count: parseInt(fd.get('passenger_count') || '1', 10),
                  priority: (fd.get('priority') || 'normal').toString(),
                  preferred_vehicle: (fd.get('preferred_vehicle') || '').toString(),
                  notes: (fd.get('notes') || '').toString()
                };

                const sLat = (fd.get('start_lat') || '').toString();
                const sLng = (fd.get('start_lng') || '').toString();
                const dLat = (fd.get('destination_lat') || '').toString();
                const dLng = (fd.get('destination_lng') || '').toString();
                if (sLat && sLng) { payload.start_lat = parseFloat(sLat); payload.start_lng = parseFloat(sLng); }
                if (dLat && dLng) { payload.destination_lat = parseFloat(dLat); payload.destination_lng = parseFloat(dLng); }

                submitController = new AbortController();
                if (btnSubmit) btnSubmit.disabled = true;
                if (btnCancel) btnCancel.disabled = false;
                try {
                  const { ok, json } = await api('https://logistics2.jetlougetravels-ph.com/api/fleet.php?endpoint=reservations', { 
                    method: 'POST', 
                    body: JSON.stringify(payload), 
                    signal: submitController.signal 
                  });
                  if (ok && json?.success) {
                    suppressResetToastOnce = true;
                    form.reset();
                    form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
                    try { if (window.pushNotification) window.pushNotification('success', 'Reservation submitted'); else setFeedback('Reservation submitted', true); } catch(e) { setFeedback('Reservation submitted', true); }
                  } else {
                    try { if (window.pushNotification) window.pushNotification('danger', json?.message || 'Failed to create reservation'); else setFeedback(json?.message || 'Failed to create reservation', false); } catch(e) { setFeedback(json?.message || 'Failed to create reservation', false); }
                  }
                } catch (err) {
                  if (err && err.name === 'AbortError') {
                    try { if (window.pushNotification) window.pushNotification('warning', 'Submission cancelled'); } catch(e) {}
                  } else {
                    try { if (window.pushNotification) window.pushNotification('danger', 'Unexpected error during submission'); } catch(e) {}
                  }
                } finally {
                  submitController = null;
                  if (btnSubmit) btnSubmit.disabled = false;
                  if (btnCancel) btnCancel.disabled = false;
                }
              });

              form.addEventListener('reset', function (ev) {
                setTimeout(() => {
                  form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
                  suppressResetToastOnce = false;
                }, 0);
              });
            }

            if (btnCancel) {
              btnCancel.addEventListener('click', function () {
                if (submitController) {
                  try { submitController.abort(); } catch (e) {}
                  submitController = null;
                }
                if (form) {
                  suppressResetToastOnce = true;
                  form.reset();
                  form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
                  if (startLatInput) startLatInput.value = '';
                  if (startLngInput) startLngInput.value = '';
                  if (destLatInput) destLatInput.value = '';
                  if (destLngInput) destLngInput.value = '';
                  if (startGroup) { try { startGroup.clearLayers(); } catch(_){} }
                  if (destGroup) { try { destGroup.clearLayers(); } catch(_){} }
                  if (map && startMarker) { try { map.removeLayer(startMarker); } catch(_){} startMarker = null; }
                  if (map && destMarker) { try { map.removeLayer(destMarker); } catch(_){} destMarker = null; }
                  if (map && routeLine) { try { map.removeLayer(routeLine); } catch(_){} routeLine = null; try { window.__vrdsResvRouteLine = null; } catch(_){} }
                  try { if (window.pushNotification) window.pushNotification('info', 'Reservation form cancelled'); } catch(_) {}
                }
              });
            }

            // Wire address fields
            if (startInput) {
              startInput.addEventListener('focus', () => { lastFocused = 'start'; ensureMap(); });
              startInput.addEventListener('blur', async () => {
                if (suppressStartBlurOnce) { suppressStartBlurOnce = false; return; }
                const val = startInput.value;
                if (!val) return;
                const g = await geocodeAddress(val);
                if (g) setStartPoint(g.lat, g.lon, g.display_name || val);
              });
              startInput.addEventListener('input', async (e) => {
                if (!e.isTrusted) return;
                lastFocused = 'start';
                const val = (startInput && startInput.value) ? startInput.value.trim() : '';
                clearTimeout(startTypingTimer);
                if (val.length < 4) return;
                const mySeq = ++startGeocodeSeq;
                startTypingTimer = setTimeout(async () => {
                  const g = await geocodeAddress(val);
                  if (mySeq !== startGeocodeSeq) return;
                  if (g) setStartPoint(g.lat, g.lon, g.display_name || val);
                }, 900);
              });
              startInput.addEventListener('keydown', async (e) => {
                if (e.key === 'Enter') {
                  e.preventDefault();
                  const val = startInput.value;
                  if (!val) return;
                  const g = await geocodeAddress(val);
                  if (g) setStartPoint(g.lat, g.lon, g.display_name || val);
                }
              });
            }

            if (destInput) {
              destInput.addEventListener('focus', () => { lastFocused = 'dest'; ensureMap(); });
              destInput.addEventListener('blur', async () => {
                if (suppressDestBlurOnce) { suppressDestBlurOnce = false; return; }
                const val = destInput.value;
                if (!val) return;
                const g = await geocodeAddress(val);
                if (g) setDestPoint(g.lat, g.lon, g.display_name || val);
              });
              destInput.addEventListener('input', async (e) => {
                if (!e.isTrusted) return;
                lastFocused = 'dest';
                const val = (destInput && destInput.value) ? destInput.value.trim() : '';
                clearTimeout(destTypingTimer);
                if (val.length < 4) return;
                const mySeq = ++destGeocodeSeq;
                destTypingTimer = setTimeout(async () => {
                  const g = await geocodeAddress(val);
                  if (mySeq !== destGeocodeSeq) return;
                  if (g) setDestPoint(g.lat, g.lon, g.display_name || val);
                }, 900);
              });
              destInput.addEventListener('keydown', async (e) => {
                if (e.key === 'Enter') {
                  e.preventDefault();
                  const val = destInput.value;
                  if (!val) return;
                  const g = await geocodeAddress(val);
                  if (g) setDestPoint(g.lat, g.lon, g.display_name || val);
                }
              });
            }

            // Initialize
            setTimeout(() => {
              ensureMap();
              if (dateInput) dateInput.addEventListener('input', updateSubmitState);
              if (timeInput) timeInput.addEventListener('input', updateSubmitState);
              updateSubmitState();
            }, 100);

            // Removed driver/vehicle loaders; call only if still defined elsewhere
            if (typeof loadReservations === 'function') {
              loadReservations();
            }
          });
        })();
    </script>
@endpush
