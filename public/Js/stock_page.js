$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $categoryFilter = $("#categoryFilter");
    const $tableRows = $(".data-table tbody tr");
    const $emptyState = $("#emptyState");
    const $resultCount = $("#resultCount");
    const $table = $(".data-table");

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
            const category = $row.find("td:eq(1)").text().toLowerCase();
            const quantity = $row.find("td:eq(2)").text().toLowerCase();

            const categoryMatch =
                currentCategory === "" || categoryId == currentCategory;

            const searchMatch =
                productName.includes(searchValue) ||
                category.includes(searchValue) ||
                quantity.includes(searchValue) ||
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
        } else {
            $table.show();
            $emptyState.hide();
            $resultCount.show();
            $resultCount.html(`Showing ${visibleCount} stocks`);
        }
    }
});
