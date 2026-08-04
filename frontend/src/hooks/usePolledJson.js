import { useEffect, useRef, useState } from 'react';

const MAX_BACKOFF_MS = 5 * 60 * 1000;
const INITIAL_BACKOFF_MS = 5000;

/**
 * Polls a JSON endpoint on an interval, using ETag/If-None-Match so an
 * unchanged response costs a 304 instead of a full payload + re-render.
 * On failure, keeps returning the last known data (never null again) and
 * backs off exponentially instead of hammering an unreachable backend.
 */
export default function usePolledJson(url, intervalMs) {
  const [data, setData] = useState(null);
  const etagRef = useRef(null);

  useEffect(() => {
    let cancelled = false;
    let timeoutId;
    let backoff = INITIAL_BACKOFF_MS;

    const tick = async () => {
      let hadError = false;
      try {
        const headers = {};
        if (etagRef.current) {
          headers['If-None-Match'] = etagRef.current;
        }
        const response = await fetch(url, { headers, cache: 'no-store' });

        if (response.status === 304) {
          // Unchanged -- keep showing what we already have.
        } else if (response.ok) {
          const etag = response.headers.get('ETag');
          if (etag) {
            etagRef.current = etag;
          }
          const json = await response.json();
          if (!cancelled) {
            setData(json);
          }
        } else {
          hadError = true;
        }
      } catch (e) {
        hadError = true;
      }

      backoff = hadError ? Math.min(backoff * 2, MAX_BACKOFF_MS) : INITIAL_BACKOFF_MS;

      if (!cancelled) {
        const delay = hadError ? backoff : intervalMs ?? INITIAL_BACKOFF_MS;
        timeoutId = setTimeout(tick, delay);
      }
    };

    tick();

    return () => {
      cancelled = true;
      clearTimeout(timeoutId);
    };
  }, [url, intervalMs]);

  return data;
}
