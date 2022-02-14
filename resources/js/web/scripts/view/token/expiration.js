$(document).on(
    'change.n1ebieski/icore/web/scripts/view/token@custom',
    'select#expiration',
    function () {
        if ($(this).val() === 'custom') {
            $(this).replaceWith('<input type="number" id="expiration" name="expiration" class="form-control">');
        }
    }
);
