import { useEffect, useState } from 'react';
import './assets/css/bootstrap.css';
import './assets/css/app.css';
import MoveTicker from './components/ticker/MoveTicker.jsx';
import ErrorBoundary from './components/ErrorBoundary.jsx';
import usePolledJson from './hooks/usePolledJson.js';
import { getLayoutComponent } from './layouts/index.js';

const API_BASE = import.meta.env.VITE_API_BASE ?? '/api';
const DEFAULT_RELOAD_MS = 60000;
const DEFAULT_SLIDE_DELAY_MS = 8000;

const App = () => {
  const settings = usePolledJson(`${API_BASE}/read_settings`, DEFAULT_RELOAD_MS);
  const newsData = usePolledJson(`${API_BASE}/read_config`, settings?.generalReload ?? DEFAULT_RELOAD_MS);
  const [slideIndex, setSlideIndex] = useState(0);

  const newsList = newsData?.news ?? [];
  const slideCount = newsList.length;

  // Data can shrink (an item got deleted) between refreshes; keep the index
  // in range without resetting it back to 0 on every unrelated refresh.
  useEffect(() => {
    if (slideCount === 0) {
      return;
    }
    setSlideIndex((i) => i % slideCount);
  }, [slideCount]);

  useEffect(() => {
    if (slideCount === 0) {
      return;
    }
    const id = setInterval(() => {
      setSlideIndex((i) => (i + 1) % slideCount);
    }, settings?.slideDelay ?? DEFAULT_SLIDE_DELAY_MS);
    return () => clearInterval(id);
  }, [slideCount, settings?.slideDelay]);

  const currentItem = newsList[slideIndex] ?? {};

  const renderTicker = () => {
    if (settings?.showTicker == 1) {
      return (
        <MoveTicker
          height={settings.tickerHeight}
          speed={settings.tickerSpeed}
          direction={settings.tickerOrientation}
        />
      );
    }
    return null;
  };

  const renderLayout = () => {
    const layoutProps = {
      header: currentItem.headline,
      content: currentItem.content,
      imageSrc: currentItem.image,
      url: currentItem.url,
    };

    const LayoutComponent = getLayoutComponent(settings?.layout_type);
    return <LayoutComponent {...layoutProps} />;
  };

  if (!settings || !newsData) {
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
  }

  if (slideCount === 0) {
    return (
      <div>
        {renderTicker()}
        <div className="panel-empty">No content has been added yet.</div>
      </div>
    );
  }

  return (
    <div>
      {renderTicker()}
      {/* key remounts the boundary per slide, so one bad slide can't
          permanently blank the display -- the next slide gets a clean try. */}
      <ErrorBoundary key={slideIndex}>{renderLayout()}</ErrorBoundary>
    </div>
  );
};

export default App;
