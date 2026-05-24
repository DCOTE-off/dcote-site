document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.embla').forEach((root) => {
    const embla = EmblaCarousel(root, {
      loop: true,
      align: 'center',
      containScroll: false,
      skipSnaps: false,
    })

    const slides = root.querySelectorAll('.embla__slide')

    function updateSelected() {
      const selected = embla.selectedScrollSnap()

      slides.forEach((slide, index) => {
        slide.classList.toggle('is-selected', index === selected)
      })
    }

    embla.on('init', updateSelected)
    embla.on('select', updateSelected)
    embla.on('reInit', updateSelected)

    updateSelected()
  })
})