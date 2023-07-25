import './styles/app.css'
import 'leaflet/dist/leaflet.css'

import leaflet from 'leaflet'

import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png'
import iconUrl from 'leaflet/dist/images/marker-icon.png'
import shadowUrl from 'leaflet/dist/images/marker-shadow.png'

leaflet.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl })
