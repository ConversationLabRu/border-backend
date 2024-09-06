import './styles.css';
import './border-info-styles.css';
import {AppRoot, Avatar, AvatarStack, Button, List, Modal, Placeholder, Text} from "@telegram-apps/telegram-ui";
import { InlineButtonsItem } from "@telegram-apps/telegram-ui/dist/components/Blocks/InlineButtons/components/InlineButtonsItem/InlineButtonsItem.js";
import { useState, useEffect } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import ReportService from "@/API/ReportService.js";
import {useBackButton, useMainButton} from "@tma.js/sdk-react";
import {IoBus, IoCar, IoInformation} from "react-icons/io5";
import { VscDeviceCamera } from "react-icons/vsc";
import React from 'react';
import {
    ModalHeader
} from "@telegram-apps/telegram-ui/dist/components/Overlays/Modal/components/ModalHeader/ModalHeader.js";


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
            mainButton.hide();
        };
        backButton.on("click", handleBackButtonClick);
        return () => backButton.off("click", handleBackButtonClick);
    }, [backButton, navigate, direction, direction.id]);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    const mainButton = useMainButton();


    useEffect(() => {
        backButton.show()
        mainButton.setText("Добавить отчет").setBgColor("#007aff").show().enable();
    }, []);

    useEffect(() => {
        const handleMainButtonClick = () => {
            navigate(`/borderCrossing/${id}/reports/create`,
                {
                    state: {
                        directionCrossing: directionCrossing,
                        direction: direction
                    }
                }
            );

            mainButton.hide();
        };

        mainButton.on("click", handleMainButtonClick);

        return () => {
            mainButton.off("click", handleMainButtonClick);
        }
    }, [mainButton]);

    const [countryLogos, setCountryLogos] = useState({
        fromCountryLogo: null,
        toCountryLogo: null,
    });


    useEffect(() => {
        const directionIdNumber = Number(id);
        ReportService.getLast(directionIdNumber).then((r) => {
            if (!(r instanceof Error)) {
                setReports(r);

                if (r.length > 0) {
                    // Извлечь логотипы из первого отчета
                    const firstReport = r[0];
                    const fromCountryLogo = firstReport?.from_city?.country?.logo;
                    const toCountryLogo = firstReport?.to_city?.country?.logo;
                    setCountryLogos({
                        fromCountryLogo,
                        toCountryLogo,
                    });
                }
            }
        }).catch((error) => {
            console.error('Ошибка при получении отчётов:', error);
        });
    }, [id]);

    const handleShowStatistics = () => {
        const directionIdNumber = Number(id);
        ReportService.getStatistic(directionIdNumber).then((r) => {
            if (!(r instanceof Error)) {
                setStatisticData(r); // Обновить состояние с данными статистики
            }
        }).catch((error) => {
            console.error('Ошибка при получении отчётов:', error);
        });
    };

    const formatDirectionTime = (key, value) => {
        switch (key) {
            case 'timeCarNotFlipped':

                return <div>
                    <div className={"report-elem-logo-container stat-container"}>
                        <IoCar size={28}/>
                        <Avatar size={20} className={"first-elem-logo"} src={`/${directionCrossing.from_city.country.logo}`}/>
                        <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`}/>
                        <Avatar size={20} className={"stat-logo-last"} src={`/${directionCrossing.to_city.country.logo}`}/>

                        {value}
                    </div>
                </div>
            case 'timeCarFlipped':
                return <div>
                    <div className={"report-elem-logo-container stat-container"}>
                        <IoCar size={28}/>
                        <Avatar size={20} className={"first-elem-logo"} src={`/${directionCrossing.to_city.country.logo}`}/>
                        <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`}/>
                        <Avatar size={20} className={"stat-logo-last"} src={`/${directionCrossing.from_city.country.logo}`}/>

                        {value}
                    </div>
                </div>
            case 'timeBusNotFlipped':
                return <div>
                    <div className={"report-elem-logo-container stat-container"}>
                        <IoBus size={28}/>
                        <Avatar size={20} className={"first-elem-logo"} src={`/${directionCrossing.from_city.country.logo}`}/>
                        <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`}/>
                        <Avatar size={20} className={"stat-logo-last"} src={`/${directionCrossing.to_city.country.logo}`}/>

                        {value}
                    </div>
                </div>
            case 'timeBusFlipped':
                return <div>
                    <div className={"report-elem-logo-container stat-container"}>
                        <IoBus size={28}/>
                        <Avatar size={20} className={"first-elem-logo"} src={`/${directionCrossing.to_city.country.logo}`}/>
                        <Avatar className={"to-logo-size"} size={20} src={`/to_logo.svg`}/>
                        <Avatar size={20} className={"stat-logo-last"} src={`/${directionCrossing.from_city.country.logo}`}/>

                        {value}
                    </div>
                </div>
            default:
                return `${key}: ${value}`;
        }
    };

    const formatStatistics = (data) => {
        if (!data) return [];

        // Преобразовать данные в массив строк (или отформатировать как нужно)
        return Object.entries(data).map(([key, value], index) => (
            <React.Fragment key={index}>
                {/*<Text weight="1">{formatDirectionTime(key, value)}</Text>*/}

                {formatDirectionTime(key, value)}
                <br />
            </React.Fragment>
        ));
    };



    const [statisticData, setStatisticData] = useState(null); // Добавить состояние для хранения статистики

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
                                            {`Текущая очередь в зоне ожидания: ${directionCrossing['cache'].countCar}`}
                                        </Text>
                                    </div>

                                    <div className="time-container-title">
                                        <Text weight="1">
                                            Предполагаемое время до въезда в ПП:
                                        </Text>
                                        {directionCrossing["cache"]["time"]}
                                    </div>
                                </div>
                            )}

                            <Modal
                                header={<ModalHeader>Прогноз прохождения пункта пропуска</ModalHeader>}
                                trigger={<Button size="m" onClick={handleShowStatistics}>Посмотреть прогноз</Button>}
                            >
                                {statisticData ? (
                                    <Placeholder
                                        header={<>
                                            <Text weight="3" style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                Прогноз построен на основе отчетов о прохождении пункта пропуска.
                                            </Text>
                                            <br/>
                                            <Text weight="1" style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                Добавляя отчеты, вы значительно улучшаете точность прогноза.
                                            </Text>
                                        </>}                                    >
                                        {formatStatistics(statisticData)}

                                    </Placeholder>
                                ) : (
                                    <Placeholder
                                        header={<>
                                            <Text weight="3" style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                Прогноз построен на основе отчетов о прохождении пункта пропуска.
                                            </Text>
                                            <br/>
                                            <Text weight="1" style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                Добавляя отчеты, вы значительно улучшаете точность прогноза.
                                            </Text>
                                        </>}
                                    >
                                        <Text weight="1">
                                            Нет данных
                                        </Text>
                                    </Placeholder>
                                )}
                            </Modal>



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

                                    const exitTime = new Date(report.checkpoint_exit);
                                    const options = {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                    };
                                    const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                    const formattedDate = formatter.format(exitTime);

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
                                                    {report.time_difference_text}
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
