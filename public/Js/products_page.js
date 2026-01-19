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

$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $categoryFilter = $("#category-filter");
    const $tableRows = $(".data-table tbody tr");
    const $emptyState = $("#emptyState");
    const $resultCount = $("#resultCount");
    const $table = $(".data-table");
    const $paginationLinks = $(".pagination-links");

    let currentCategory = "";

    $categoryFilter.on("change", function () {
        currentCategory = $(this).val();
        performSearch();
    });

    $searchInput.on("keyup", function () {
        performSearch();
    });

    function performSearch() {
        const searchValue = $searchInput.val().toLowerCase();
        let visibleCount = 0;

        $tableRows.each(function () {
            const $row = $(this);
            const categoryId = $row.data("category");

            const productName = $row.find("td:eq(0)").text().toLowerCase();
            const description = $row.find("td:eq(1)").text().toLowerCase();
            const price = $row.find("td:eq(2)").text().toLowerCase();
            const barcode = $row.find("td:eq(3)").text().toLowerCase();
            const category = $row.find("td:eq(4)").text().toLowerCase();

            const categoryMatch =
                currentCategory === "" || categoryId == currentCategory;

            const searchMatch =
                productName.includes(searchValue) ||
                description.includes(searchValue) ||
                price.includes(searchValue) ||
                barcode.includes(searchValue) ||
                category.includes(searchValue) ||
                searchValue === "";

            if (categoryMatch && searchMatch) {
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
            $paginationLinks.hide();
        } else {
            $table.show();
            $emptyState.hide();
            $resultCount.show();
            $resultCount.html(`Showing ${visibleCount} products`);

            if (searchValue !== "" || currentCategory !== "") {
                $paginationLinks.find("div:last-child").hide();
            } else {
                $paginationLinks.find("div:last-child").show();
            }
        }
    }

    performSearch();
});
