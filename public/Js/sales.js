$(document).ready(function () {
    let searchTimeout;

    function fetchSales() {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(function () {
            const filters = {
                search: $("#search-input").val().trim(),
                status: $("#status-filter").val(),
                start_date: $("#start-date").val(),
                end_date: $("#end-date").val(),
            };

            $("#sales-tbody").css("opacity", "0.5");

            $.ajax({
                url: window.appConfig.routes.salesSearch,
                method: "GET",
                data: filters,
                success: function (response) {
                    renderSales(response.sales);
                },
                error: function () {
                    alert("Error fetching sales data");
                },
                complete: function () {
                    $("#sales-tbody").css("opacity", "1");
                },
            });
        }, 300);
    }

    function renderSales(sales) {
        const tbody = $("#sales-tbody");
        const emptyState = $("#empty-state");
        const paginationInfo = $("#pagination-info");

        tbody.empty();

        if (sales.length === 0) {
            emptyState.show();
            tbody.closest("table").hide();
            paginationInfo.hide();
            return;
        }

        emptyState.hide();
        tbody.closest("table").show();
        paginationInfo.hide();

        sales.forEach(function (sale) {
            const date = new Date(sale.created_at);
            const formattedDate = date.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
                hour: "numeric",
                minute: "2-digit",
                hour12: true,
            });

            const statusBadge = getStatusBadge(sale.status);
            const refundUrl = window.appConfig.routes.refundsIndex.replace(
                ":id",
                sale.id,
            );

            const row = `
                <tr>
                    <td>${sale.invoice_no}</td>
                    <td>${formattedDate}</td>
                    <td>${sale.items_count || 0}</td>
                    <td>${sale.cashier ? sale.cashier.name : "N/A"}</td>
                    <td>₱${parseFloat(sale.total_amount).toFixed(2)}</td>
                    <td class="badge-status">${statusBadge}</td>
                    <td>
                        <a href="${refundUrl}" class="btn btn-sm btn-primary">
                            View Refunds
                        </a>
                    </td>
                </tr>
            `;

            tbody.append(row);
        });
    }

    function getStatusBadge(status) {
        const badges = {
            completed: '<span class="badge-completed">Completed</span>',
            pending: '<span class="badge-pending">Pending</span>',
            canceled: '<span class="badge-canceled">Canceled</span>',
            exchanged: '<span class="badge-exchanged">Exchanged</span>',
            refunded: '<span class="badge-refunded">Refunded</span>',
            partially_refunded:
                '<span class="badge-partially-refunded">Partially Refunded</span>',
        };
        return (
            badges[status] ||
            '<span class="badge-secondary">' + status + "</span>"
        );
    }

    $("#search-input").on("input", fetchSales);
    $("#status-filter").on("change", fetchSales);
    $("#start-date").on("change", fetchSales);
    $("#end-date").on("change", fetchSales);

    $("#clear-filters").on("click", function () {
        $("#search-input").val("");
        $("#status-filter").val("");
        $("#start-date").val("");
        $("#end-date").val("");
        location.reload();
    });
});
