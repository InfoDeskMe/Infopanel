function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]').content
}

// Thin fetch wrapper: attaches the CSRF header to every non-GET request,
// since every backoffice write endpoint requires it.
function apiFetch(url, options = {}) {
  const method = options.method || 'GET'
  const headers = new Headers(options.headers || {})
  if (method !== 'GET') {
    headers.set('X-CSRF-Token', getCsrfToken())
  }
  return fetch(url, { ...options, method, headers })
}

// Short random string used only for throwaway DOM element ids, never for
// anything sent to the server (server-generated ids come back from the API).
function makeId(length) {
  let result = ''
  const characters = '123456789'
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * characters.length))
  }
  return result
}
