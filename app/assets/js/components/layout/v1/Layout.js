import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import QRCode from 'react-qr-code'


class Layout extends Component {
  render() {
    return (
      <div>
        <div className="container-fluid">
          <div className="row">
            <div className="col-10">
              <div className="col">
                <div className="fixedCol">
                <h1 className="headerText">{this.props.header}</h1>
                  <img src={this.props.imageSrc} alt="" className="mainImage"></img>
                </div>
              </div>
            </div>
            <div className="col-2">
              <div className="row qr_code">
                <span>Read more:</span>
                <QRCode value={this.props.url} className=""/>
              </div>
              <div className="row">
                <p className="frameText">{this.props.content}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default Layout
