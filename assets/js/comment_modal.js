document.addEventListener('DOMContentLoaded', function () {
console.log('comment modal OK');
    // Boutons commentaire
    document.querySelectorAll('.comment-button').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        });
    });

    // Ferme la modale via le bouton X
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        });
    });

    // Ferme si on clique en dehors du contenu
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('comment-modal')) {
            event.target.style.display = 'none';
        }
    });
});
