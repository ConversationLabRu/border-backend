import '../styles.css';
import { AppRoot, Avatar, AvatarStack, List, Text } from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {useLocation, useNavigate, useParams} from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import { ServerURL } from "@/API/ServerConst.js";
import {useBackButton} from "@tma.js/sdk-react";

export default function InformationPage() {
    const navigate = useNavigate();


    const location = useLocation();


    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;

    const backButton = useBackButton();

    useEffect(() => {
        backButton.show()
    }, []);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/borderCrossing/info/${directionCrossing.id}`,
                {
                    state: {
                        direction: direction,
                        directionCrossing: directionCrossing
                    }
                });
            backButton.hide();
        };

        backButton.on("click", handleBackButtonClick);

        return () => {
            backButton.off("click", handleBackButtonClick);
        }
    }, [backButton]);

    const openUrlInNewTab = (url) => {
        if (url) {
            console.log(url)
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppRoot>
            <div className="container">

                <Text weight="3" className={"header-text"}>
                    Информация
                </Text>

                {direction.info !== undefined && (
                    <List>
                        {direction.info.map((doc, index) => {
                            if (doc.type === "document") {
                                return (
                                    <div className={"border-crossing-info"}
                                         onClick={() => openUrlInNewTab(doc.url)}>

                                        <div className={"border-info-container"}>
                                            <img
                                                src={`${ServerURL.URL_STATIC}/passport.png`}
                                                alt={"passport"}
                                            />

                                            <div className="text-container">
                                                <Text key={index} weight="3" className={"title-info"}>
                                                    Документы
                                                </Text>

                                                <Text key={index} weight="3" className={"desc-info"}>
                                                    Документы, необходимые для пересечения границы
                                                </Text>
                                            </div>
                                        </div>

                                        <img
                                            src={`${ServerURL.URL_STATIC}/continue.svg`}
                                            alt={'dada'}
                                        />
                                    </div>
                                );
                            } else if (doc.type === "import-export-standart") {
                                return (
                                    <div className={"border-crossing-info"}
                                         onClick={() => openUrlInNewTab(doc.url)}>

                                        <div className={"border-info-container"}>
                                            <img
                                                src={`${ServerURL.URL_STATIC}/wine-bottle.png`}
                                                alt={"wine-bottle"}
                                            />

                                            <div className="text-container">
                                                <Text key={index} weight="3" className={"title-info"}>
                                                    Нормы ввоза/вывоза
                                                </Text>

                                                <Text key={index} weight="3" className={"desc-info"}>
                                                    Информация о разрешениях и нормах ввоза/вывоза через границу
                                                </Text>
                                            </div>
                                        </div>

                                        <img
                                            src={`${ServerURL.URL_STATIC}/continue.svg`}
                                            alt={'dada'}
                                        />
                                    </div>
                                );
                            } else {
                                return (
                                    <div className={"border-crossing-info non-margin"}
                                         onClick={() => openUrlInNewTab(doc.url)}>

                                        <div className={"border-info-container"}>
                                            <img
                                                src={`${ServerURL.URL_STATIC}/bindle.png`}
                                                alt={"bindle"}
                                            />

                                            <div className="text-container">
                                                <Text key={index} weight="3" className={"title-info"}>
                                                    УТД
                                                </Text>

                                                <Text key={index} weight="3" className={"desc-info"}>
                                                    Пересечение границы по Упрощенному Транзитному Документу
                                                </Text>
                                            </div>
                                        </div>

                                        <img
                                            src={`${ServerURL.URL_STATIC}/continue.svg`}
                                            alt={'dada'}
                                        />
                                    </div>
                                );
                            }
                        })}
                    </List>
                )}
            </div>
        </AppRoot>
    );
}
