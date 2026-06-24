document.addEventListener("DOMContentLoaded", () => {
    var create_var_btn = document.getElementById("CREATE_VAR_BTN");
    var modal = document.getElementById("createVariableModal");

    if (CREATE_VAR_BTN && modal) {
        create_var_btn.addEventListener("click", () => {
            console.log("clicked");
            modal.classList.add("show-modal");
        });
    }

    var close_modal = document.getElementById("close-modal");
    if (close_modal) {
        close_modal.addEventListener("click", () => {
            modal.classList.remove("show-modal");
        });
    }
});
