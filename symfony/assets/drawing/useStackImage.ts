/* eslint-disable no-param-reassign */
import { Ref, ref, onMounted } from 'vue'

export default function useStackImage(
  image: Ref<HTMLImageElement | undefined>,
  imageSrc: string,
): {
  imgCurrentHeight: Ref<string>
  imgCurrentWidth: Ref<string>
  imgWidth: Ref<string>
  imgHeight: Ref<string>
  containerHeightPx: Ref<string>
  imageLoaded: Ref<boolean>
} {
  const imageLoaded = ref(false)
  const imgWidth = ref('0')
  const imgHeight = ref('0')
  const imgCurrentWidth = ref('0')
  const imgCurrentHeight = ref('0')
  const containerHeightPx = ref('0px')

  function onResize() {
    if (!image.value) {
      return
    }
    containerHeightPx.value = `${image.value.height}px`
    imgCurrentWidth.value = `${image.value.width}`
    imgCurrentHeight.value = `${image.value.height}`
    imgWidth.value = image.value.naturalWidth.toString()
    imgHeight.value = image.value.naturalHeight.toString()
  }

  onMounted(() => {
    window.addEventListener('resize', onResize)

    if (image.value) {
      image.value.src = imageSrc
      image.value.addEventListener(
        'load',
        () => {
          imageLoaded.value = true
          onResize()
        },
        true,
      )
    }
  })

  return {
    imgCurrentHeight,
    imgCurrentWidth,
    imgWidth,
    imgHeight,
    containerHeightPx,
    imageLoaded,
  }
}
