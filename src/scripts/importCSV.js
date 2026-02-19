document.addEventListener("DOMContentLoaded", () => {
    // --- Elements ---
    const loadCsvBtn = document.getElementById("load_csv");
    const importCsvBtn = document.getElementById("import_csv");
    const fileInput = document.getElementById("file-upload");
    const form = document.getElementById("csvForm");
    const closeErrorsBtn = document.getElementById("closeErrors");
    const errorContainer = document.getElementById("error-container");
    const fileTextLabel = document.querySelector(".file-upload-text");

    // --- Interaction Logic ---

    // File Selection Handler
    if (fileInput) {
        fileInput.addEventListener("change", () => {
            // Enable load button
            if (loadCsvBtn) loadCsvBtn.disabled = false;

            // Update label text
            if (fileInput.files[0] && fileTextLabel) {
                fileTextLabel.innerText = `${fileInput.files[0].name}: cliquez sur "charger" pour visionner un extrait.`;
            }
        });
    }

    // Load Button Handler
    if (loadCsvBtn) {
        loadCsvBtn.addEventListener("click", () => {
            // Slight delay to toggle buttons visual state
            setTimeout(() => {
                loadCsvBtn.disabled = true;
                if (importCsvBtn) importCsvBtn.disabled = false;
            }, 500);
        });
    }

    // Form Submission Routing
    if (form) {
        form.addEventListener("submit", (e) => {
            const submitter = e.submitter;

            // Route to appropriate handler based on which button was clicked
            // load_csv = show file preview
            // import_csv = import file
            if (submitter && submitter.name === "load_csv") {
                form.action = "importCSV.php";
            } else if (submitter && submitter.name === "import_csv") {
                form.action = "../actions/importCSV_action.php";
            }
        });
    }

    // Error Container Handler
    if (closeErrorsBtn && errorContainer) {
        closeErrorsBtn.addEventListener("click", () => {
            errorContainer.remove();
        });
    }
});
