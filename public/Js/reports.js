document.addEventListener('DOMContentLoaded', function () {
 
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if(activeTab){
        const triggerEl = document.querySelector(`#${activeTab}-tab`);
        if(triggerEl){
            new bootstrap.Tab(triggerEl).show();
        }
    }

 
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('shown.bs.tab', function (e) {
            const currentTab = e.target.getAttribute('data-bs-target').replace('#','');
            document.querySelectorAll('input[name="tab"]').forEach(input => {
                input.value = currentTab;
            });
        });
    });
});