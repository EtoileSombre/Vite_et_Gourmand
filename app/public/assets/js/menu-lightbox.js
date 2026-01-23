// Gestion du clic sur les images du carrousel pour ouvrir la lightbox
document.addEventListener('DOMContentLoaded', function() {
    const carouselItems = document.querySelectorAll('.carousel-item-clickable');
    const lightbox = new bootstrap.Modal(document.getElementById('lightbox'));
    const lightboxCarousel = document.getElementById('lightboxCarousel');
    
    carouselItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            // Ne pas ouvrir si on clique sur les boutons de navigation
            if (e.target.closest('.carousel-control-prev') || e.target.closest('.carousel-control-next')) {
                return;
            }
            
            // Synchroniser avec le carrousel de la lightbox
            const lightboxCarouselInstance = bootstrap.Carousel.getInstance(lightboxCarousel) || new bootstrap.Carousel(lightboxCarousel);
            lightboxCarouselInstance.to(index);
            
            // Ouvrir la lightbox
            lightbox.show();
        });
    });
});
