import * as basicLightbox from 'basiclightbox'

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.ea-lightbox-thumbnail').forEach((imageElement) => {
    new Image(imageElement)
  })
})

class Image {
  constructor(public field: Element) {
    this.field.addEventListener('click', this.#renderLightbox.bind(this))
  }

  #renderLightbox(evt: Event) {
    evt.preventDefault()
    const selector = this.field.getAttribute('data-ea-lightbox-content-selector')
    if (!selector) {
      return
    }
    const lightboxContent = document.querySelector(selector)?.innerHTML
    if (!lightboxContent) {
      return
    }
    const lightbox = basicLightbox.create(lightboxContent)
    lightbox.show()
  }
}
