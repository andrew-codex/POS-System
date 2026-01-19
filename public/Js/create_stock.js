function confirmCreate(formId) {
    const form = document.getElementById(formId);
    const submitButton = document.getElementById("createStockButton");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Are you sure you want to create this stock?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            form.submit();
        },
        allowOutsideClick: () => !Swal.isLoading(),
    });
}

$(document).ready(function () {
    $("#product_id").select2({
        placeholder: "Select a product",
        allowClear: true,
        width: "100%",
        dropdownAutoWidth: true,
    });

    $(window).on("resize", function () {
        $("#product_id").each(function () {
            $(this).select2({
                width: "100%",
            });
        });
    });
});
