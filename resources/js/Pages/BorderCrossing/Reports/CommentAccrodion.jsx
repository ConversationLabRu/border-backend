import React, { useState } from 'react';
import './comment-accrodion-styles.css';
import {Text} from "@telegram-apps/telegram-ui";
import {ServerURL} from "@/API/ServerConst.js"; // Импортируйте CSS файл для стилей аккордеона

const CommentAccordion = ({ title, children }) => {
    const [isOpen, setIsOpen] = useState(false);

    const toggleAccordion = () => {
        setIsOpen(!isOpen);
    };

    return (
        <div className="custom-accordion">
            <button className="accordion-header" onClick={toggleAccordion}>
                <div className={"title-accordion"}>
                    {title}
                </div>
                <img
                    src={`${ServerURL.URL_STATIC}/continue.svg`}
                    alt={"continue"}
                    className={`accordion-icon ${isOpen ? 'open' : ''}`}
                />
            </button>
            {isOpen && <div className="accordion-content">{children}</div>}
        </div>
    );
};

export default CommentAccordion;
