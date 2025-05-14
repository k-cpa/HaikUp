import './styles/app.scss';
document.addEventListener('DOMContentLoaded', () => {
    const likeIcons = document.querySelectorAll('.like-button');

    likeIcons.forEach(icon => {
        icon.addEventListener('click', async (event) => {
            event.preventDefault();
            if (event.target && event.target.classList.contains('like-icon')) {
                // Faire l'appel AJAX
                const haikuId = event.target.dataset.haikuId;
                fetch(`/feed/${haikuId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ id: haikuId })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // Gérer les erreurs si nécessaire
                        console.log('Erreur avec l\'appel AJAX');
                    } else {
                        // Si la réponse indique que le like a été effectué, mettre à jour l'interface
                        const isLiked = data.liked; // true si le like a été ajouté, false sinon

                        if (isLiked) {
                            // Mettre à jour visuellement que l'élément est liké
                            event.target.textContent = 'favorite';  // Icône "likée"
                            event.target.classList.add('liked');
                            event.target.setAttribute('data-liked', 'true');
                        } else {
                            // Mettre à jour visuellement que l'élément n'est plus liké
                            event.target.textContent = 'favorite_border';  // Icône "non likée"
                            event.target.classList.remove('liked');
                            event.target.setAttribute('data-liked', 'false');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    // Gérer l'erreur en rétablissant l'état précédent si une erreur survient
                    event.target.classList.toggle('liked');
                    event.target.textContent = isLiked ? 'favorite_border' : 'favorite';

                });
            }
        });
    });
});
