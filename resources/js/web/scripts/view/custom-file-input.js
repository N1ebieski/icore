jQuery(document).on('readyAndAjax', function () {
    $(".custom-file-input").each(function () {
        $(this).on("change", function () {
            let fileName = $(this).val().split("\\").pop();

            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
});
