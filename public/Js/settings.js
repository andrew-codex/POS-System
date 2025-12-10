function generalSettingsForm(formId) {
    Swal.fire({
        title: "Are you sure you want to edit ?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, update it!",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}


document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');

    if (activeTab) {
        const tabTrigger = document.querySelector(`#${activeTab}-tab`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
});
