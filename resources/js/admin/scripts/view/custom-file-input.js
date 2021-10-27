jQuery(document).on('readyAndAjax', function () {
    $(".custom-file-input").each(function () {
        $(this).on("change", function () {
            var files = [];
            for (var i = 0; i < $(this)[0].files.length; i++) {
                files.push($(this)[0].files[i].name);
            }

            $(this).siblings(".custom-file-label").addClass("selected").html(files.join(', '));
        });
    });
});