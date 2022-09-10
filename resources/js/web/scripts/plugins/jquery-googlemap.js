/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on('ready.n1ebieski/icore/web/scripts/plugins/jquery-googlemap@init', function () {
    $('#map, .map').each(function () {
        let $map = $(this);

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
        
                $.each($map.data.addressMarker, function (key, value) {
                    $map.addMarker({
                        address: value
                    });
                });
            }
        }
    });
});
