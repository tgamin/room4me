$(document).ready(function () {
    const requiredFieldsSelector = "input,textarea,select";
    const requiredFields = $(requiredFieldsSelector).filter("[required]:visible");
    
    function validate() {
        let errors = 0;
        requiredFields.each(function () {
            if ($(this).val() == "") {
                $(this).parent().find(".error").removeClass("hidden");
                errors++;
            } else {
                $(this).parent().find(".error").addClass("hidden");
            }
        });
        
        return errors === 0;
    }
    $('.user-form').on("submit", function (e) { 
        return validate();
    });
    $(requiredFieldsSelector).on("change", function (e) {
        if ($(this).val() != "") {
            $(this).parent().parent().find(".error").addClass("hidden");
        }
    });
    $('.user-form input[type="text"], input[type="email"], select').on("change", function (e) {
        e.preventDefault();
        const $form = $(this).parents('form');
        const reservationId = $form.find('input[name="reservationId"]').val();
        $.ajax({
            type: "POST",
            url: `/reservation/${reservationId}/update`,
            data: $form.serialize(),
            success: function (result) {
            },
        });
    });
    $('.user-form input[type="file"]').on("change", function (e) {
        e.preventDefault();
        const $form = $(this).parents('form');
        const reservationId = $form.find('input[name="reservationId"]').val();
        let data = new FormData();
        data.append("identity_document", $("#identity_document")[0].files[0]);
        $.ajax({
            type: "POST",
            url: `/reservation/${reservationId}/update-document`,
            enctype: "multipart/form-data",
            data: data,
            processData: false,
            contentType: false,
            success: function (result) {
                $(".btn-identity-document").attr("href", result);
                $(".btn-identity-document").removeClass("hidden");
                $(".btn-identity-document").addClass("block");
                $("#identity_document").val("");
            },
            error: function (data, textStatus, jqXHR) {
                alert(data.responseJSON.errors.identity_document[0]);
                $("#identity_document").val("");
            },
        });
    });
});