import React, { useEffect, useState, useRef, useCallback } from 'react'
import Ticker from './Tickerhelper'

let newsData = []
let CONFIG = {}

setInterval(() => {
  (async () => {
    const url = 'http://localhost:8080/data_ticker.json';
    try {
      const response = await fetch(url, {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Cache-Control': 'no-cache',
        cache: 'reload',
        Pragma: 'no-cache',
        Expires: '0',
      });
      const data = await response.json();

      for (const key in data) {
        if (data.hasOwnProperty(key)) {
          const content = data[key]?.content;
          if (content) {
            newsData.push(content);
          } else {
            console.log(`No data for ${key}`);
          }
        }
      }
    } catch (error) {
      console.error('Error on getting data', error);
    }
  })();
}, 500);

setInterval(() => {
  ; (async () => {
    const url = 'http://localhost:8080/config.json'
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
      console.error('Error:', error)
    }
  })()
}, 2000)

const MoveTicker = () => (
  <>
    <div className="backgroundTicker">
      <Ticker 
        items={newsData}
        direction={CONFIG.tickerOrientation}
        height={CONFIG.tickerHeight}
        speed={CONFIG.tickerSpeed}
      >
      </Ticker>
    </div>
  </>
)

export default MoveTicker
