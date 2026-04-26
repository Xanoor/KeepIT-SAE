document.addEventListener("DOMContentLoaded", () => {
    var CREATE_VAR_BTN = document.getElementsByClassName("CREATE_VAR_BTN");

    if (CREATE_VAR_BTN) {
        Array.from(CREATE_VAR_BTN).forEach((button) => {
            button.addEventListener("click", createVarBtn_handler);
        });
    }
});

function createVarBtn_handler() {
    // TODO: open the modal
}
