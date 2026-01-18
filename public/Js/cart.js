$(document).ready(function () {


    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "1000",
        extendedTimeOut: "1000",
        preventDuplicates: true
    };


 


    let cart = JSON.parse(localStorage.getItem("pos_cart")) || [];
    cart = cart.filter(item => item && item.id && item.name && item.price && item.qty);


    if (window.saleSuccess && window.saleSuccess !== '') {
        toastr.success(window.saleSuccess);
        cart = [];
        localStorage.removeItem('pos_cart');
    }

    function saveCart() {
        localStorage.setItem("pos_cart", JSON.stringify(cart));
    }

    function updateTotals() {
        let subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        $('#subtotal').text("₱" + subtotal.toFixed(2));
        $('#total').text("₱" + subtotal.toFixed(2));
        saveCart();
    }

    function renderCart() {
        let cartBody = $('#cart-items');
        cartBody.empty();
        if (cart.length === 0) {
            cartBody.html('<p>No items in cart</p>');
            updateTotals();
            return;
        }
        cart.forEach(item => {
            if (!item || !item.name || !item.price || !item.qty) return;
            let desc = item.description || "No description";
            cartBody.append(`
                <div class="cart-item">
                    <div class="left">
                        <strong>${item.name}</strong> <small>${desc}</small>
                        <br>
                        <small>₱${item.price.toFixed(2)} each</small>
                    </div>
                    <div class="right qty-controls">
                        <button class="qty-minus" data-id="${item.id}">−</button>
                        <input class="qty-input" data-id="${item.id}" type="number" min="1" value="${item.qty}">
                        <button class="qty-plus" data-id="${item.id}">+</button>
                    </div>
                </div>
            `);
        });
        updateTotals();
    }

    renderCart();

    $('#category-select').on('change', function () {
        $('#filters-form').submit();
    });


const beep = new Audio("data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAESsAACJWAAACABAAZGF0YRAAAAAA//////8=");
const beepError = new Audio("data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAESsAACJWAAACABAAZGF0YRQAAAAA//////8=");

function addProductToCart(id, name, price, description = "", stock = 0) {
    if (!id || !name || !price) return;

    let existing = cart.find(item => item.id == id);

    if (existing) {
        if (existing.qty < stock) {
            existing.qty++;
            saveCart();
            renderCart();
            toastr.success(`${name} added to cart!`);
            beep.play();
        } else {
            toastr.error(`Cannot exceed available stock (${stock})`);
             beepError.play();
             return;
        }
    } else {
        if (stock > 0) {
            cart.push({ id, name, price, qty: 1, description });
            saveCart();
            renderCart();
            toastr.success(`${name} added to cart!`);
              beep.play();
              return;
        } else {
            toastr.error(`${name} is out of stock!`);
              beepError.play();
              return;
        }
    }
}


$(document).on('click', '.product-card', function () {
    addProductToCart(
        $(this).data('id'),
        $(this).data('name'),
        parseFloat($(this).data('price')),
        $(this).data('description') || "",
        parseInt($(this).data('stock')) || 0
    );
});



    $(document).on('click', '.qty-plus', function () {
        let id = $(this).data('id');
        let item = cart.find(i => i.id == id);
        if (!item) return;


        let stock = parseInt($(`.product-card[data-id='${id}']`).data('stock')) || 0;

        if (item.qty < stock) {
            item.qty++;
            saveCart();
            renderCart();
        } else {
            toastr.error(`Cannot exceed available stock (${stock})`);
             beepError.play();
                return;
        }
    });

    $(document).on('click', '.qty-minus', function () {
        let item = cart.find(i => i.id == $(this).data('id'));
        if (!item) return;
        if (item.qty > 1) item.qty--; else cart = cart.filter(i => i.id != item.id);
        saveCart(); renderCart();
    });

    $(document).on('input', '.qty-input', function () {
        let id = $(this).data('id');
        let item = cart.find(i => i.id == id);
        if (!item) return;

        let val = parseInt($(this).val());


        if (isNaN(val) || val < 1) {
            val = 1;
        }


        let stock = parseInt($(`.product-card[data-id='${id}']`).data('stock')) || 0;


        if (val > stock) {
            val = stock;
            toastr.error(`Cannot exceed available stock (${stock})`);
        }

        item.qty = val;

        saveCart();
        renderCart();
        updateTotals();
        beep.play();
    });



    $('#open-payment').on('click', function () {
        let total = parseFloat($('#total').text().replace('₱', '')) || 0;
        $('#payment-total').text("₱" + total.toFixed(2));
        $('#payment-amount').val('');
        $('#payment-change').text("₱0.00");
        $('#confirm-payment').prop('disabled', true);
        $('#paymentModal').appendTo('body');
        $('#paymentModal').modal('show');
    });

    $(document).on('input', '#payment-amount', calculateChange);

    $('.denom-btn').on('click', function () {
        let addValue = parseFloat($(this).data('value'));
        let current = parseFloat($('#payment-amount').val()) || 0;
        $('#payment-amount').val(current + addValue);
        calculateChange();
    });

    function calculateChange() {
        let total = parseFloat($('#total').text().replace('₱', '')) || 0;
        let amount = parseFloat($('#payment-amount').val()) || 0;
        let change = amount - total;
        if (change >= 0) {
            $('#payment-change').text("₱" + change.toFixed(2));
            $('#confirm-payment').prop('disabled', false);
        } else {
            $('#payment-change').text("₱0.00");
            $('#confirm-payment').prop('disabled', true);
        }
    }


    $('#sale-form').on('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            toastr.error("Cart is empty!");
            return;
        }

        let total = parseFloat($('#total').text().replace('₱', '')) || 0;
        let amountReceived = parseFloat($('#payment-amount').val()) || 0;
        let change = amountReceived - total;

        if (change < 0) {
            e.preventDefault();
            toastr.error("Amount received is less than total!");
            return;
        }


        $('#sale-form input[name^="cart"]').remove();

        cart.forEach((item, index) => {
            $('#sale-form').append(`<input type="hidden" name="cart[${index}][id]" value="${item.id}">`);
            $('#sale-form').append(`<input type="hidden" name="cart[${index}][qty]" value="${item.qty}">`);
            $('#sale-form').append(`<input type="hidden" name="cart[${index}][price]" value="${item.price}">`);
        });


        $('#form-total').val(total.toFixed(2));
        $('#form-change').val(change.toFixed(2));

        $('#confirm-payment').prop('disabled', true);
    });

});
