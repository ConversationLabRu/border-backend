import './styles.css';
import './border-info-styles.css';
import { AppRoot, Avatar, AvatarStack, List, Text } from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {useLocation, useNavigate, useParams} from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import { ServerURL } from "@/API/ServerConst.js";
import ReportService from "@/API/ReportService.js";

export default function BorderCrossingInfo() {
    const location = useLocation();

    const directionCrossing = location.state?.directionCrossing;

    const [reports, setReports] = useState([])

    const { id } = useParams();

    const navigate = useNavigate();


    useEffect(() => {
        console.log(id);
        const directionIdNumber = Number(id);

        ReportService.getLast(directionIdNumber).then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setReports(r);
                console.log(r[0].checkpoint_entry)
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);

    return (
        <AppRoot>
            <div>
                {(directionCrossing === undefined) ? (
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
                                src={`${ServerURL.URL_STATIC}/${directionCrossing?.header_image}`}
                                alt={"header"}
                                className="header-image"
                            />
                            {/* Текст поверх изображения */}
                            <div className="overlay-text">
                                <Text weight="1" className="img-text">
                                    {directionCrossing?.from_city.name} - {directionCrossing?.to_city.name}
                                </Text>
                            </div>
                        </div>

                        <div className="container">
                            <div className="btn-container">
                                <div className="btn">
                                    <img
                                        src={`${ServerURL.URL_STATIC}/camera-logo.svg`}
                                        alt={"logo-btn"}
                                    />

                                    <Text weight="1">
                                        Камеры
                                    </Text>
                                </div>

                                <div className="btn">
                                    <img
                                        src={`${ServerURL.URL_STATIC}/info-logo.svg`}
                                        alt={"logo-btn"}
                                    />

                                    <Text weight="1">
                                        Информация
                                    </Text>
                                </div>
                            </div>


                            <div className="report-container-title">
                                <Text weight="1">
                                    Отчёты о прохождении
                                </Text>

                                <Text weight="1" className={"link-report"} onClick={() => {
                                    navigate(`/borderCrossing/${id}/reports`,
                                        {
                                            state: {
                                                directionCrossing: directionCrossing
                                            }
                                        }
                                    );
                                }}>
                                    Смотреть все
                                </Text>
                            </div>

                            <List>
                                {reports.map((report, index) => {

                                    // Предполагаем, что checkpoint_entry и checkpoint_exit - это объекты Date
                                    const entryTime = new Date(report.checkpoint_entry);
                                    const exitTime = new Date(report.checkpoint_exit);

                                    const options = {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                    };

                                    // Форматируем дату с учетом временной зоны пользователя
                                    const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                    const formattedDate = formatter.format(exitTime);

                                    // Вычисляем разницу в миллисекундах
                                    let differenceInMs = exitTime - entryTime;

                                    if (report.checkpoint_queue !== null) {
                                        const queueTime = new Date(report.checkpoint_queue);


                                        differenceInMs = exitTime - queueTime

                                    }

                                    // Преобразуем миллисекунды в часы и минуты
                                    const differenceInMinutes = Math.floor(differenceInMs / 60000);
                                    const hours = Math.floor(differenceInMinutes / 60);
                                    const minutes = differenceInMinutes % 60;

                                    // Форматируем разницу во времени
                                    const timeDifference = `${declensionHours(hours)} ${declensionMinutes(minutes)}`;

                                    console.log(52)
                                    console.log(report)

                                    return (
                                        <div className="border-crossing-container" key={index}>
                                            <div className={"border-info-container"}>
                                                <AvatarStack className={"avatar-stack-report"}>
                                                    <React.Fragment>
                                                        {report.is_flipped_direction ? (
                                                            <div className={"report-elem-logo-container"}>
                                                                <Avatar
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/${directionCrossing.from_city.country.logo}`}
                                                                />
                                                                <Avatar
                                                                    className={"to-logo-size"}
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/to_logo.svg`}
                                                                />
                                                                <Avatar
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/${directionCrossing.to_city.country.logo}`}
                                                                />
                                                            </div>
                                                        ) : (
                                                            <div className={"report-elem-logo-container"}>
                                                                <Avatar
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/${directionCrossing.to_city.country.logo}`}
                                                                />
                                                                <Avatar
                                                                    className={"to-logo-size"}
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/to_logo.svg`}
                                                                />
                                                                <Avatar
                                                                    size={20}
                                                                    src={`${ServerURL.URL_STATIC}/${directionCrossing.from_city.country.logo}`}
                                                                />
                                                            </div>
                                                        )}
                                                    </React.Fragment>
                                                </AvatarStack>

                                                <Text weight="3" className={"text-card"}>
                                                    {timeDifference}
                                                </Text>
                                            </div>

                                            <Text weight="3" className={"date-create-report-text"}>
                                                {`${formattedDate}`}
                                            </Text>
                                        </div>
                                    )
                                })}
                            </List>
                        </div>
                    </div>
                )}
            </div>
        </AppRoot>
    );
}

/**
 * Функция для склонения слова "час" в зависимости от числа.
 * @param {number} count - Число, для которого нужно склонить слово.
 * @returns {string} - Склоненное слово "час" в зависимости от числа.
 */
export function declensionHours(count) {
    const number = Math.abs(count) % 100; // Берем абсолютное значение и последние две цифры
    const lastDigit = number % 10; // Последняя цифра

    if (number > 10 && number < 20) {
        // Если число от 11 до 19 включительно
        return `${count} часов`;
    }

    switch (lastDigit) {
        case 1:
            return `${count} час`;
        case 2:
        case 3:
        case 4:
            return `${count} часа`;
        default:
            return `${count} часов`;
    }
}

/**
 * Функция для склонения слова "минута" в зависимости от числа.
 * @param {number} count - Число, для которого нужно склонить слово.
 * @returns {string} - Склоненное слово "минута" в зависимости от числа.
 */
export function declensionMinutes(count) {
    const number = Math.abs(count) % 100; // Берем абсолютное значение и последние две цифры
    const lastDigit = number % 10; // Последняя цифра

    if (number > 10 && number < 20) {
        // Если число от 11 до 19 включительно
        return `${count} минут`;
    }

    switch (lastDigit) {
        case 1:
            return `${count} минута`;
        case 2:
        case 3:
        case 4:
            return `${count} минуты`;
        default:
            return `${count} минут`;
    }
}
