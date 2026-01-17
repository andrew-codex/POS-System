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



$(document).ready(function() {
    
    const $searchInput = $('#searchInput');
    const $tableRows = $('.data-table tbody tr');
    const $emptyState = $('#emptyState');
    const $resultCount = $('#resultCount');
    const $table = $('.data-table');
    

    $searchInput.on('keyup', function() {
        performSearch();
    });
    

    function performSearch() {
        const searchValue = $searchInput.val().toLowerCase();
        let visibleCount = 0;
        
        $tableRows.each(function() {
            const $row = $(this);
     
            const categoryName = $row.find('td:eq(0)').text().toLowerCase();
            const description = $row.find('td:eq(1)').text().toLowerCase();
            
        
            const searchMatch = 
                categoryName.includes(searchValue) || 
                description.includes(searchValue) || 
                searchValue === '';
            
       
            if (searchMatch) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });
        if (visibleCount === 0) {
            $table.hide();
            $emptyState.show();
            $resultCount.hide();
        } else {

            $table.show();
            $emptyState.hide();
            $resultCount.show();
            $resultCount.html(`Showing ${visibleCount} categories`);
        }
    }
});