<template>
  <div>
    <div class="drawing-container">
      <img ref="image" :data-src="imageSrc" alt="" class="lozad drawing-img" />
      <svg
        ref="svg"
        :width="imgCurrentWidth"
        :height="imgCurrentHeight"
        :viewBox="`0 0 ${imgWidth} ${imgHeight}`"
        xmlns="http://www.w3.org/2000/svg"
        version="1.1"
      >
        <path fill="none" :d="path" stroke-linecap="round" stroke="red" stroke-width="6px" class="path" />
      </svg>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Ref, ref } from 'vue'
import { useDrawing, Point } from './useDrawing'
import useStackImage from './useStackImage'

// eslint-disable-next-line no-undef
const props = defineProps({
  imageSrc: {
    type: String,
    required: true,
  },
  initialArrArrPoints: {
    type: Array as () => Point[][],
    default: null,
  },
})

const svg: Ref<SVGElement | undefined> = ref()

const { path } = useDrawing(svg, props.initialArrArrPoints)

const image: Ref<HTMLImageElement | undefined> = ref()
const { imgCurrentHeight, imgCurrentWidth, imgHeight, imgWidth, containerHeightPx } = useStackImage(
  image,
  props.imageSrc,
)
</script>

<style scoped>
.drawing-container {
  position: relative;
  max-width: 550px;
  height: v-bind(containerHeightPx);
  margin-left: auto;
  margin-right: auto;
}

.drawing-img {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: auto;
  user-select: none;
  pointer-events: none;
}
svg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
  user-select: none;
}
</style>
