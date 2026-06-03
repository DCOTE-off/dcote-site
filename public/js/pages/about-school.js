document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('imageModal')
  const overlay = modal.querySelector('.modal-overlay')
  const modalImg = document.getElementById('modalTargetImg')
  const modalCaption = document.getElementById('modalCaption')
  const modalDownload = modal.querySelector('.modal-download')
  const modalClose = modal.querySelector('.modal-close')
  const modalNext = modal.querySelector('.modal-next')
  const modalPrev = modal.querySelector('.modal-prev')

  let modalImages = []
  let modalIndex = 0

  function getDownloadUrl(image) {
    return image.dataset.download?.trim() || image.currentSrc || image.src
  }

  function showModalImage(index) {
    modalIndex = (index + modalImages.length) % modalImages.length

    const image = modalImages[modalIndex]
    const src = image.currentSrc || image.src
    const downloadUrl = getDownloadUrl(image)

    modalImg.src = src
    modalImg.alt = image.alt || ''
    modalDownload.href = downloadUrl

  }

  function openModal(images, index) {
    modalImages = images
    showModalImage(index)
    modal.style.display = 'flex'
  }

  function closeModal() {
    modal.style.display = 'none'
    modalImg.src = ''
    modalImages = []
    modalIndex = 0
  }

    function disableScroll() {
    document.addEventListener('wheel', blockDefault, {
        passive: false
    });
    document.addEventListener('touchmove', blockDefault, {
        passive: false
    });
    document.addEventListener('keydown', blockScrollKeys);
    }

    function enableScroll() {
        document.removeEventListener('wheel', blockDefault);
        document.removeEventListener('touchmove', blockDefault);
        document.removeEventListener('keydown', blockScrollKeys);
    }

    function blockDefault(e) {
        e.preventDefault();
    }

    function blockScrollKeys(e) {
        const keys = ['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End'];
        if (keys.includes(e.key)) {
            e.preventDefault();
        }
    }
  modalClose.addEventListener('click', closeModal)

  modalNext.addEventListener('click', () => {
    if (!modalImages.length) return
    showModalImage(modalIndex + 1)
  })

  modalPrev.addEventListener('click', () => {
    if (!modalImages.length) return
    showModalImage(modalIndex - 1)
  })

  modal.addEventListener('click', (event) => {
    if (event.target === overlay) {
      closeModal()
    }
  })

  document.addEventListener('keydown', (event) => {
    if (modal.style.display !== 'flex') return

    if (event.key === 'Escape') closeModal()
    if (event.key === 'ArrowRight') showModalImage(modalIndex + 1)
    if (event.key === 'ArrowLeft') showModalImage(modalIndex - 1)
  })

function waitForImages(images) {
  return Promise.all(
    images.map((image) => {
      if (image.complete && image.naturalWidth > 0) {
        return Promise.resolve()
      }

      if (image.decode) {
        return image.decode().catch(() => {})
      }

      return new Promise((resolve) => {
        image.addEventListener('load', resolve, { once: true })
        image.addEventListener('error', resolve, { once: true })
      })
    })
  )
}

document.querySelectorAll('.embla').forEach(async (root) => {
  const slides = Array.from(root.querySelectorAll('.embla__slide'))
  const images = slides
    .map((slide) => slide.querySelector('.carousel-img'))
    .filter(Boolean)

  await waitForImages(images)

  const embla = EmblaCarousel(root, {
    loop: slides.length > 2,
    align: 'center',
    containScroll: false,
    skipSnaps: false,
  })

  function updateSelected() {
    const selected = embla.selectedScrollSnap()

    slides.forEach((slide, index) => {
      const isSelected = index === selected
      const image = slide.querySelector('.carousel-img')

      slide.classList.toggle('is-selected', isSelected)

      if (image) {
        image.classList.toggle('is-clickable', !isSelected)
      }
    })
  }

  slides.forEach((slide, index) => {
    const image = slide.querySelector('.carousel-img')
    if (!image) return

    image.addEventListener('click', (event) => {
      event.preventDefault()
      event.stopPropagation()

      const selected = embla.selectedScrollSnap()

      if (index !== selected) {
        embla.scrollTo(index)
        return
      }

      openModal(images, index)
    })
  })

  embla.on('select', updateSelected)
  embla.on('reInit', updateSelected)

  updateSelected()

  requestAnimationFrame(() => {
    root.classList.add('is-ready')
  })
})
})