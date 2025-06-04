    
document.addEventListener('DOMContentLoaded', function () {

    // GESTION AJAX DE L'ABONNEMENT 
    const subscribeButtons = document.querySelectorAll('.subscribe-button');
    subscribeButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            
            const userId = event.target.dataset.userId;

            fetch(`/profil/${userId}/subscription`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.log('Erreur avec l\'appel AJAX');
                    return;
                }
                const isSubscribed = data.subscribed; // true = abonné, false = désabonné
                if (isSubscribed) {
                    event.target.textContent = 'Se désabonner';
                    event.target.classList.add('subscribed');
                    event.target.setAttribute('data-subscribed', 'true');
                } else {
                    event.target.textContent = 'S’abonner';
                    event.target.classList.remove('subscribed');
                    event.target.setAttribute('data-subscribed', 'false');
                }
                console.log('Data :', data)
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
        });
    });
});
