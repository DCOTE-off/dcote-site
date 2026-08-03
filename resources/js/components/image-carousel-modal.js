import EmblaCarousel from 'embla-carousel';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('imageModal');
    const overlay = modal?.querySelector('.modal-overlay');
    const modalImage = document.getElementById('modalTargetImg');
    const modalCaption = document.getElementById('modalCaption');
    const downloadLink = modal?.querySelector('.modal-download');
    const closeButton = modal?.querySelector('.modal-close');
    const nextButton = modal?.querySelector('.modal-next');
    const previousButton = modal?.querySelector('.modal-prev');

    if (
        !modal
        || !overlay
        || !modalImage
        || !downloadLink
        || !closeButton
        || !nextButton
        || !previousButton
    ) {
        return;
    }

    let modalImages = [];
    let modalIndex = 0;

    function getDownloadUrl(image) {
        return image.dataset.download?.trim() || image.currentSrc || image.src;
    }

    function showModalImage(index) {
        if (modalImages.length === 0) {
            return;
        }

        modalIndex = (index + modalImages.length) % modalImages.length;

        const image = modalImages[modalIndex];
        modalImage.src = image.currentSrc || image.src;
        modalImage.alt = image.alt || '';
        downloadLink.href = getDownloadUrl(image);

        if (modalCaption && modal.hasAttribute('data-caption-from-alt')) {
            modalCaption.textContent = image.alt || '';
        }
    }

    function openModal(images, index) {
        modalImages = images;
        showModalImage(index);
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
        modalImage.src = '';
        modalImages = [];
        modalIndex = 0;
    }

    function waitForImages(images) {
        return Promise.all(images.map((image) => {
            if (image.complete && image.naturalWidth > 0) {
                return Promise.resolve();
            }

            if (typeof image.decode === 'function') {
                return image.decode().catch(() => {});
            }

            return new Promise((resolve) => {
                image.addEventListener('load', resolve, { once: true });
                image.addEventListener('error', resolve, { once: true });
            });
        }));
    }

    async function setupCarousel(root) {
        const slides = Array.from(root.querySelectorAll('.embla__slide'));
        const images = slides
            .map((slide) => slide.querySelector('.carousel-img'))
            .filter(Boolean);

        if (images.length === 0) {
            return;
        }

        await waitForImages(images);

        const carousel = EmblaCarousel(root, {
            loop: slides.length > 2,
            align: 'center',
            containScroll: false,
            skipSnaps: false,
        });

        function updateSelectedSlide() {
            const selectedIndex = carousel.selectedScrollSnap();

            slides.forEach((slide, index) => {
                const image = slide.querySelector('.carousel-img');
                const isSelected = index === selectedIndex;

                slide.classList.toggle('is-selected', isSelected);
                image?.classList.toggle('is-clickable', !isSelected);
            });
        }

        slides.forEach((slide, index) => {
            const image = slide.querySelector('.carousel-img');

            if (!image) {
                return;
            }

            image.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (index !== carousel.selectedScrollSnap()) {
                    carousel.scrollTo(index);
                    return;
                }

                openModal(images, index);
            });
        });

        carousel.on('select', updateSelectedSlide);
        carousel.on('reInit', updateSelectedSlide);
        updateSelectedSlide();

        requestAnimationFrame(() => root.classList.add('is-ready'));
    }

    closeButton.addEventListener('click', closeModal);
    nextButton.addEventListener('click', () => showModalImage(modalIndex + 1));
    previousButton.addEventListener('click', () => showModalImage(modalIndex - 1));

    modal.addEventListener('click', (event) => {
        if (event.target === overlay) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (modal.style.display !== 'flex') {
            return;
        }

        if (event.key === 'Escape') {
            closeModal();
        } else if (event.key === 'ArrowRight') {
            showModalImage(modalIndex + 1);
        } else if (event.key === 'ArrowLeft') {
            showModalImage(modalIndex - 1);
        }
    });

    document.querySelectorAll('.embla').forEach(setupCarousel);
});
