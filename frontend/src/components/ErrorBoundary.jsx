import React from 'react'

// Error boundaries only work as class components -- there's no hook
// equivalent. Catches render errors in the slide/ticker tree so an
// unattended display shows a neutral message instead of a blank crash.
class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props)
    this.state = { hasError: false }
  }

  static getDerivedStateFromError() {
    return { hasError: true }
  }

  componentDidCatch(error, info) {
    console.error('Panel render error:', error, info)
  }

  render() {
    if (this.state.hasError) {
      return <div className="panel-error">Something went wrong displaying this slide.</div>
    }
    return this.props.children
  }
}

export default ErrorBoundary
