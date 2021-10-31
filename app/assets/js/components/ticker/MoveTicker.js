import React, { useEffect, useState } from 'react'
import Ticker from 'react-ticker'

let newsData = []
let CONFIG = {}
let n = -1

setInterval(() => {
  ;(async () => {
    const url = 'data_ticker.json'
    try {
      const response = await fetch(url, {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Cache-Control': 'no-cache',
        cache: 'reload',
        Pragma: 'no-cache',
        Expires: '0',
      })
      const data = await response.json()
      let dataTicker = Object.keys(data)
      dataTicker.forEach((key, index) => {
        newsData.push(`${data[key].content}`)
    });
    } catch (error) {
      console.log('Error:', error)
    }
  })()
}, 500)

setInterval(() => {
  ;(async () => {
    const url = 'config.json'
    try {
      const response = await fetch(url, {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Cache-Control': 'no-cache',
        cache: 'reload',
        Pragma: 'no-cache',
        Expires: '0',
      })
      const data = await response.json()
      CONFIG = data
    } catch (error) {
      console.log('Error:', error)
    }
  })()
}, 2000)

function MakeAPICall() {
  return new Promise((resolve) => {
    resolve(newsData[n])
    n = n + 1
    if (n > Object.keys(newsData).length) {
      n = 0
    } 
  }, 500)
}

const GetNewsFromAPI = () => {
  const [news, setNews] = useState('')

  useEffect(() => {
    async function fetchData() {
      const newsFromAPI = await MakeAPICall()
      setNews(newsFromAPI)
    }
    fetchData()
  }, [])
  return news ? (
    <span className="">*** {news} ***</span>
  ) : (
    <span className="">***</span>
  )
}

const MoveTicker = () => (
  <>
    <div className="backgroundTicker">
      <Ticker
        height={CONFIG.tickerHeight}
        speed={CONFIG.tickerSpeed}
        direction={CONFIG.tickerOrientation}
        mode={CONFIG.tickerMode}
        offset={CONFIG.tickerOffset}
      >
        {() => <GetNewsFromAPI />}
      </Ticker>
    </div>
  </>
)

export default MoveTicker
