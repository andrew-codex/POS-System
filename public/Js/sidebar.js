function confirmLogout(event) {
    Swal.fire({
        title: "Are you sure?",
        text: "You will be logged out of the system.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Logout",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}