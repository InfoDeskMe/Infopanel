import React, { useEffect, useState } from 'react'
import Layout from './components/layout/v1/Layout'
import LayoutV2 from './components/layout/v2/LayoutV2'
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
            this.getData(i)
            i++
          } else {
            i = 0
          }
        }, CONFIG.slideDelay)
      })
  }

  getData = (s) => {
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

    const renderTicker = (s) => {
      if (s == 1) {
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
    }

    const renderLayout = (m) => {
      if (m == 0) {
        return (
          <Layout
          header={headline}
          content={content}
          imageSrc={imageSrc}
          url={url}
          />
          )
      } else {
        return (
          <LayoutV2
          header={headline}
          content={content}
          imageSrc={imageSrc}
          url={url}
          />
          )
      }
    }

    const prepareRendering = () => {
      return (
        <div>
          {renderTicker(config.showTicker)},
          {renderLayout(config.layout_type)},
        </div>
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
      return (
        <div>
          {prepareRendering()}
        </div>
      )
    }
  }
}

export default App
