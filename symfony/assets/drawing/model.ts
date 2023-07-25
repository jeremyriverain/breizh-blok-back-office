import { Point } from './useDrawing'

export interface LineBoulderInterface {
  '@id': string
  arrArrPoints: Point[][]
}

export interface SavePayloadInterface {
  arrArrPoints: Point[][]
  path: string
}

export type Nullable<T> = {
  [P in keyof T]: T[P] | null
}
