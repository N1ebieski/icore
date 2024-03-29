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


window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');

require('bootstrap');

require('jscroll');

require('jquery.cookie');

require('jquery.easing');

require('trumbowyg');
require('trumbowyg/dist/langs/pl.min.js');
require('trumbowyg/dist/plugins/colors/trumbowyg.colors.min.js');
require('trumbowyg/dist/plugins/history/trumbowyg.history.min.js');
require('trumbowyg/dist/plugins/table/trumbowyg.table.min.js');

require('jquery-lazy/jquery.lazy.js');

require('jquery.tagsinput-revisited/src/jquery.tagsinput-revisited.js');

require('bootstrap-confirmation2/dist/bootstrap-confirmation.js');

require('bootstrap-select/dist/js/bootstrap-select.min.js');
require('bootstrap-select/dist/js/i18n/defaults-pl_PL.min.js');

require('ajax-bootstrap-select/dist/js/ajax-bootstrap-select.min.js');
require('ajax-bootstrap-select/dist/js/locale/ajax-bootstrap-select.pl-PL.min.js');

require('pickadate/lib/picker.js');
require('pickadate/lib/picker.date.js');
require('pickadate/lib/picker.time.js');
//require('pickadate/lib/compressed/translations/pl_PL.js');

require('magnific-popup/dist/jquery.magnific-popup.js');

require('jquery-googlemap/jquery.googlemap.js');

require('corejs-typeahead/dist/typeahead.jquery.js');
window.Bloodhound = require('corejs-typeahead/dist/bloodhound.js');

$.ajaxSetup({
    'headers': {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ajaxError(function (event, request) {
    if (request.status === 401) {
        window.location = '/login';

        return;
    }
});

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// window.axios = require('axios');
//
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

// let token = document.head.querySelector('meta[name="csrf-token"]');
//
// if (token) {
//     window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
// } else {
//     console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
// }

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });