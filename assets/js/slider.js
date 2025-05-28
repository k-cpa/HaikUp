import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

// Import Swiper styles

export default class Slider {
    constructor(element) {
        // Initialisation des éléments DOM essentiels
        this.element = element;
        this.mainContainer = element.querySelector('.swiper-container');
        this.wrapper = element.querySelector('.swiper-wrapper');
        this.maxSlides = this.wrapper ? parseInt(this.wrapper.dataset.maxSlide) || 3 : 3;

        // Vérifier si l'élément existe
        if (!this.mainContainer) {
            console.error('Slider container not found');
            return;
        }

        // Initialiser le slider
        this.initGallery();
    }

    initGallery() {
        // Configuration de base du slider
        const swiperConfig = {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            initialSlide: 0,
            spaceBetween: 20,
            loop: false,
            watchOverflow: false,

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },

            // Pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },

            // Responsive breakpoints
            breakpoints: {
                // When window width is >= 768px
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                // When window width is >= 1024px
                1024: {
                    slidesPerView: this.maxSlides,
                    spaceBetween: 20,
                }
            },

            // Events
            on: {
                init: () => {
                    console.log('Swiper initialized');
                },
                slideChange: () => {
                    console.log('Slide changed');
                }
            }
        };

        // Créer l'instance Swiper
        this.swiper = new Swiper(this.mainContainer, swiperConfig);
    }

    // Méthode pour détruire le slider si nécessaire
    destroy() {
        if (this.swiper) {
            this.swiper.destroy();
            this.swiper = null;
        }
    }

    // Méthode pour mettre à jour le slider si les slides changent
    update() {
        if (this.swiper) {
            this.swiper.update();
        }
    }
}