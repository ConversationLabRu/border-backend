import './styles.css';
import { AppRoot, Avatar, AvatarStack, List, Text } from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {useNavigate, useParams} from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import { ServerURL } from "@/API/ServerConst.js";

export default function BorderCrossing() {
    const [directionCrossings, setDirectionCrossings] = useState([]);
    const { id } = useParams();
    const navigate = useNavigate();


    useEffect(() => {
        console.log(id);
        const directionIdNumber = Number(id);

        BorderCrossingService.getAllById(directionIdNumber).then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setDirectionCrossings(r);
                console.log(r); // Используйте console.log для отладки
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);

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
                                src={`${ServerURL.URL_STATIC}/${directionCrossings[0]?.direction?.image}`}
                                alt={"header"}
                                className="header-image"
                            />
                            {/* Текст поверх изображения */}
                            <div className="overlay-text">
                                <Text weight="1" className="img-text">
                                    {directionCrossings[0]?.direction?.name}
                                </Text>
                            </div>
                        </div>

                        <div className="container">
                            <Text weight="3" className={"header-text"}>
                                Пограничные переходы
                            </Text>

                            <List>
                                {directionCrossings.map((direction, index) => (
                                    <div className="border-crossing-container"
                                         key={index}
                                         onClick={() => {
                                             navigate(`/borderCrossing/info/${direction.id}`,
                                                 {
                                                     state: {
                                                         directionCrossing: directionCrossings[index]
                                                     }
                                                 }
                                             );
                                         }
                                    }>
                                        <div className={"border-info-container"}>
                                            <AvatarStack>
                                                <React.Fragment>
                                                    <Avatar
                                                        size={40}
                                                        src={`${ServerURL.URL_STATIC}/${direction.from_city.country.logo}`}
                                                    />
                                                    <Avatar
                                                        className={"avatar-country"}
                                                        size={40}
                                                        src={`${ServerURL.URL_STATIC}/${direction.to_city.country.logo}`}
                                                    />
                                                </React.Fragment>
                                            </AvatarStack>

                                            <Text weight="3" className={"text-card"}>
                                                {`${direction.from_city.name} - ${direction.to_city.name}`}
                                            </Text>
                                        </div>

                                        <img
                                            src={`${ServerURL.URL_STATIC}/continue.svg`}
                                            alt={'dada'}
                                        />
                                    </div>
                                ))}
                            </List>

                            <Text weight="3" className={"header-text"}>
                                Информация
                            </Text>

                            {directionCrossings[0]?.direction?.info !== undefined && (
                                <List>
                                    {directionCrossings[0]?.direction?.info.map((doc, index) => {
                                        if (doc.type === "document") {
                                            return (
                                                <div className={"border-crossing-info"}>

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
                                                <div className={"border-crossing-info"}>

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
                                                <div className={"border-crossing-info non-margin"}>

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
                    </div>
                )}
            </div>
        </AppRoot>
    );
}
