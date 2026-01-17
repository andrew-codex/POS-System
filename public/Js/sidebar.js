document.getElementById('logout-trigger').addEventListener('click', function(e) {
    console.log('Logout trigger clicked');
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your session!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, logout!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
});