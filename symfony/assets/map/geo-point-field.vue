<template>
  <div>
    <div ref="form" class="invisible" style="height: 0">
      <slot name="form" />
    </div>
    <div v-if="!requiredField">
      <button class="btn btn-secondary mb-3" data-cy="geo-point-field-toggler-btn" @click.prevent="showMap = !showMap">
        <span class="btn-label"
          ><span class="action-label">{{ showMap ? deleteLabel : addLabel }}</span></span
        >
      </button>
    </div>
    <MapBox v-if="latLngInitialized && showMap" :center="latLng" data-cy="geo-point-field-map-box">
      <MapBoxMarker v-model:lat-lng="latLng" :draggable="true" />
    </MapBox>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { defaultLatLng } from './constants'

export default defineComponent({
  name: 'GeoPointField',
  props: {
    requiredField: {
      type: Boolean,
      default: true,
    },
    deleteLabel: {
      type: String,
      required: true,
    },
    addLabel: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      latLng: defaultLatLng,
      latitudeInput: null as HTMLInputElement | null,
      longitudeInput: null as HTMLInputElement | null,
      latLngInitialized: false,
      showMap: false,
      areInputsInitiallyHydrated: false,
    }
  },
  computed: {
    formattedInputLatLng(): [string, string] {
      const [lat, lng] = this.latLng
      return [this.inputFormat(lat), this.inputFormat(lng)]
    },
  },
  watch: {
    latLng() {
      const [lat, lng] = this.formattedInputLatLng
      if (!this.latitudeInput || !this.longitudeInput) {
        throw new Error('latitude and longitude should not be null')
      }
      this.latitudeInput.value = lat
      this.longitudeInput.value = lng
    },
    showMap: {
      handler() {
        if (this.showMap) {
          this.syncInputsFromMap()
        } else {
          this.tearDownMap()
        }
      },
    },
  },
  mounted() {
    this.latitudeInput = (this.$refs.form as HTMLFormElement).querySelector("input[name*='latitude']")
    this.longitudeInput = (this.$refs.form as HTMLFormElement).querySelector("input[name*='longitude']")
    if (!this.latitudeInput || !this.longitudeInput) {
      throw new Error('latitude and longitude inputs should exist')
    }
    this.syncMapFromInputs()
    if (this.requiredField || this.areInputsInitiallyHydrated) {
      this.showMap = true
    }
  },
  methods: {
    syncMapFromInputs() {
      let hasValues = 0
      if (this.latitudeInput?.value && this.latitudeInput.value.length > 0) {
        this.latLng = [parseFloat(this.latitudeInput.value.replace(',', '.')), this.latLng[1]]
        hasValues++
      }
      if (this.longitudeInput?.value && this.longitudeInput.value.length > 0) {
        this.latLng = [this.latLng[0], parseFloat(this.longitudeInput.value.replace(',', '.'))]
        hasValues++
      }
      if (hasValues === 2) {
        this.areInputsInitiallyHydrated = true
      }
      this.latLngInitialized = true
    },
    inputFormat(geopoint: number): string {
      return geopoint.toString().replace('.', ',')
    },
    syncInputsFromMap() {
      this.latitudeInput = (this.$refs.form as HTMLFormElement).querySelector("input[name*='latitude']")
      this.longitudeInput = (this.$refs.form as HTMLFormElement).querySelector("input[name*='longitude']")
      if (!this.latitudeInput || !this.longitudeInput) {
        throw new Error('latitude and longitude inputs should exist')
      }
      this.latitudeInput.value = this.formattedInputLatLng[0]
      this.longitudeInput.value = this.formattedInputLatLng[1]
      this.latLngInitialized = true
    },
    tearDownMap() {
      this.longitudeInput!.value = ''
      this.latitudeInput!.value = ''
    },
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
