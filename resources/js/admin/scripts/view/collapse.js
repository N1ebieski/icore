$(document).on(
    'change.n1ebieski/icore/admin/scripts/view/collapse@publishedAt',
    '[aria-controls="collapse-published-at"]',
    function () {
        if ($(this).val() == 0) $('#collapse-published-at').collapse('hide');
        else $('#collapse-published-at').collapse('show');
    }
);

$(document).on(
    'change.n1ebieski/icore/admin/scripts/view/collapse@activationAt',
    '[aria-controls="collapse-activation-at"]',
    function () {
        if ($(this).val() == 2) $('#collapse-activation-at').collapse('show');
        else $('#collapse-activation-at').collapse('hide');
    }
);