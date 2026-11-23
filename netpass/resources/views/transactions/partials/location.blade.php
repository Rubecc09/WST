<div class="col-12">
    <h3>Application location</h3>
    <p class="field-hint" id="map-status" aria-live="polite">{{ config('services.google_maps.key') ? 'Click the map to select a location, or enter coordinates below.' : 'Map unavailable: configure GOOGLE_MAPS_KEY. You can still enter coordinates below.' }}</p>
    <div id="application-map" style="height:340px;width:100%;border:1px solid #00f0ff;background:#141720;" aria-label="Application location map"></div>
</div>
<div class="col-12 col-md-6"><x-form.field name="latitude" label="Latitude"><input class="form-control" id="latitude" name="latitude" type="number" min="-90" max="90" step="any" value="{{ old('latitude', '14.5995') }}"></x-form.field></div>
<div class="col-12 col-md-6"><x-form.field name="longitude" label="Longitude"><input class="form-control" id="longitude" name="longitude" type="number" min="-180" max="180" step="any" value="{{ old('longitude', '120.9842') }}"></x-form.field></div>
@if (config('services.google_maps.key'))
<script src="{{ asset('js/application-map.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&amp;loading=async&amp;callback=initApplicationMap" onerror="applicationMapError()"></script>
@endif
