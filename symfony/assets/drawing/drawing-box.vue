<template>
  <div>
    <div class="drawing-container">
      <img ref="image" :data-src="imageSrc" alt="" class="lozad drawing-img" data-cy="drawing-image" />
      <svg
        ref="svg"
        :width="imgCurrentWidth"
        :height="imgCurrentHeight"
        :viewBox="`0 0 ${imgWidth} ${imgHeight}`"
        xmlns="http://www.w3.org/2000/svg"
        version="1.1"
        @mousedown.prevent="onStart"
        @mousemove.prevent="onMove"
        @mouseup.prevent="onEnd"
        @touchstart.prevent="onStart"
        @touchmove.prevent="onMove"
        @touchend.prevent="onEnd"
      >
        <path fill="none" :d="path" stroke-linecap="round" stroke="red" stroke-width="4px" class="path" />
      </svg>
      <div v-if="imageLoaded" class="drawing-actions">
        <button
          class="btn btn-secondary text-primary btn-undo"
          href="#"
          aria-label="undo"
          :disabled="arrArrPoints.length === 0"
          @click.prevent="undo"
        >
          <i class="fa fa-rotate-left"></i>
        </button>
        <a class="btn btn-secondary text-danger btn-clear" href="#" aria-label="clear" @click.prevent="clear"
          ><i class="fa fa-fw fa-trash-o"></i
        ></a>
        <button
          class="btn btn-secondary text-primary btn-save"
          href="#"
          aria-label="save"
          :disabled="loading"
          @click.prevent="save"
        >
          <i v-if="!loading" class="fas fa-save"></i>
          <template v-else>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="sr-only">Loading...</span>
          </template>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Ref, ref } from 'vue'
import { useDrawing, Point } from './useDrawing'
import { SavePayloadInterface } from './model'
import useStackImage from './useStackImage'

const props = defineProps({
  imageSrc: {
    type: String,
    required: true,
  },
  initialArrArrPoints: {
    type: Array as () => Point[][],
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const svg: Ref<SVGElement | undefined> = ref()

const { onEnd, onMove, onStart, path, arrArrPoints } = useDrawing(svg, props.initialArrArrPoints)

const emit = defineEmits<{
  (e: 'save', payload?: SavePayloadInterface): void
}>()

function save() {
  // console.log(dataUrl.value)
  if (arrArrPoints.value && path.value) {
    emit('save', {
      arrArrPoints: arrArrPoints.value,
      path: path.value,
    })
  } else {
    emit('save')
  }
}

function undo() {
  if (arrArrPoints.value.length === 0) {
    return
  }
  arrArrPoints.value.pop()
}

function clear() {
  arrArrPoints.value = []
}

const image: Ref<HTMLImageElement | undefined> = ref()
const { imgCurrentHeight, imgCurrentWidth, imgHeight, imgWidth, containerHeightPx, imageLoaded } = useStackImage(
  image,
  props.imageSrc,
)
</script>

<style scoped>
.drawing-container {
  position: relative;
  max-width: 550px;
  height: v-bind(containerHeightPx);
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
  cursor: crosshair;
  user-select: none;
}

.drawing-actions {
  position: absolute;
  right: 10px;
  bottom: 10px;
  display: flex;
  justify-content: end;
}
.drawing-actions .btn {
  margin-left: 7px;
}
</style>
