import './styles.css';
import './border-info-styles.css';
import { AppRoot, Avatar, AvatarStack, List, Text } from "@telegram-apps/telegram-ui";
import { InlineButtonsItem } from "@telegram-apps/telegram-ui/dist/components/Blocks/InlineButtons/components/InlineButtonsItem/InlineButtonsItem.js";
import { useState, useEffect } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import ReportService from "@/API/ReportService.js";
import { useBackButton } from "@tma.js/sdk-react";
import { IoInformation } from "react-icons/io5";
import { VscDeviceCamera } from "react-icons/vsc";
import React from 'react';


export default function BorderCrossingInfo() {
    const location = useLocation();
    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;
    const [reports, setReports] = useState([]);
    const { id } = useParams();
    const navigate = useNavigate();
    const backButton = useBackButton();
    const [waitingArea, setWaitingArea] = useState(null);

    useEffect(() => {
        backButton.show();
        return () => backButton.hide();
    }, [backButton]);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/borderCrossing/${direction.id}`, {
                state: { direction }
            });
            backButton.hide();
        };
        backButton.on("click", handleBackButtonClick);
        return () => backButton.off("click", handleBackButtonClick);
    }, [backButton, navigate, direction, direction.id]);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    useEffect(() => {
        const directionIdNumber = Number(id);
        ReportService.getLast(directionIdNumber).then((r) => {
            if (!(r instanceof Error)) {
                setReports(r);
            }
        }).catch((error) => {
            console.error('Ошибка при получении отчётов:', error);
        });
    }, [id]);

    useEffect(() => {
        if (directionCrossing?.from_city?.name === "Каменный Лог" || directionCrossing?.to_city?.name === "Каменный Лог") {
            fetch('https://belarusborder.by/info/monitoring-new?token=test&checkpointId=b60677d4-8a00-4f93-a781-e129e1692a03')
                .then((response) => response.json())
                .then((data) => setWaitingArea(data))
                .catch((error) => console.error('Ошибка при получении данных:', error));
        } else if (directionCrossing?.from_city?.name === "Брест" || directionCrossing?.to_city?.name === "Брест") {
            fetch('https://belarusborder.by/info/monitoring-new?token=test&checkpointId=a9173a85-3fc0-424c-84f0-defa632481e4')
                .then((response) => response.json())
                .then((data) => setWaitingArea(data))
                .catch((error) => console.error('Ошибка при получении данных:', error));
        } else if (directionCrossing?.from_city?.name === "Бенякони" || directionCrossing?.to_city?.name === "Бенякони") {
            fetch('https://belarusborder.by/info/monitoring-new?token=test&checkpointId=53d94097-2b34-11ec-8467-ac1f6bf889c0')
                .then((response) => response.json())
                .then((data) => setWaitingArea(data))
                .catch((error) => console.error('Ошибка при получении данных:', error));
        }
    }, [directionCrossing]);

    // Функция для вычисления разницы во времени
    const calculateTimeDifference = (registrationDate) => {
        const [timeStr, dateStr] = registrationDate.split(' ');
        const [day, month, year] = dateStr.split('.').map(Number);
        const [hours, minutes, seconds] = timeStr.split(':').map(Number);
        const registrationDateUTC = new Date(Date.UTC(year, month - 1, day, hours, minutes, seconds));

        const now = new Date();
        const belarusTime = new Date(now.getTime() + 3 * 60 * 60 * 1000);

        const differenceInMs = belarusTime - registrationDateUTC;

        const differenceInMinutes = Math.floor(differenceInMs / 60000);
        const hoursDiff = Math.floor(differenceInMinutes / 60);
        const minutesDiff = differenceInMinutes % 60;
        return { hours: hoursDiff, minutes: minutesDiff };
    };

    const renderWaitingAreaInfo = () => {
        if (waitingArea && waitingArea.carLiveQueue.length > 0) {
            const regTime = waitingArea.carLiveQueue[0].registration_date;

            const { hours, minutes } = calculateTimeDifference(regTime);

            let parts = [];

            // Добавляем часы в строку, если значение часов больше нуля
            if (hours > 0) {
                parts.push(declensionHours(hours));
            }

            // Добавляем минуты в строку, если значение минут больше нуля
            if (minutes > 0) {
                parts.push(declensionMinutes(minutes));
            }

            // Формируем строку в зависимости от наличия элементов
            if (parts.length > 0) {
                return `${parts.join(' ')}`;
            } else {
                return 'неизвестно';
            }        }
        return "Загрузка...";
    };

    return (
        <AppRoot>
            <div>
                {directionCrossing === undefined ? (
                    <div className="loader-overlay">
                        <ThreeDots height="80" width="80" color="#007aff" ariaLabel="loading" />
                    </div>
                ) : (
                    <div>
                        <div className="image-container">
                            <img src={`/${directionCrossing?.header_image}`} alt="header" className="header-image" />
                            <div className="overlay-text">
                                <Text weight="1" className="img-text">
                                    {directionCrossing?.from_city.name} - {directionCrossing?.to_city.name}
                                </Text>
                            </div>
                        </div>

                        <div className="container">
                            <div className="btn-container">
                                <div
                                    className={'btn-crossing'}
                                    style={{ width: '35%' }}
                                    onClick={() => {
                                        navigate(`/borderCrossing/${id}/cameras`, {
                                            state: { directionCrossing, direction }
                                        });
                                    }}
                                >
                                    <InlineButtonsItem
                                        style={{ width: '100%', height: '85%' }}
                                        mode="gray"
                                        text="Камеры"
                                    >
                                        <VscDeviceCamera style={{ width: '100%', height: '30%' }} />
                                    </InlineButtonsItem>
                                </div>
                                <div
                                    className={'btn-crossing'}
                                    style={{ width: '35%' }}
                                    onClick={() => openUrlInNewTab(directionCrossing.url_arcticle)}
                                >
                                    <InlineButtonsItem
                                        style={{ width: '100%', height: '85%' }}
                                        mode="gray"
                                        text="Информация"
                                    >
                                        <IoInformation style={{ width: '100%', height: '30%' }} />
                                    </InlineButtonsItem>
                                </div>
                            </div>

                            {(directionCrossing?.from_city?.country.name === "Беларусь" || directionCrossing?.to_city?.country.name === "Беларусь") && (
                                <div className="bel-container-inf report-title">
                                    <div>
                                        <Text weight="1" className={""}>
                                            {waitingArea ? `Текущая очередь в зоне ожидания: ${waitingArea.carLiveQueue?.length || 0}` : "Загрузка..."}
                                        </Text>
                                    </div>

                                    <div className="time-container-title">
                                        <Text weight="1">
                                            Предполагаемое время до въезда в ПП:
                                        </Text>
                                        {renderWaitingAreaInfo()}
                                    </div>
                                </div>
                            )}

                            <div className="report-container-title">
                                <Text weight="1">
                                    Отчёты о прохождении
                                </Text>
                                <Text weight="1" className={"link-report"} onClick={() => {
                                    navigate(`/borderCrossing/${id}/reports`, {
                                        state: { directionCrossing, direction }
                                    });
                                }}>
                                    Смотреть все
                                </Text>
                            </div>

                            <List
                                onClick={() => {
                                    navigate(`/borderCrossing/${id}/reports`, {
                                        state: { directionCrossing, direction }
                                    });
                                }}
                                className={"report-list"}
                            >
                                {reports.map((report, index) => {
                                    const entryTime = new Date(report.checkpoint_entry);
                                    const exitTime = new Date(report.checkpoint_exit);
                                    const options = {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                    };
                                    const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                    const formattedDate = formatter.format(exitTime);

                                    let differenceInMs = exitTime - entryTime;

                                    if (report.checkpoint_queue !== null) {
                                        const queueTime = new Date(report.checkpoint_queue);
                                        differenceInMs = exitTime - queueTime;
                                    }

                                    const differenceInMinutes = Math.floor(differenceInMs / 60000);
                                    const hours = Math.floor(differenceInMinutes / 60);
                                    const minutes = differenceInMinutes % 60;
                                    const timeDifference = `${declensionHours(hours)} ${declensionMinutes(minutes)}`;

                                    return (
                                        <div className="border-crossing-container" key={index}>
                                            <div className={"border-info-container"}>
                                                <AvatarStack className={"avatar-stack-report"}>
                                                    <React.Fragment>
                                                        {!report.is_flipped_direction ? (
                                                            <div className={"report-elem-logo-container"}>
                                                                <Avatar size={20} src={`/${directionCrossing.from_city.country.logo}`} />
                                                                <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`} />
                                                                <Avatar size={20} src={`/${directionCrossing.to_city.country.logo}`} />
                                                            </div>
                                                        ) : (
                                                            <div className={"report-elem-logo-container"}>
                                                                <Avatar size={20} src={`/${directionCrossing.to_city.country.logo}`} />
                                                                <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`} />
                                                                <Avatar size={20} src={`/${directionCrossing.from_city.country.logo}`} />
                                                            </div>
                                                        )}
                                                    </React.Fragment>
                                                </AvatarStack>
                                                <Text weight="3" className={"text-card"} style={{ marginTop: "0" }}>
                                                    {timeDifference}
                                                </Text>
                                            </div>
                                            <Text weight="3" style={{ marginTop: "0" }}>
                                                {formattedDate}
                                            </Text>
                                        </div>
                                    );
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
