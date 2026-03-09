// TOAST
function showToast(message, type = "success") {
    const colors = {
        success: "bg-success text-white",
        error: "bg-danger text-white",
        warning: "bg-warning text-dark",
        info: "bg-info text-white",
    };

    const toastHTML = `
        <div class="toast align-items-center ${colors[type]} border-0 show mb-2">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    const container = document.getElementById("toast-container");
    container.insertAdjacentHTML("beforeend", toastHTML);

    setTimeout(() => {
        container.firstElementChild?.remove();
    }, 4000);
}

// DELETE
window.deletefn = function (e) {
    if (!confirm("Are you sure you want to delete this hospital and doctors?")) {
        return;
    }

    let id = e.dataset.id;
    let action = e.dataset.action;
    let method = e.dataset.method ?? "POST";

    fetch(action, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ id }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, "success");
                e.closest("tr")?.remove();
            } else {
                showToast(data.message || "Delete failed", "error");
            }
        })
        .catch(() => {
            showToast("Something went wrong", "error");
        });
};
// IMAGE CHANGE
const inputs = document.getElementsByClassName('img_change');

Array.from(inputs).forEach(input => {
    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const imgLoad = document.querySelector('.img_load');
        if (!imgLoad) return;
        // if (imgLoad) {
    imgLoad.classList.remove('d-none');
// }
        const reader = new FileReader();
        reader.onload = function (e) {
            imgLoad.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
});


