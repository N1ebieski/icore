jQuery(document).ready(function() {
    let $map = $("#map");

    if ($map.length) {
        $map.data = $map.data();

        if (typeof $map.data.addressMarker !== 'undefined' && $map.data.addressMarker.length) {
            $map.googleMap({
                zoom: parseInt($map.data.zoom),
                coords: $map.data.coords,                
                scrollwheel: true,
                type: "ROADMAP"
            })
            .addClass($map.data.containerClass);
    
            $.each($map.data.addressMarker, function(key, value) {
                $map.addMarker({
                    address: value
                });
            });
        }
    }
});