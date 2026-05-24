document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.embla').forEach((root) => {
    const embla = EmblaCarousel(root, {
      loop: true,
      align: 'center',
      containScroll: false,
      skipSnaps: false,
    })

    const slides = Array.from(root.querySelectorAll('.embla__slide'))

    function updateSelected() {
      const selected = embla.selectedScrollSnap()

      slides.forEach((slide, index) => {
        const isSelected = index === selected

        slide.classList.toggle('is-selected', isSelected)

        const image = slide.querySelector('.carousel-img')
        if (image) {
          image.classList.toggle('is-clickable', !isSelected)
        }
      })
    }

    slides.forEach((slide, index) => {
      const image = slide.querySelector('.carousel-img')
      if (!image) return

      image.addEventListener('click', () => {
        if (index === embla.selectedScrollSnap()) return

        embla.scrollTo(index)
      })
    })

    embla.on('select', updateSelected)
    embla.on('reInit', updateSelected)

    updateSelected()
  })
})