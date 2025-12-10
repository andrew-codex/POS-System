function confirmCreate(formId) {
    const form = document.getElementById(formId);

  
    if (!form.checkValidity()) {
        form.reportValidity(); 
        return;
    }

  
    Swal.fire({
        title: "Are you sure you want to create this category?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); 
        }
    });
}