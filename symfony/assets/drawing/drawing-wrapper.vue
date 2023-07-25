<template>
  <drawing-box
    :image-src="imageSrc"
    :initial-arr-arr-points="initialLineBoulder?.arrArrPoints"
    :loading="loading"
    @save="onSave"
  ></drawing-box>
</template>

<script lang="ts" setup>
import { ref, Ref } from 'vue'
import DrawingBox from './drawing-box.vue'
import { LineBoulderInterface, SavePayloadInterface } from './model'

const headers = {
  Accept: 'application/ld+json',
  'Content-Type': 'application/ld+json',
}

// eslint-disable-next-line no-undef
const props = defineProps({
  imageSrc: {
    type: String,
    required: true,
  },
  initialLineBoulder: {
    type: Object as () => LineBoulderInterface,
    default: null,
  },
  boulderIri: {
    type: String,
    required: true,
  },
  rockImageIri: {
    type: String,
    required: true,
  },
})

console.log(props)

const loading = ref(false)

const currentLineBoulder: Ref<LineBoulderInterface | null> = ref(props.initialLineBoulder ?? null)

async function postLineBoulder(payload: SavePayloadInterface) {
  const { arrArrPoints, path: smoothLine } = payload
  const response = await fetch('/admin/line_boulders', {
    method: 'POST',
    headers,
    body: JSON.stringify({
      boulder: props.boulderIri,
      rockImage: props.rockImageIri,
      arrArrPoints,
      smoothLine,
    }),
  })

  const json = await response.json()
  console.log(json)
  if (response.ok) {
    currentLineBoulder.value = {
      '@id': json['@id'],
      arrArrPoints: json.arrArrPoints,
    }
  }
  return response
}

async function editLineBoulder(payload: SavePayloadInterface) {
  if (!currentLineBoulder.value) {
    throw Error('currentLineBoulder should not be null')
  }
  const { arrArrPoints, path: smoothLine } = payload
  const response = await fetch(currentLineBoulder.value['@id'], {
    method: 'PUT',
    headers,
    body: JSON.stringify({
      arrArrPoints,
      smoothLine,
    }),
  })

  const json = await response.json()
  console.log(json)
  return response
}

async function deleteLineBoulder() {
  if (!currentLineBoulder.value) {
    throw Error('currentLineBoulder should not be null')
  }
  const response = await fetch(currentLineBoulder.value['@id'], {
    method: 'DELETE',
    headers,
  })

  currentLineBoulder.value = null

  return response
}

async function onSave(payload?: SavePayloadInterface) {
  loading.value = true
  if (payload) {
    if (!currentLineBoulder.value) {
      console.log('post')
      postLineBoulder(payload)
    } else {
      console.log('put')
      editLineBoulder(payload)
    }
  } else if (currentLineBoulder.value) {
    console.log('delete')
    deleteLineBoulder()
  }
  loading.value = false
}
</script>
