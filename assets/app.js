import './styles/app.scss';
import Slider from './js/slider';
import './js/comment_modal.js';
import './js/ajax_subscription.js';
import './js/dropMenuHaiku.js';
import './js/adjustPaddingHeader.js';

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
                        const isLiked = data.liked; 

                        if (isLiked) {
                            // Mettre à jour visuellement que l'élément est liké
                            event.target.textContent = 'favorite';  
                            event.target.classList.add('liked');
                            event.target.setAttribute('data-liked', 'true');
                        } else {
                            // Mettre à jour visuellement que l'élément n'est plus liké
                            event.target.textContent = 'favorite_border';  
                            event.target.classList.remove('liked');
                            event.target.setAttribute('data-liked', 'false');
                        }
                        // On fait un event target sur le haiku le plus proche pour récup 
                        const haikuCard = event.target.closest('.haiku');
                        const likeText = haikuCard.querySelector('.nbr_likes p');

                        if (likeText && typeof data.likesCount !== 'undefined') {
                            likeText.textContent = `${data.likesCount} J'aime`;
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

    // SLIDER
    const sliders = document.querySelectorAll('.swiper-parent')

    sliders.forEach((sliderElement) => {
        new Slider(sliderElement);
    });


    // Gestion burger menu header pour mobile et pour desktop
    const hamMenu = document.querySelector('.ham_burger');
    const hamDesktop = document.querySelector('.ham_burger_desktop');
    const offScreenMenu = document.querySelector('.off_screen_menu');
    const offScreenDesktop = document.querySelector('.off_screen_desktop');

    if(hamMenu) {
        hamMenu.addEventListener('click', () => {
            hamMenu.classList.toggle('active');
            offScreenMenu.classList.toggle('active');
        })
    }
    if(hamDesktop) {
        hamDesktop.addEventListener('click', () => {
            hamDesktop.classList.toggle('active');
            offScreenDesktop.classList.toggle('active');
        })
    }

});



