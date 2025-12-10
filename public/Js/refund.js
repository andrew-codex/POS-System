document.addEventListener("input", function(e) {
    if (e.target.classList.contains("refund-qty")) {
        updateRowAmount(e.target);
        let mainRow = e.target.closest("tr");
        if (mainRow.querySelector(".is-changed").checked) {
            updateExchangeDifference(mainRow.nextElementSibling);
        }
        calculateTotalRefund();
    }
});

document.querySelectorAll(".is-changed").forEach(chk => {
    chk.addEventListener("change", function() {
        let mainRow = this.closest("tr");
        let exchangeRow = mainRow.nextElementSibling;
        exchangeRow.classList.toggle("d-none", !this.checked);
        if (!this.checked) {
            exchangeRow.querySelector(".new-price").value = "";
            exchangeRow.querySelector(".new-price-hidden").value = "";
            exchangeRow.querySelector(".difference").value = "";
        }
        calculateTotalRefund();
    });
});

document.querySelectorAll(".new-product").forEach(sel => {
    sel.addEventListener("change", function() {
        let exchangeRow = this.closest(".exchange-row");
        let price = parseFloat(this.selectedOptions[0].dataset.price) || 0;
        exchangeRow.querySelector(".new-price").value = price;
        exchangeRow.querySelector(".new-price-hidden").value = price;
        updateExchangeDifference(exchangeRow);
        calculateTotalRefund();
    });
});

function updateRowAmount(input) {
    let row = input.closest("tr");
    let qty = parseFloat(input.value) || 0;
    let price = parseFloat(row.querySelector(".refund-price").value) || 0;
    row.querySelector(".refund-amount").textContent = "₱" + (qty * price).toFixed(2);
}

function updateExchangeDifference(row) {
    let mainRow = row.previousElementSibling;
    let qty = parseFloat(mainRow.querySelector(".refund-qty").value) || 0;
    let oldPrice = parseFloat(mainRow.querySelector(".refund-price").value) || 0;
    let newPrice = parseFloat(row.querySelector(".new-price-hidden").value) || 0;
    if (!newPrice) { row.querySelector(".difference").value = ""; return; }
    let diff = (oldPrice - newPrice) * qty;
    row.querySelector(".difference").value = diff >= 0
        ? "Refund - ₱" + diff.toFixed(2)
        : "Customer Add ₱" + Math.abs(diff).toFixed(2);
}

function calculateTotalRefund() {
    let total = 0;
    document.querySelectorAll(".refund-row").forEach(mainRow => {
        let qty = parseFloat(mainRow.querySelector(".refund-qty").value) || 0;
        let price = parseFloat(mainRow.querySelector(".refund-price").value) || 0;
        if (!mainRow.querySelector(".is-changed").checked) total += qty * price;
        else {
            let exchangeRow = mainRow.nextElementSibling;
            let newPrice = parseFloat(exchangeRow.querySelector(".new-price-hidden").value) || 0;
            total += (price - newPrice) * qty;
        }
    });
    total = total < 0.01 ? 0.01 : total;
    document.getElementById("refund_amount").value = total.toFixed(2);
}