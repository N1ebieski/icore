<div 
    id="map" 
    data-container-class="{{ $containerClass }}"
    data-address-marker="{{ $addressMarker }}" 
    data-zoom="{{ $zoom }}"
></div>

@push('script')
<script 
    defer 
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemap.api_key') }}&callback=initMap" 
    type="text/javascript"
></script>
@endpush