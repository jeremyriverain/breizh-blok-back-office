import './styles/app.css'

import './leaflet'
import './lightbox'

import lozad from 'lozad'

import { createApp } from 'vue'

import MapBoxMarker from './map/map-box-marker.vue'
import MapBox from './map/map-box.vue'
import GeoPointField from './map/geo-point-field.vue'

import DrawingWrapper from './drawing/drawing-wrapper.vue'
import StackImage from './drawing/stack-image.vue'

function vueFactory() {
  const app = createApp({})
  app.config.compilerOptions.delimiters = ['##{', '}##']
  return app
}

const mapboxApp = vueFactory()
mapboxApp.component('GeoPointField', GeoPointField)
mapboxApp.component('MapBox', MapBox)
mapboxApp.component('MapBoxMarker', MapBoxMarker)
mapboxApp.mount('#vue-mapbox')

const lineDrawerApp = vueFactory()
lineDrawerApp.component('DrawingWrapper', DrawingWrapper)
lineDrawerApp.component('StackImage', StackImage)
lineDrawerApp.mount('#vue-draw-line')
lineDrawerApp.mount('#ea-index-Boulder #main')
lineDrawerApp.mount('.ea-detail-Boulder #main .vue-draw-line')

document.addEventListener('DOMContentLoaded', () => {
  const sources = document.querySelectorAll('.js-hidden-vich')
  sources.forEach((s) => {
    const img = document.createElement('img')
    img.src = `/uploads/${s.getAttribute('image')}`
    img.classList.add('img-fluid')
    if (s.parentNode) {
      s.parentNode.appendChild(img)
    }
  })
})

const observer = lozad() // lazy loads elements with default selector as '.lozad'
observer.observe()
