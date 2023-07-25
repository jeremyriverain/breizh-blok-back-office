<template>
  <div class="map-container">
    <div id="map" class="map-field"></div>
    <slot />
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, PropType } from 'vue'
import { map, tileLayer, Map, control, LatLngTuple } from 'leaflet'
import { defaultLatLng } from './constants'

export default defineComponent({
  name: 'MapBox',
  provide() {
    return {
      map: computed(() => this.mapInstance),
    }
  },
  props: {
    center: {
      type: Object as PropType<LatLngTuple>,
      default: defaultLatLng,
    },
    zoom: {
      type: Number,
      default: 12,
    },
  },
  data() {
    return {
      mapInstance: undefined as Map | undefined,
    }
  },
  watch: {
    center(newValue) {
      if (!this.mapInstance) {
        return
      }

      this.mapInstance.setView(newValue)
    },
  },
  mounted() {
    const googleStreets = tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    })
    const googleSat = tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    })

    const mapInstance = map('map', {
      center: this.center,
      zoom: this.zoom,
      layers: [googleStreets, googleSat],
    })
    const baseMaps = {
      'Google Street': googleStreets,
      'Google Satellite': googleSat,
    }

    control.layers(baseMaps, {}, { position: 'bottomleft' }).addTo(mapInstance)

    googleSat.addTo(mapInstance)
    this.mapInstance = mapInstance
  },
})
</script>

<style scoped>
.map-container {
  width: 100%;
  height: 400px;
  position: relative;
}

.map-field {
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
}

.map-menu {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 999;
  padding-left: 1rem;
  padding-top: 1rem;
}
</style>
