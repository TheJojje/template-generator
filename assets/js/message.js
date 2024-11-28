document.addEventListener("DOMContentLoaded", function () {
    const message = document.getElementById("message");
    if (message) {
        setTimeout(() => {
            message.classList.add("hide");
        }, 4000);

        setTimeout(() => {
            if (message && message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 4500);
    }
});