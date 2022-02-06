import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import QRCode from 'react-qr-code'


class LayoutV2 extends Component {
  render() {
    return (
      <div>
        <div className="container-fluid">
          <div className="">
            <div className="">
              <div className="">
                <div className="fixedCol">
                <h1 className="headerTextV2">{this.props.header}</h1>
                  <img src={this.props.imageSrc} alt="" class="mainImageV2"></img>
                </div>
              </div>
            </div>
            <div className="row bottomBlock">
              <div className='col-4'>
              <div className="qr_codeV2">
                <span>Read more:</span>
                <div className="svgFrame">
                  <QRCode value={this.props.url} className=""/>
                </div>
              </div>
              </div>
              <div className="col-8">
                <p className="frameTextV2">{this.props.content}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default LayoutV2
