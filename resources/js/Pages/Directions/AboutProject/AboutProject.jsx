import '../styles.css';
import {AppRoot, Avatar, Button, Cell, Image, List, Section, Text} from "@telegram-apps/telegram-ui";
// import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import {Link, useNavigate} from "react-router-dom";
import {DirectionCard} from "@/Pages/Directions/Components/card.jsx";
import {ServerURL} from "@/API/ServerConst.js";
import {Icon20Copy} from "@telegram-apps/telegram-ui/dist/icons/20/copy.js";
import {ThreeDots} from "react-loader-spinner";
import {useTWAEvent} from "@tonsolutions/telemetree-react";
import {useBackButton} from "@tma.js/sdk-react";

export default function AboutProject() {
    const eventBuilder = useTWAEvent();

    const navigate = useNavigate();

    const backButton = useBackButton();

    useEffect(() => {
        backButton.show();
        return () => backButton.hide();
    }, [backButton]);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/`);
            backButton.hide();
        };
        backButton.on("click", handleBackButtonClick);
        return () => backButton.off("click", handleBackButtonClick);
    }, [backButton, navigate]);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppRoot>
            <div>

                <img
                    src="/bordersmainnew2.png"
                    alt={"das"}
                    className="header-image-direction"
                />
                <List>

                    <div className={"text-info"} style={{margin: "5%"}}>
                        <Text>
                            <strong>«Очереди на границе»</strong> — совместный проект портала <a style={{color: "cornflowerblue"}} href={"https://kgd.ru/"} target={"_blank"}>Калининград.Ru</a> и компании <a style={{color: "cornflowerblue"}} href={"https://conversationlab.ru/"} target={"_blank"}>Conversation Lab_</a>.<br/>
                            Здесь вы найдёте актуальную информацию о пересечении границы между Россией, Беларусью, Литвой и Польшей. <br/> <br/>
                            Прогнозы по времени прохождения контроля, оформление документов, правила и нормы провоза товаров, полезные советы по навигации и трансляция с камер в пунктах пропуска — здесь есть всё, что нужно для пересечения пункта пропуска на автомобиле и автобусе.
                        </Text>
                    </div>

                    <div className="bottom-button-container-about-project" style={{bottom: "60px"}}
                         onClick={() => openUrlInNewTab("https://t.me/conversationlab")}>
                        <Button
                            before={
                                <Avatar size={24} src={"/images/logo-company.jpg"}/>
                            }
                            mode="filled"
                            size="s"
                        >
                            Связаться с Conversation Lab_
                        </Button>
                    </div>

                    <div className="bottom-button-container-about-project"
                         onClick={() => openUrlInNewTab("https://t.me/kgdrubot")}>
                        <Button
                            before={
                                <Avatar size={24} src={"/images/logoK.jpg"}/>
                            }
                            mode="filled"
                            size="s"
                        >
                            Связаться с Калининград.Ru
                        </Button>
                    </div>
                </List>

            </div>
        </AppRoot>
    );
}
