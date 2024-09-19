import './styles.css';
import './border-info-styles.css';
import {
    AppRoot,
    Avatar,
    AvatarStack,
    Button,
    List,
    Modal,
    Placeholder,
    TabsList,
    Text
} from "@telegram-apps/telegram-ui";
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
import html2canvas from "html2canvas";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {TabsItem} from "@telegram-apps/telegram-ui/dist/components/Navigation/TabsList/components/TabsItem/TabsItem.js";
import {LineChart} from "@mui/x-charts";
import {Switch} from "@mui/material";


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

    const [jsonData1, setJsonData1] = useState([]);
    const [isLoading, setIsLoading] = useState(true);


    // Получение данных с API для первой линии
    const fetchData1 = () => {
        const directionIdNumber = Number(id);
        ReportService.getStatisticGraph(directionIdNumber).then((r) => {
            if (!(r instanceof Error)) {
                console.log(r)
                setJsonData1(r); // Обновить состояние с данными статистики
            }
        }).catch((error) => {
            console.error('Ошибка при получении отчётов:', error);
        });
    };



    useEffect(() => {
        const fetchData = async () => {
            await fetchData1();
            setIsLoading(false);
        };
        fetchData();
    }, []);

// Функция для извлечения часов из строки времени
    const extractHours = (timeString) => {
        const match = timeString.match(/(\d{1,2}):\d{2}/);
        return match ? parseInt(match[1], 10) : null;
    };

    const endDate = new Date(); // Текущая дата
    const startDate = new Date();
    startDate.setDate(endDate.getDate() - 7); // Начальная дата (7 дней назад)

    const getDatesInRange = (start, end) => {
        let dates = [];
        let currentDate = new Date(start);

        while (currentDate < end) {
            // Используем toLocaleDateString для русского формата даты
            dates.push(currentDate.toLocaleDateString('ru-RU')); // Формат DD.MM.YYYY
            currentDate.setDate(currentDate.getDate() + 1);
        }

        return dates;
    };

    const [isFlippedDirection, setFlippedDirection] = useState(false)

    const formatToRussianDate = (dateString) => {
        const [year, month, day] = dateString.split('-');
        return `${day}.${month}.${year}`; // Формат DD.MM.YYYY
    };

    const  dailyForecast = (data) => {

        if (!data || data.length === 0) return [];



        // Преобразование данных для графика
        const xAxisData = getDatesInRange(startDate, endDate);
        console.log(jsonData1)

        // Преобразуем данные из jsonData1
        var waitTimeData1;
        var waitTimeData2;

        if (isFlippedDirection) {

            waitTimeData1 = xAxisData.map(date => {
                const item = data.timeWeekToFlip.car.find(d => formatToRussianDate(d.day) === date);
                return item ? parseFloat(item.avg_time) : 0.1; // Используем значение avg_time или 0, если данных нет
            });

            waitTimeData2 = xAxisData.map(date => {
                const item = data.timeWeekToFlip.bus.find(d => formatToRussianDate(d.day) === date);
                return item ? parseFloat(item.avg_time) : 0.1; // Используем значение avg_time или 0, если данных нет
            });

        } else {

            waitTimeData1 = xAxisData.map(date => {
                const item = data.timeWeekTo.car.find(d => formatToRussianDate(d.day) === date);
                return item ? parseFloat(item.avg_time) : 0.1; // Используем значение avg_time или 0, если данных нет
            });

            waitTimeData2 = xAxisData.map(date => {
                const item = data.timeWeekTo.bus.find(d => formatToRussianDate(d.day) === date);
                return item ? parseFloat(item.avg_time) : 0.1; // Используем значение avg_time или 0, если данных нет
            });

        }

        // Объединяем два массива, убираем дубликаты, сортируем и преобразуем в часы
        const mergedData = [...waitTimeData1, ...waitTimeData2] // Объединить массивы
            .filter((value, index, self) => self.indexOf(value) === index) // Удалить дубликаты
            .filter(value => value !== 0)
            .sort((a, b) => a - b) // Сортировка по возрастанию
            .map(minutes => Math.floor(minutes / 60)); // Преобразование минут в часы (без минут)

        console.log(mergedData)

        // Функция для форматирования значений
        const valueFormatterText = (value) => {
            return value !== 0.1 ? declensionHours(Math.floor(value / 60)) + " " + declensionMinutes(Math.floor(value % 60)) : 'нет данных';
        };

        const yAxisFormatter = (value) => {
            if (value === 0) return ''; // Если значение равно 0, вернуть пустую строку
            const hours = Math.floor(value / 60);
            return `${hours}`;
        };


        // Преобразовать данные в массив строк (или отформатировать как нужно)
        return <div className="graf-container">
            <React.Fragment>
                <LineChart
                    xAxis={[{
                        scaleType: "band",
                        data: xAxisData
                    }]}  // Ось X будет общей для обоих графиков
                    yAxis={[{
                        scaleType: "linear",
                        valueFormatter: yAxisFormatter

                    }]}
                    series={[
                        {
                            curve: "linear",
                            data: waitTimeData1,  // Время ожидания для первой линии
                            color: "red",
                            label: 'Автомобиль',
                            valueFormatter: valueFormatterText
                        },
                        {
                            curve: "linear",
                            data: waitTimeData2,  // Время ожидания для первой линии
                            label: 'Автобус',
                            valueFormatter: valueFormatterText
                        },
                    ]}
                    width={400}
                    height={300}
                />
            </React.Fragment>
        </div>
    };

    const [statisticData, setStatisticData] = useState(null); // Добавить состояние для хранения статистики

    // Определение iOS и macOS
    const isIOS = () => {
        return /iPad|iPhone|iPod/.test(navigator.platform) && !window.MSStream;
    };

    useEffect(() => {
        if (isIOS()) {
            document.documentElement.classList.add('ios');
        }
    }, []);

    // Функция для захвата изображения и отправки
    const captureAndShare = async () => {
        const shareButton = document.querySelector('.share-button');
        const shareText = document.querySelector('.share-text');

        if (shareButton) {
            shareButton.style.display = 'none'; // Скрываем кнопку
            shareText.style.display = 'block'

        }

        const element = document.getElementById('statistic-placeholder');

// Задаём фон через CSS перед созданием снимка
        const root = document.documentElement;
        const rootStyles = getComputedStyle(root);
        const backgroundColor = rootStyles.getPropertyValue('--tg-theme-bg-color').trim();

        element.style.backgroundColor = backgroundColor;

        const canvas = await html2canvas(element, { backgroundColor: null }); // Захватываем элемент

        // Преобразуем canvas в Blob и сохраняем
        canvas.toBlob(async (blob) => {
            const file = new File([blob], "border-crossing-info.jpg", { type: "image/jpeg" });

            if (isIOS()) {
                await navigator.share({
                    title: 'Очереди на границах',
                    text: 'Очереди на границах: @bordercrossingsbot',
                    files: [file]
                });
            } else {
                const formData = new FormData();
                formData.append('image', file);
                await BorderCrossingService.sendPhotoPost(formData);
            }
        });

// Восстанавливаем кнопку
        if (shareButton) {
            shareButton.style.display = ''; // Восстанавливаем отображение кнопки
            shareText.style.display = 'none'
        }

    };

    const [activeTab, setActiveTab] = useState('generalForecast');

    // Функция для рендера содержимого на основе активной вкладки
    const renderContent = () => {
        return formatStatistics(statisticData);

    };

    // Функция для рендера содержимого на основе активной вкладки
    const renderContentStat = () => {
        return dailyForecast(jsonData1);

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
                                    style={{width: '35%'}}
                                    onClick={() => {
                                        navigate(`/borderCrossing/${id}/cameras`, {
                                            state: {directionCrossing, direction}
                                        });
                                    }}
                                >
                                    <InlineButtonsItem
                                        style={{width: '100%', height: '85%'}}
                                        mode="gray"
                                        text="Камеры"
                                    >
                                        <VscDeviceCamera style={{width: '100%', height: '30%'}}/>
                                    </InlineButtonsItem>
                                </div>
                                <div
                                    className={'btn-crossing'}
                                    style={{width: '35%'}}
                                    onClick={() => openUrlInNewTab(directionCrossing.url_arcticle)}
                                >
                                    <InlineButtonsItem
                                        style={{width: '100%', height: '85%'}}
                                        mode="gray"
                                        text="Информация"
                                    >
                                        <IoInformation style={{width: '100%', height: '30%'}}/>
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

                            {((directionCrossing?.from_city?.country.name === "Польша" || directionCrossing?.to_city?.country.name === "Польша")
                                && (directionCrossing['cachePoland'].timeCarFormatString !== ""
                                    || directionCrossing['cachePoland'].timeBusFormatString !== "")) && (
                                <div className="bel-container-inf report-title">
                                    <div>
                                        <Text weight="1" className={""}>
                                            {`Ожидание въезда на КПП с Польской стороны по информации Погрануправления РП на ${directionCrossing['cachePoland'].timeUpdate}:`}
                                        </Text>
                                    </div>

                                    <div className="time-container-title-poland">
                                        <div className={"report-elem-logo-container stat-container-poland"}>
                                            <div className={"elem-poland-time"}>
                                                <IoCar size={28}/>
                                                {directionCrossing['cachePoland'].timeCarFormatString}
                                            </div>

                                            <div className={"elem-poland-time"}>
                                                <IoBus size={28}/>
                                                {directionCrossing['cachePoland'].timeBusFormatString}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div style={{display: 'flex', justifyContent: 'center', alignItems: 'center', gap: "10px"}}>
                                <Modal
                                    header={<ModalHeader>Прогноз прохождения пункта пропуска</ModalHeader>}
                                    trigger={<Button size="m" style={{width: "50%"}} onClick={handleShowStatistics}>Прогноз</Button>}
                                >
                                    {statisticData ? (
                                        <Placeholder
                                            id={"statistic-placeholder"}
                                            className={"statistic-placeholder"}
                                            header={<>
                                                {activeTab === "generalForecast" ? (
                                                    <div>
                                                        <Text weight="3"
                                                              style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                            Прогноз построен на основе отчетов о прохождении пункта
                                                            пропуска.
                                                        </Text>
                                                        <br/>
                                                        <Text weight="1"
                                                              style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                            Добавляя отчеты, вы значительно улучшаете точность прогноза.
                                                        </Text>
                                                    </div>
                                                ) : (
                                                    <div>
                                                        <Text weight="1">
                                                            В обратном направлении:
                                                        </Text>
                                                        <Switch onClick={() => {
                                                            setFlippedDirection(!isFlippedDirection)
                                                        }} checked={isFlippedDirection}/>
                                                        <Text weight="3"
                                                              style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                            Прогноз времени ожидания въезда на КПП в ближайшие 24 часа
                                                            по данным Погрануправления РП
                                                        </Text>
                                                    </div>
                                                )}
                                                <h4 id={"share-text"} className={"share-text"}>Очереди на границах:
                                                    @bordercrossingsbot</h4>
                                            </>}>
                                            {<div>{renderContent()}</div>}
                                        </Placeholder>
                                    ) : (
                                        <Placeholder
                                            id={"statistic-placeholder"}
                                            className={"statistic-placeholder"}
                                            header={<>
                                                <Text weight="3"
                                                      style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                    Прогноз построен на основе отчетов о прохождении пункта пропуска.
                                                </Text>
                                                <br/>
                                                <Text weight="1"
                                                      style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
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

                                <Modal
                                    header={<ModalHeader>Статистика прохождения пункта пропуска</ModalHeader>}
                                    trigger={<Button size="m" style={{width: "50%"}} onClick={fetchData1}>Статистика</Button>}
                                >
                                    {jsonData1 ? (
                                        <Placeholder
                                            id={"statistic-placeholder"}
                                            className={"statistic-placeholder"}
                                            header={<>
                                                <div>

                                                    {isFlippedDirection ? (
                                                        <Text>
                                                            {directionCrossing.to_city.name} - {directionCrossing.from_city.name}
                                                        </Text>
                                                    ) : (
                                                        <Text>
                                                            {directionCrossing.from_city.name} - {directionCrossing.to_city.name}
                                                        </Text>
                                                    )}

                                                    <Text weight="1" style={{display: "inline-block"}}>
                                                        В обратном направлении:
                                                    </Text>

                                                    <Switch onClick={() => {
                                                        setFlippedDirection(!isFlippedDirection)
                                                    }} checked={isFlippedDirection}/>
                                                    <Text weight="3"
                                                          style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                        Статистика за последние 7 дней на основе отчетов о прохождении пункта пропуска
                                                    </Text>
                                                    <br/>
                                                    <Text weight="1"
                                                          style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                        Добавляя отчеты, вы значительно улучшаете статистику.
                                                    </Text>
                                                </div>
                                            </>}>
                                            {<div>{renderContentStat()}</div>}
                                        </Placeholder>
                                    ) : (
                                        <Placeholder
                                            id={"statistic-placeholder"}
                                            className={"statistic-placeholder"}
                                            header={<>
                                                <Text weight="3"
                                                      style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
                                                    Прогноз построен на основе отчетов о прохождении пункта пропуска.
                                                </Text>
                                                <br/>
                                                <Text weight="1"
                                                      style={{fontSize: "calc(var(--tgui--text--font_size) - 10%)"}}>
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
                            </div>


                            <div className="report-container-title">
                                <Text weight="1">
                                    Отчёты о прохождении
                                </Text>
                                <Text weight="1" className={"link-report"} onClick={() => {
                                    navigate(`/borderCrossing/${id}/reports`, {
                                        state: {directionCrossing, direction}
                                    });
                                }}>
                                    Смотреть все
                                </Text>
                            </div>

                            <List
                                onClick={() => {
                                    navigate(`/borderCrossing/${id}/reports`, {
                                        state: {directionCrossing, direction}
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
                                                                <Avatar size={20}
                                                                        src={`/${directionCrossing.from_city.country.logo}`}/>
                                                                <Avatar className={"to-logo-size"} size={20}
                                                                        src={`/to_logo.svg`}/>
                                                                <Avatar size={20}
                                                                        src={`/${directionCrossing.to_city.country.logo}`}/>
                                                            </div>
                                                        ) : (
                                                            <div className={"report-elem-logo-container"}>
                                                                <Avatar size={20}
                                                                        src={`/${directionCrossing.to_city.country.logo}`}/>
                                                                <Avatar className={"to-logo-size"} size={20}
                                                                        src={`/to_logo.svg`}/>
                                                                <Avatar size={20}
                                                                        src={`/${directionCrossing.from_city.country.logo}`}/>
                                                            </div>
                                                        )}
                                                    </React.Fragment>
                                                </AvatarStack>
                                                <Text weight="3" className={"text-card"} style={{marginTop: "0"}}>
                                                    {report.time_difference_text}
                                                </Text>
                                            </div>
                                            <Text weight="3" style={{marginTop: "0"}}>
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
