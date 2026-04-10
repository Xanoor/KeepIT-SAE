const selectCheckboxBtn = document.getElementById("select-checkbox-button");
const selectCheckboxList = document.getElementById("select-checkbox-list");
const selectCheckboxes = Array.from(
    document.querySelectorAll('#select-checkbox-list input[type="checkbox"]'),
);

const action_select = document.getElementById("action_select");

function selectCheckboxBtn_clicked(e) {
    if (
        selectCheckboxList &&
        selectCheckboxBtn.parentElement == selectCheckboxList.parentElement
    ) {
        if (selectCheckboxList.style.display == "none") {
            selectCheckboxList.style.display = "flex";
            selectCheckboxList.parentElement.classList.add(
                "select-checkbox-active",
            );
        } else {
            selectCheckboxList.style.display = "none";
            selectCheckboxList.parentElement.classList.remove(
                "select-checkbox-active",
            );
        }
    }
}

function selectCheckbox_changed(e) {
    e = e.target;

    switch (e.id) {
        case "select-checkbox-all":
            selectCheckboxes.forEach((elem) => (elem.checked = e.checked));
            break;

        default:
            const all = document.getElementById("select-checkbox-all");
            if (all) {
                if (!e.checked) all.checked = false;
                else {
                    let i = 0;
                    selectCheckboxes.forEach((e) => {
                        if (e.checked) i += 1;
                    });
                    all.checked = selectCheckboxes.length - 1 == i; //Without all btn
                }
            }
            break;
    }
}

selectCheckboxes.forEach((el) =>
    el.addEventListener("change", (e) => selectCheckbox_changed(e)),
);

if (selectCheckboxBtn) {
    selectCheckboxBtn.addEventListener("click", (e) =>
        selectCheckboxBtn_clicked(e),
    );
}

function redirectAction(e) {
    if (e.target.value === "export") {
        const exportMenu = document.getElementById("exportMenuContainer");
        if (exportMenu) {
            const exportBtn = document.getElementById("export-menu-submit");
            if (exportBtn) {
                exportBtn.onclick = () => {
                    exportMenu.classList.add("hide-menu");
                };
            }
            exportMenu.classList.remove("hide-menu");
        }
    } else {
        window.location.href = e.target.value;
    }
}

// WCAG-compliant: navigation occurs only on explicit user action (click or Enter),
// not on option change via keyboard navigation.
if (action_select) {
    action_select.addEventListener("change", (e) => {
        if (e.isTrusted) {
            redirectAction(e);
        }
    });

    action_select.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            redirectAction(e);
        }
    });
}

const pageInput = document.querySelector(".page-num-input");
if (pageInput) {
    pageInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            //if the user press enter inside number input, submit the form
            e.preventDefault();
            this.form.submit();
        }
    });
}
