import {useEffect, useRef} from 'react'
import QRCode from 'react-qr-code'

function BottomLayout(props) {
  const videoRef = useRef();

  const sourceEndingHelper = () => {
    return (props.imageSrc ?? '').slice(-3) === 'mp4';
  };

  useEffect(() => {
    videoRef.current?.load();
  }, [props.imageSrc]);

    return (
      <div>
        <div className="container-fluid">
          <div className="">
            <div className="">
              <div className="">
                <div className="fixedCol">
                <h1 className="headerTextV2">{props.header}</h1>
                  {sourceEndingHelper() ? <video loop autoPlay muted className="mainImage" ref={videoRef}><source src={props.imageSrc} type="video/mp4" /></video> : <img src={props.imageSrc} alt="" className="mainImage"></img>}
                </div>
              </div>
            </div>
            <div className="row bottomBlock">
              <div className='col-4'>
              <div className="qr_codeV2">
                <span>Read more:</span>
                <div className="svgFrame">
                  <QRCode value={props.url ?? ''} className=""/>
                </div>
              </div>
              </div>
              <div className="col-8">
                <p className="frameTextV2">{props.content}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }

export default BottomLayout
