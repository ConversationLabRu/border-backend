import './styles.css';
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
import {BsQuestionCircleFill} from "react-icons/bs";

export default function BorderCrossing() {
    const [directionCrossings, setDirectionCrossings] = useState([]);
    const { id } = useParams();
    const navigate = useNavigate();

    const location = useLocation();

    const direction = location.state?.direction;

    const backButton = useBackButton();

    useEffect(() => {
        backButton.show()
    }, []);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/`);
            backButton.hide();
        };

        backButton.on("click", handleBackButtonClick);

        return () => {
            backButton.off("click", handleBackButtonClick);
        }
    }, [backButton]);

    useEffect(() => {
        const directionIdNumber = Number(id);

        BorderCrossingService.getAllById(directionIdNumber).then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setDirectionCrossings(r);
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppRoot>
            <div>
                {(directionCrossings === undefined || directionCrossings.length === 0) ? (
                    <div className="loader-overlay">
                        <ThreeDots
                            height="80"
                            width="80"
                            color="#007aff"
                            ariaLabel="loading"
                        />
                    </div>
                ) : (
                    <div>
                        <div className="image-container">
                            {/* Основное изображение */}
                            <img
                                src={`/${direction.image}`}
                                alt={"header"}
                                className="header-image"
                            />
                            {/* Текст поверх изображения */}
                            <div className="overlay-text">
                                <Text weight="1" className="img-text">
                                    {direction.name}
                                </Text>
                            </div>
                        </div>

                        <div className="container">
                            <Section header={"Пограничные переходы"}>
                                <List>
                                    {directionCrossings.map((directionCros, index) => (

                                        <Cell
                                            after={
                                                <img
                                                    src={`/continue.svg`}
                                                    alt={'dada'}
                                                />
                                            }
                                            before={
                                                <AvatarStack>
                                                    <React.Fragment>
                                                        <Avatar
                                                            size={40}
                                                            src={`/${directionCros.from_city.country.logo}`}
                                                        />
                                                        <Avatar
                                                            size={40}
                                                            src={`/${directionCros.to_city.country.logo}`}
                                                        />
                                                    </React.Fragment>
                                                </AvatarStack>
                                            }
                                            onClick={() => {
                                                         navigate(`/borderCrossing/info/${directionCros.id}`,
                                                             {
                                                                 state: {
                                                                     directionCrossing: directionCrossings[index],
                                                                     direction: direction
                                                                 }
                                                             }
                                                         );
                                                     }
                                                }
                                        >
                                            {`${directionCros.from_city.name} - ${directionCros.to_city.name}`}
                                        </Cell>
                                    ))}
                                </List>
                            </Section>

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
                                            } else if (doc.type === "frequent-questions") {
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
                                                                src={`/question-mark.png`}
                                                            />
                                                        }
                                                        onClick={() => openUrlInNewTab(doc.url)}

                                                    >
                                                        {`Частые вопросы`}
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
                    </div>
                )}
            </div>
        </AppRoot>
    );
}
