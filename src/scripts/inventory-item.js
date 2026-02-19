document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.getElementById('submit-delete');
    
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            const confirmed = confirm("⚠️ Êtes-vous sûr de vouloir SUPPRIMER cet appareil ? Cette action est irréversible.");
            if (!confirmed) {
                e.preventDefault(); // Prevents the form from submitting
            }
        });
    }
});
