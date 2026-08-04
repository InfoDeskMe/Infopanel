import Ticker from './Tickerhelper'
import usePolledJson from '../../hooks/usePolledJson.js'

const API_BASE = import.meta.env.VITE_API_BASE ?? '/api'
const DEFAULT_TICKER_RELOAD_MS = 5000

// height/speed/direction come from App.jsx's own settings poll -- no need
// for this component to poll settings a second time on its own schedule.
const MoveTicker = ({ height, speed, direction }) => {
  const tickerData = usePolledJson(`${API_BASE}/read_ticker`, DEFAULT_TICKER_RELOAD_MS)
  const items = (tickerData?.news ?? []).map((entry) => entry.content).filter(Boolean)

  return (
    <div className="backgroundTicker">
      <Ticker items={items} direction={direction} height={height} speed={speed}></Ticker>
    </div>
  )
}

export default MoveTicker
