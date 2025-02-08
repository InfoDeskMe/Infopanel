import React, { useEffect, useState } from 'react';
import Layout from './components/layout/v1/Layout';
import LayoutV2 from './components/layout/v2/LayoutV2';
import '../css/Bootstrap.css';
import '../css/app.css';
import MoveTicker from './components/ticker/MoveTicker';

const App = () => {
  const [error, setError] = useState(null);
  const [isLoaded, setIsLoaded] = useState(false);
  const [items, setItems] = useState([]);
  const [headline, setHeadline] = useState('');
  const [content, setContent] = useState('');
  const [imageSrc, setImageSrc] = useState('');
  const [url, setUrl] = useState('');
  const [tickerDataArray, setTickerDataArray] = useState([]);
  const [config, setConfig] = useState({});

  const getConfig = () => {
    fetch('/config.json', {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache',
      cache: 'reload',
      Pragma: 'no-cache',
      Expires: '0',
    })
      .then((resConfig) => resConfig.json())
      .then((resConfig) => {
        setIsLoaded(false);
        setConfig(resConfig);
      });
  };

  const getData = (s) => {
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
            return eval('result.news' + s + '.headline');
          }
          function getContent() {
            return eval('result.news' + s + '.content');
          }

          function getImageSrc() {
            return eval('result.news' + s + '.image');
          }

          function getUrl() {
            return eval('result.news' + s + '.url');
          }

          setIsLoaded(true);
          setItems(result);
          setHeadline(getHeadline());
          setContent(getContent());
          setImageSrc(getImageSrc());
          setUrl(getUrl());
        },
        (error) => {
          setIsLoaded(true);
          setError(error);
        }
      );
  };

  useEffect(() => {
    var i = 0;
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
        const CONFIG = res;
        getConfig();
        setInterval(() => {
          window.location.reload();
        }, CONFIG.generalReload);
        setInterval(() => {
          if (i < CONFIG.newsNumber) {
            getData(i);
            i++;
          } else {
            i = 0;
          }
        }, CONFIG.slideDelay);
      });
  }, []);

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
      );
    }
  };

  const renderLayout = (m) => {
    if (m == 0) {
      return (
        <Layout header={headline} content={content} imageSrc={imageSrc} url={url} />
      );
    } else {
      return (
        <LayoutV2 header={headline} content={content} imageSrc={imageSrc} url={url} />
      );
    }
  };

  const prepareRendering = () => {
    return (
      <div>
        {renderTicker(config.showTicker)}
        {renderLayout(config.layout_type)}
      </div>
    );
  };

  if (error) {
    setTimeout(() => window.location.reload(), 5000);
    return <div>Error because {error.message}</div>;
  } else if (!isLoaded) {
    return (
      <div className="lds-roller">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    );
  } else {
    return <div>{prepareRendering()}</div>;
  }
};

export default App;