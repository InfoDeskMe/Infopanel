import React, { useEffect, useRef, useState } from "react";

const Ticker = ({ items, speed, height = "50px", direction = "left" }) => {
    const tickerRef = useRef(null);
    const [offset, setOffset] = useState(0);

    useEffect(() => {
        const tickerWidth = tickerRef.current.scrollWidth;
        const containerWidth = tickerRef.current.offsetWidth;

        const scrollTicker = () => {
            setOffset((prevOffset) => {
                if (direction === "left") {
                    return prevOffset < -tickerWidth ? containerWidth : prevOffset - 1;
                } else if (direction === "right") {
                    return prevOffset > containerWidth ? -tickerWidth : prevOffset + 1;
                }
                return prevOffset;
            });
        };

        const interval = setInterval(scrollTicker, speed);

        return () => clearInterval(interval);
    }, [speed, direction, height]);

    return (
        <div
            style={{
                overflow: "hidden",
                whiteSpace: "nowrap",
                width: "100%", 
                height, 
                position: "relative"
            }}
        >
            <div
                ref={tickerRef}
                style={{
                    transform: `translateX(${offset}px)`,
                }}
            >
                {items.map((item, index) => (
                    <span key={index} style={{ marginRight: "50px", color: "white", marginBottom: height / 1.5, fontSize: height / 1.5 }}>
                       *** {item} ***
                    </span>
                ))}
            </div>
        </div>
    );
};

export default Ticker