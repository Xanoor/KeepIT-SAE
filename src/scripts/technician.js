function fillEditTechForm(row) {
    const id = row.getAttribute('data-id');
    const nom = row.getAttribute('data-nom');
    const prenom = row.getAttribute('data-prenom');
    const login = row.getAttribute('data-login');

    const title = document.querySelector('.edit-tech-title');
    title.innerHTML = `${prenom} ${nom} <span class="tech-number">Technicien n°${id}</span>`;

    const fields = document.querySelectorAll('.edit-form-section input');
    fields[0].value = prenom;
    fields[1].value = nom;
    fields[2].value = login;
    fields[3].value = "";

    document.querySelectorAll('.tech-row').forEach(r => r.classList.remove('active'));
    row.classList.add('active');
}