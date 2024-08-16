import React from 'react';
import './Icon.css';

const Icon = ({ IconImage, iconBackground, iconColor, iconSize }) => {
    return (
        <div
            className="icon__background"
            style={{
                width: iconSize,
                height: iconSize,
                backgroundColor: iconBackground,
            }}
        >
            <IconImage size={iconSize * 0.75} style={{ color: iconColor }} />
        </div>
    );
};

export default Icon;
