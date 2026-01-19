function confirmEdit(formId) {
    const form = document.getElementById(formId);
    const submitButton = document.getElementById("submitButton");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: "Are you sure you want to update this product?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            form.submit();
        },
        allowOutsideClick: () => !Swal.isLoading(),
    });
}
