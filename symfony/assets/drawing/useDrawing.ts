import { ref, Ref, computed } from 'vue'
import { Nullable } from './model'

interface Point {
  x: number
  y: number
}

function smoothLine(points: Point[], tension = 1): string | null {
  const leftPoints = 8 // intervalle de points non pris en compte par l'algorithme de Catmull-Rom Spline

  if (points.length <= leftPoints + 2) {
    return null
  }

  const data = points.filter((p, i) => i % leftPoints === 0)

  const size = data.length
  const last = size - 2

  let smoothPath = `M${[data[0].x, data[0].y]}`

  for (let i = 0; i < size - 1; i += 1) {
    const x0 = i ? data[i - 1].x : data[0].x
    const y0 = i ? data[i - 1].y : data[0].y

    const x1 = data[i].x
    const y1 = data[i].y

    const x2 = data[i + 1].x
    const y2 = data[i + 1].y

    const x3 = i !== last ? data[i + 2].x : x2
    const y3 = i !== last ? data[i + 2].y : y2

    const cp1x = x1 + ((x2 - x0) / 6) * tension
    const cp1y = y1 + ((y2 - y0) / 6) * tension

    const cp2x = x2 - ((x3 - x1) / 6) * tension
    const cp2y = y2 - ((y3 - y1) / 6) * tension

    smoothPath += `C${[cp1x, cp1y, cp2x, cp2y, x2, y2]}`
  }

  return smoothPath
}

function polyline(points: Point[]): string {
  if (points.length < 2) {
    return ''
  }

  let result = `M${points[0].x},${points[0].y} `

  for (let i = 1; i < points.length; i += 1) {
    result += `L${points[i].x}, ${points[i].y}`
  }

  return result
}

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
function useDrawing(svg: Ref<SVGElement | undefined>, initialArrArrPoints?: Point[][]) {
  const isMoving = ref(false)
  const arrArrPoints: Ref<Point[][]> = ref(initialArrArrPoints ?? [])

  function extractCoords(e: MouseEvent | TouchEvent): Point | Nullable<Point> {
    try {
      if (!svg.value) {
        return {
          x: null,
          y: null,
        }
      }

      const svgRect = svg.value.getBoundingClientRect()

      const offsetX = e instanceof MouseEvent ? e.x : e.targetTouches[0]?.clientX
      const offsetY = e instanceof MouseEvent ? e.y : e.targetTouches[0]?.clientY

      if (!offsetX || !offsetY) {
        return {
          x: null,
          y: null,
        }
      }

      const viewBox = svg.value.getAttribute('viewBox')
      if (!viewBox) {
        return {
          x: null,
          y: null,
        }
      }
      const [, , w, h] = viewBox.split(' ').map((v) => parseInt(v, 10))

      return {
        x: (offsetX - svgRect.x) * (w / svg.value.clientWidth),
        y: (offsetY - svgRect.y) * (h / svg.value.clientHeight),
      }
    } catch (error) {
      console.error(error, e)
      return {
        x: null,
        y: null,
      }
    }
  }

  function onStart(e: MouseEvent | TouchEvent) {
    isMoving.value = true
    const { x, y } = extractCoords(e)
    if (x && y) {
      arrArrPoints.value[arrArrPoints.value.length] = [{ x, y }]
    }
  }

  function onMove(e: MouseEvent | TouchEvent) {
    if (!isMoving.value) {
      return
    }

    const { x, y } = extractCoords(e)
    if (x && y) {
      arrArrPoints.value[arrArrPoints.value.length - 1].push({ x, y })
    }
  }

  function onEnd(e: MouseEvent | TouchEvent) {
    isMoving.value = false
    const { x, y } = extractCoords(e)
    if (x && y) {
      arrArrPoints.value[arrArrPoints.value.length - 1]?.push({ x, y })
    }
  }

  const smoothLines = computed(() => arrArrPoints.value.map((l) => smoothLine(l)).join(' '))

  const path = computed(() => {
    if (!isMoving.value) {
      return smoothLines.value
    }

    return arrArrPoints.value
      .map((l, i) => {
        if (i + 1 === arrArrPoints.value.length) {
          return polyline(l)
        }
        return smoothLine(l)
      })
      .join(' ')
  })

  return {
    onMove,
    onStart,
    onEnd,
    path,
    arrArrPoints,
  }
}

export { useDrawing, type Point }
