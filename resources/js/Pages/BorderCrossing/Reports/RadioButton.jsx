// RadioButton.jsx
import React from 'react';
import './radio-button.css'; // Импортируем CSS для стилизации

const RadioButton = ({ id, name, value, checked, onChange, label }) => {
    return (
        <label className="radio-button">
            <span className="radio-label">{label}</span>
            <input
                type="radio"
                id={id}
                name={name}
                value={value}
                checked={checked}
                onChange={onChange}
                className="radio-input"
            />
            <span className="radio-custom"></span>
        </label>
    );
};

export default RadioButton;
