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

document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav-link');
    const loadingOverlay = document.getElementById('contentLoadingOverlay');
    const pageContent = document.getElementById('pageContentWrapper');
    const currentPath = window.location.pathname;
    
    // Function to show loading state
    function showLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('show');
        }
        if (pageContent) {
            pageContent.classList.add('loading');
        }
    }
    
    // Function to hide loading state
    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('show');
        }
        if (pageContent) {
            pageContent.classList.remove('loading');
            pageContent.classList.add('page-enter');
            
            // Remove animation class after it completes
            setTimeout(() => {
                pageContent.classList.remove('page-enter');
            }, 300);
        }
    }
    
    // Add click event to all sidebar navigation links
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const linkPath = new URL(this.href).pathname;
            
            // Only show loading if navigating to a different page
            if (linkPath !== currentPath) {
                showLoading();
                
                // Add loading state to the clicked link
                sidebarLinks.forEach(l => l.classList.remove('loading'));
                this.classList.add('loading');
            } else {
                // Prevent navigation to the same page
                e.preventDefault();
            }
        });
    });
    
    // Hide loading when page loads (handles back/forward button navigation)
    window.addEventListener('pageshow', function(event) {
        hideLoading();
        // Remove loading class from all links
        sidebarLinks.forEach(l => l.classList.remove('loading'));
    });
    
    // Hide loading on initial page load
    hideLoading();
    
    // Handle logout button separately
    const logoutTrigger = document.getElementById('logout-trigger');
    const logoutForm = document.getElementById('logout-form');
    
    if (logoutTrigger && logoutForm) {
        logoutTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            showLoading();
            
            // Update spinner text for logout
            const spinnerText = document.querySelector('#contentLoadingOverlay .spinner-text');
            if (spinnerText) {
                spinnerText.textContent = 'Logging out...';
            }
            
            // Submit the logout form after a short delay
            setTimeout(() => {
                logoutForm.submit();
            }, 100);
        });
    }
});