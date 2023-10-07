import React, {useEffect, useRef} from 'react'
import QRCode from 'react-qr-code'

function Layout(props) {
  const videoRef = useRef();

  const sourceEndingHelper = () => {
    let ending = props.imageSrc.slice(-3);
    if (ending === 'mp4') {
      return true;
    } else return false; 
  };

  useEffect(() => {    
    videoRef.current?.load();
  }, [props.imageSrc]);

    return (
      <div>
        <div className="container-fluid">
          <div className="row">
            <div className="col-10">
              <div className="col">
                <div className="fixedCol">
                <h1 className="headerText">{props.header}</h1>
                  {sourceEndingHelper() ? <video loop autoPlay muted className="mainImage" ref={videoRef}><source src={props.imageSrc} type="video/mp4" /></video> : <img src={props.imageSrc} alt="" className="mainImage"></img>}  
                  {/* <img src={props.imageSrc} alt="" className="mainImage"></img> */}
                </div>
              </div>
            </div>
            <div className="col-2">
              <div className="row qr_code">
                <span>Read more:</span>
                <QRCode value={props.url} className=""/>
              </div>
              <div className="row">
                <p className="frameText">{props.content}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

export default Layout
