document
    .getElementById("logout-trigger")
    .addEventListener("click", function (e) {
        console.log("Logout trigger clicked");
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out of your session!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, logout!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("logout-form").submit();
            }
        });
    });

document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(".sidebar-nav-link");
    const loadingOverlay = document.getElementById("contentLoadingOverlay");
    const pageContent = document.getElementById("pageContentWrapper");
    const currentPath = window.location.pathname;

    function showLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add("show");
        }
        if (pageContent) {
            pageContent.classList.add("loading");
        }
    }

    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove("show");
        }
        if (pageContent) {
            pageContent.classList.remove("loading");
            pageContent.classList.add("page-enter");

            setTimeout(() => {
                pageContent.classList.remove("page-enter");
            }, 300);
        }
    }

    sidebarLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            const linkPath = new URL(this.href).pathname;

            if (linkPath !== currentPath) {
                showLoading();

                sidebarLinks.forEach((l) => l.classList.remove("loading"));
                this.classList.add("loading");
            } else {
                e.preventDefault();
            }
        });
    });

    window.addEventListener("pageshow", function (event) {
        hideLoading();
        sidebarLinks.forEach((l) => l.classList.remove("loading"));
    });

    hideLoading();
});
