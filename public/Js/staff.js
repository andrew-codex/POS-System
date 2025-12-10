function confirmDelete(formId) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}


function confirmStatusChange(form, action) {
    let actionText = action === 'activate' ? 'activate' : 'deactivate';
    let buttonColor = action === 'activate' ? '#28a745' : '#6c757d';

    Swal.fire({
        title: `Are you sure you want to ${actionText} this user?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: buttonColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: actionText.charAt(0).toUpperCase() + actionText.slice(1),
    }).then((result) => {
        if (result.isConfirmed) {
            
            form.submit();
        }
    });
}


