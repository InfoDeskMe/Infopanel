import React, { useEffect, useState } from 'react'
import Layout from './components/layout/Layout'
import '../css/Bootstrap.css'
import '../css/app.css'
import MoveTicker from './components/ticker/MoveTicker'

class App extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      error: null,
      isLoaded: false,
      items: [],
      data: [],
      CONFIG: {},
    }
    this.props = {}
  }

  getConfig = () => {
    const result = fetch('/config.json', {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache',
      cache: 'reload',
      Pragma: 'no-cache',
      Expires: '0',
    })
      .then((resConfig) => resConfig.json())
      .then((resConfig) => {
        this.setState({
          isLoaded: false,
          config: resConfig,
        })
      })
  }

  componentDidMount() {
    var i = 0
    fetch('/config.json', {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache',
      cache: 'reload',
      Pragma: 'no-cache',
      Expires: '0',
    })
      .then((res) => res.json())
      .then((res) => {
        const CONFIG = res
        this.getConfig()
        setInterval(() => {
          window.location.reload()
        }, CONFIG.generalReload)
        setInterval(() => {
          if (i < CONFIG.newsNumber) {
            this.getData(i) // runs every 5 seconds.
            i++
          } else {
            i = 0
          }
        }, CONFIG.slideDelay)
      })
  }

  getData = (s) => {
    // do something to fetch data from a remote API.
    fetch('/data.json', {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache',
      cache: 'reload',
      Pragma: 'no-cache',
      Expires: '0',
    })
      .then((res) => res.json())
      .then(
        (result) => {
          function getHeadline() {
            return eval('result.news' + s + '.headline')
          }
          function getContent() {
            return eval('result.news' + s + '.content')
          }

          function getImageSrc() {
            return eval('result.news' + s + '.image')
          }

          function getUrl() {
            return eval('result.news' + s + '.url')
          }

          this.setState({
            isLoaded: true,
            items: result,
            headline: getHeadline(),
            content: getContent(),
            imageSrc: getImageSrc(),
            url: getUrl(),
          })
        },
        (error) => {
          this.setState({
            isLoaded: true,
            error,
          })
        },
      )
  }

  render() {
    var {
      error,
      isLoaded,
      items,
      headline,
      content,
      imageSrc,
      url,
      tickerDataArray,
      config,
    } = this.state

    const renderTicker = () => {
      return (
        <MoveTicker
          tickerDataArray={tickerDataArray}
          height={config.tickerHeight}
          speed={config.tickerSpeed}
          direction={config.tickerOrientation}
          mode={config.tickerMode}
          offset={config.tickerOffset}
        />
      )
    }

    if (error) {
      setTimeout(() => window.location.reload(), 5000)
      return <div>Error because {error.message}</div>
    } else if (!isLoaded) {
      return (
        <div class="lds-roller">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      )
    } else {
      if (config.showTicker == 1) {
        return (
          <div>
            {renderTicker()},
            <Layout
              header={headline}
              content={content}
              imageSrc={imageSrc}
              url={url}
            />
          </div>
        )
      } else {
        return (
          <div>
            <Layout
              header={headline}
              content={content}
              imageSrc={imageSrc}
              url={url}
            />
          </div>
        )
      }
    }
  }
}

export default App
