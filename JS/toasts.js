document.addEventListener("DOMContentLoaded", function () {
    var toastEl = document.querySelector(".toast");
    if (toastEl) {
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});