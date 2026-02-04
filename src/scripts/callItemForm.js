document.addEventListener("DOMContentLoaded", () => {
    const select = document.getElementById("create-item-select");
    const container = document.getElementById("create-item-form");

    if (!select) return;
    select.addEventListener("change", () => {
        const type = select.value;

        if (!type) {
            container.innerHTML = "";
            return;
        }

        fetch(`../actions/loadItemForm_action.php?type=${type}`)
            .then((res) => res.text())
            .then((html) => {
                container.innerHTML = html;
            });
    });
});
