<script lang="ts">
import { Map, marker, Marker, LatLngTuple } from 'leaflet'
import { defineComponent, PropType } from 'vue'

export default defineComponent({
  name: 'MapBoxMarker',
  inject: ['map'],
  props: {
    latLng: {
      type: Object as PropType<LatLngTuple>,
      required: true,
    },
    label: {
      type: String,
      required: false,
      default: null,
    },
    draggable: {
      type: Boolean,
      default: false,
    },
  },
  emits: {
    'update:latLng': (payload: [number, number]) => Array.isArray(payload) && payload.length === 2,
  },
  data() {
    return {
      markerInstance: undefined as Marker | undefined,
    }
  },
  watch: {
    lngLat(newValue) {
      if (!this.markerInstance) {
        return
      }
      this.markerInstance.setLatLng(newValue)
    },
    map(map: Map) {
      if (!map) {
        return
      }

      const markerInstance = marker(this.latLng, {
        draggable: this.draggable,
        autoPan: true,
      }).addTo(map)

      if (this.label) {
        markerInstance.bindPopup(this.label)
      }

      markerInstance.on('dragend', () => {
        const { lat, lng } = markerInstance.getLatLng()
        this.$emit('update:latLng', [lat, lng])
      })

      this.markerInstance = markerInstance
    },
  },
  render() {
    return null
  },
})
</script>
