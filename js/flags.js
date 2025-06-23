document.addEventListener("DOMContentLoaded", function () {
    const flagCheckbox = document.getElementById("flag");
    const flagInputGroup = document.getElementById("flagInputGroup");

    // Funkcja, która ukrywa lub pokazuje input w zależności od stanu checkboxa
    function toggleInputVisibility() {
        if (flagCheckbox.checked) {
            flagInputGroup.classList.remove('d-none')
            flagInputGroup.classList.add('d-flex') // Pokazuje pole tekstowe
        } else {
            flagInputGroup.classList.add('d-none'); // Ukrywa pole tekstowe
        }
    }

    // Sprawdzanie stanu checkboxa przy załadowaniu strony
    toggleInputVisibility();

    // Nasłuchuj zmian stanu checkboxa
    flagCheckbox.addEventListener("change", toggleInputVisibility);
});