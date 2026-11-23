window.applicationMapError = function () {
    document.getElementById('map-status').textContent = 'Google Maps could not load. You can still enter coordinates below.';
};
window.gm_authFailure = window.applicationMapError;
window.initApplicationMap = function () {
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');
    function position() {
        const lat = Number(latitude.value), lng = Number(longitude.value);
        return latitude.value !== '' && longitude.value !== '' && Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180 ? { lat, lng } : null;
    }
    const center = position() || { lat: 14.5995, lng: 120.9842 };
    const map = new google.maps.Map(document.getElementById('application-map'), { center, zoom: 12, streetViewControl: false });
    const selection = new google.maps.Circle({ map, center, radius: 60, fillColor: '#fcee0a', fillOpacity: 0.8, strokeColor: '#00f0ff', strokeWeight: 2, clickable: false });
    map.addListener('click', function (event) {
        if (!event.latLng) return;
        latitude.value = event.latLng.lat().toFixed(7);
        longitude.value = event.latLng.lng().toFixed(7);
        selection.setCenter(event.latLng);
    });
    [latitude, longitude].forEach(input => input.addEventListener('change', function () {
        const selected = position();
        if (selected) { selection.setCenter(selected); map.panTo(selected); }
    }));
};
