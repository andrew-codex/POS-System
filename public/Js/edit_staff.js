function confirmEdit(formId) {
    Swal.fire({
        title: "Are you sure you want to edit this staff member?",
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


function passwordMatch() {
    const password = document.getElementById("floatingPassword").value;
    const confirmPassword = document.getElementById("floatingConfirmPassword").value;
    const messageElement = document.getElementById("password-match-message");
    const submitBtn = document.getElementById("submitBtn");

    
    if (confirmPassword === "") {
        messageElement.textContent = "";
        document.getElementById("floatingConfirmPassword").classList.remove("is-valid", "is-invalid");
        submitBtn.disabled = true;
        return;
    }

    if (password === confirmPassword) {
        messageElement.style.color = "green";
        messageElement.textContent = "Passwords match.";
        document.getElementById("floatingConfirmPassword").classList.add("is-valid");
        document.getElementById("floatingConfirmPassword").classList.remove("is-invalid");
        submitBtn.disabled = false;
    } else {
        messageElement.style.color = "red";
        messageElement.textContent = "Passwords do not match.";
        document.getElementById("floatingConfirmPassword").classList.add("is-invalid");
        document.getElementById("floatingConfirmPassword").classList.remove("is-valid");
        submitBtn.disabled = true; 
    }
}


function togglePassword(inputId, iconElement) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        iconElement.classList.remove("bi-eye-slash");
        iconElement.classList.add("bi-eye");
    } else {
        input.type = "password";
        iconElement.classList.remove("bi-eye");
        iconElement.classList.add("bi-eye-slash");
    }
}