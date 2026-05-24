document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.embla').forEach((root) => {
    const embla = EmblaCarousel(root, {
      loop: true,
      align: 'center',
      containScroll: false,
      skipSnaps: false,
    })

    const slides = Array.from(root.querySelectorAll('.embla__slide'))
    let isLocked = false
    let unlockTimer = null

    function blockEvent(event) {
      if (!isLocked) return

      event.preventDefault()
      event.stopImmediatePropagation()
    }

    root.addEventListener('pointerdown', blockEvent, true)
    root.addEventListener('mousedown', blockEvent, true)
    root.addEventListener('touchstart', blockEvent, true)
    root.addEventListener('click', blockEvent, true)

    function setLocked(value) {
      isLocked = value
      root.classList.toggle('is-click-locked', value)
      updateSelected()
    }

    function lockTemporarily() {
      setLocked(true)
      clearTimeout(unlockTimer)

      unlockTimer = setTimeout(() => {
        setLocked(false)
      }, 350)
    }

    function updateSelected() {
      const selected = embla.selectedScrollSnap()

      slides.forEach((slide, index) => {
        const isSelected = index === selected
        const image = slide.querySelector('.carousel-img')

        slide.classList.toggle('is-selected', isSelected)

        if (image) {
          image.classList.toggle('is-clickable', !isSelected && !isLocked)
        }
      })
    }

    slides.forEach((slide, index) => {
      const image = slide.querySelector('.carousel-img')
      if (!image) return

      image.addEventListener('click', (event) => {
        event.preventDefault()
        event.stopPropagation()

        if (isLocked) return
        if (index === embla.selectedScrollSnap()) return

        lockTemporarily()
        embla.scrollTo(index)
      })
    })

    embla.on('select', updateSelected)

    embla.on('reInit', () => {
      clearTimeout(unlockTimer)
      setLocked(false)
    })

    updateSelected()
  })
})