import '../styles.css';
import {AppRoot, Avatar, AvatarStack, Cell, List, Section, Text} from "@telegram-apps/telegram-ui";
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
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppRoot>
            <div className="container">
                <Section header={"Информация"}>
                    {direction.info !== undefined && (
                        <List>
                            {direction.info.map((doc, index) => {
                                if (doc.type === "document") {
                                    return (
                                        <Cell

                                            after={
                                                <img
                                                    src={`/continue.svg`}
                                                    alt={'dada'}
                                                />
                                            }
                                            before={
                                                <Avatar
                                                    size={40}
                                                    src={`/passport.png`}
                                                />
                                            }
                                            onClick={() => openUrlInNewTab(doc.url)}
                                            subtitle="Документы, необходимые для пересечаения границы"

                                        >
                                            {`Документы`}
                                        </Cell>
                                    );
                                } else if (doc.type === "import-export-standart") {
                                    return (

                                        <Cell

                                            after={
                                                <img
                                                    src={`/continue.svg`}
                                                    alt={'dada'}
                                                />
                                            }
                                            before={
                                                <Avatar
                                                    size={40}
                                                    src={`/wine-bottle.png`}
                                                />
                                            }
                                            onClick={() => openUrlInNewTab(doc.url)}
                                            subtitle="Информация о разрешениях и нормах ввоза/вывоза через границу"
                                        >
                                            {`Нормы ввоза/вывоза`}
                                        </Cell>
                                    );
                                } else {
                                    return (
                                        <Cell

                                            after={
                                                <img
                                                    src={`/continue.svg`}
                                                    alt={'dada'}
                                                />
                                            }
                                            before={
                                                <Avatar
                                                    size={40}
                                                    src={`/bindle.png`}
                                                />
                                            }
                                            onClick={() => openUrlInNewTab(doc.url)}
                                            subtitle="Пересечение границы по Упрощенному Транзитному Документу"

                                        >
                                            {`УТД`}
                                        </Cell>
                                    );
                                }
                            })}
                        </List>
                    )}
                </Section>
            </div>
        </AppRoot>
    );
}
