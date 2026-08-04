import { render, screen } from '@testing-library/react'
import { describe, expect, it, vi } from 'vitest'
import App from './App.jsx'
import usePolledJson from './hooks/usePolledJson.js'

vi.mock('./hooks/usePolledJson.js', () => ({ default: vi.fn() }))

const SETTINGS = {
  generalReload: 60000,
  slideDelay: 8000,
  showTicker: 0,
  layout_type: 0,
}

function mockPolledData({ settings, newsData }) {
  usePolledJson.mockImplementation((url) => {
    if (url.includes('read_settings')) return settings
    if (url.includes('read_config')) return newsData
    return null
  })
}

describe('App', () => {
  it('shows a loading state until both settings and news data have arrived', () => {
    mockPolledData({ settings: null, newsData: null })

    const { container } = render(<App />)

    expect(container.querySelector('.lds-roller')).toBeInTheDocument()
  })

  it('shows the empty-state placeholder when there are no news entries', () => {
    mockPolledData({ settings: SETTINGS, newsData: { news: [] } })

    render(<App />)

    expect(screen.getByText('No content has been added yet.')).toBeInTheDocument()
  })

  it('renders a slide even when its fields are incomplete, instead of crashing', () => {
    mockPolledData({
      settings: SETTINGS,
      newsData: { news: [{ headline: 'Only a headline' }] },
    })

    render(<App />)

    expect(screen.getByText('Only a headline')).toBeInTheDocument()
    expect(screen.queryByText('No content has been added yet.')).not.toBeInTheDocument()
  })
})
