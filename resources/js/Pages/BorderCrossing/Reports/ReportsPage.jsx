import '../styles.css';
import '../border-info-styles.css';
import './reports-styles.css';
import {
    Accordion,
    AppRoot,
    Avatar,
    AvatarStack, Badge,
    Blockquote,
    Button, ButtonCell, Cell, Info,
    List, Radio,
    Section, Snackbar,
    Text
} from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {useLocation, useNavigate, useParams} from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import { ServerURL } from "@/API/ServerConst.js";
import ReportService from "@/API/ReportService.js";
import {
    AccordionSummary
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionSummary/AccordionSummary.js";
import {
    AccordionContent
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionContent/AccordionContent.js";
import CommentAccordion from "@/Pages/BorderCrossing/Reports/CommentAccrodion.jsx";
import {retrieveLaunchParams, useBackButton, useMainButton, usePopup} from "@tma.js/sdk-react";
import {IoBus, IoCar, IoCloseSharp, IoWalk} from "react-icons/io5";
import {Icon28AddCircle} from "@telegram-apps/telegram-ui/dist/icons/28/add_circle.js";
import {Icon28Close} from "@telegram-apps/telegram-ui/dist/icons/28/close.js";
import Icon from "@/Pages/BorderCrossing/Reports/Icon.jsx";
import html2canvas from "html2canvas";
import {AiFillMessage} from "react-icons/ai";
import {Icon28Chat} from "@telegram-apps/telegram-ui/dist/icons/28/chat.js";

export default function ReportsPage() {
    const location = useLocation();


    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;

    const backButton = useBackButton();
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
            mainButton.hide();
        };

        backButton.on("click", handleBackButtonClick);

        return () => {
            backButton.off("click", handleBackButtonClick);
        }
    }, [backButton]);

    const [reports, setReports] = useState([])

    const { id } = useParams();

    const navigate = useNavigate();



    useEffect(() => {
        const directionIdNumber = Number(id);

        ReportService.getAll(directionIdNumber).then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setReports(r);
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);

    const { initDataRaw, initData } = retrieveLaunchParams();


    const popup = usePopup();

    const [snackbarVisible, setSnackbarVisible] = useState(false);
    const [snackbarIcon, setSnackbarIcon] = useState(<Icon
        IconImage={IoCloseSharp}
        iconBackground="rgba(0,0,0,0)"
        iconColor="white"
        iconSize={40}
    />); // true -> успешно ; false -> ошибка
    const [snackbarMessage, setSnackbarMessage] = useState('');

    // Функция для удаления отчета
    const handleDeleteReport = (reportId) => {

        popup.open({title: "Подтвердите действие", message: "Вы уверены, что хотите удалить отчет?",
            buttons: [
                {type: "cancel"},
                {type: "ok", id: "accept"}
            ]
        }).then(buttonId => {
            if (buttonId !== "accept") return;

            const requestBody = { id: reportId }; // Формируем JSON тело запроса

            ReportService.deleteReport(requestBody) // Передаем requestBody в функцию удаления
                .then(() => {
                    // Удаляем отчет локально
                    setReports((prevReports) => prevReports.filter(report => report.id !== reportId));
                })
                .catch((error) => {
                    // Обработка ошибок
                    console.error("Ошибка при удалении отчета:", error);

                    setSnackbarMessage(error.response.data.message);
                    setSnackbarIcon(<Icon
                        IconImage={IoCloseSharp}
                        iconBackground="rgba(0,0,0,0)"
                        iconColor="white"
                        iconSize={40}
                    />);
                    setSnackbarVisible(true);
                });
        })
    };

    useEffect(() => {
        if (snackbarVisible) {
            const timer = setTimeout(() => setSnackbarVisible(false), 3000);
            return () => clearTimeout(timer);
        }
    }, [snackbarVisible]);

    const [expandedIndexes, setExpandedIndexes] = useState([]);

    // Функция для управления состоянием каждого аккордеона
    const toggleAccordion = (index) => {
        setExpandedIndexes(prevState => {
            if (prevState.includes(index)) {
                // Если индекс уже есть в массиве, удаляем его (закрываем аккордеон)
                return prevState.filter(i => i !== index);
            } else {
                // Иначе добавляем индекс (открываем аккордеон)
                return [...prevState, index];
            }
        });
    };

    // const filteredReports = reports.filter(report => report.transport.name === "Car");
    const [filteredReports, setFilteredReports] = useState([]);

    useEffect(() => {
        setFilteredReports(reports); // Устанавливаем начальное значение
    }, [reports]);

    const handleFilterChange = (transportName) => {
        if (transportName === "All") {
            setFilteredReports(reports);
            return
        }

        setFilteredReports(reports.filter(report => report.transport.name === transportName));
    };

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
        const shareText = document.querySelector('.share-text');

        if (shareText) {
            shareText.style.display = 'block'

        }

        const element = document.getElementById('report-section');

        // Задаём фон через CSS перед созданием снимка
        const root = document.documentElement;
        const rootStyles = getComputedStyle(root);
        const backgroundColor = rootStyles.getPropertyValue('--tg-theme-bg-color').trim();

        element.style.backgroundColor = backgroundColor;

        const canvas = await html2canvas(element, { backgroundColor: null }); // Захватываем элемент

        // Преобразуем canvas в Blob и сохраняем
        canvas.toBlob(async (blob) => {
            const file = new File([blob], "border-report.jpg", { type: "image/jpeg" });

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
        if (shareText) {
            shareText.style.display = 'none'
        }

    };

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
                    <Section header={`${directionCrossing.from_city.name} - ${directionCrossing.to_city.name}`} className={"title-section"}>
                        <Section header={"Отчёты о прохождении"} className={"subtitle-section"}>
                            <div className="container">
                                <Section header={"Сортировать"}>
                                    <div className="filter-param-container">
                                        <form className="transport-container">
                                            <Cell
                                                Component="label"
                                                before={<Radio name="radio"/>}
                                                multiline
                                                onChange={() => handleFilterChange("All")}
                                            >
                                                Все
                                            </Cell>

                                            <Cell
                                                Component="label"
                                                before={<Radio name="radio"/>}
                                                multiline
                                                onChange={() => handleFilterChange("Car")}
                                            >
                                                <IoCar size={28}/>
                                            </Cell>

                                            <Cell
                                                Component="label"
                                                before={<Radio name="radio"/>}
                                                multiline
                                                onChange={() => handleFilterChange("Bus")}
                                            >
                                                <IoBus size={28}/>
                                            </Cell>
                                        </form>
                                    </div>
                                </Section>

                                {filteredReports.map((report, index) => {

                                    // Предполагаем, что checkpoint_entry и checkpoint_exit - это объекты Date
                                    const entryTime = new Date(report.checkpoint_entry);
                                    const exitTime = new Date(report.checkpoint_exit);

                                    const options = {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                    };

                                    // Определяем параметры форматирования для даты и времени
                                    const optionsTime = {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    };


                                    let formattedDate = ""
                                    let formattedTime = ""

                                    let formattedDateEntry = ""
                                    let formattedTimeEntry = ""

                                    let formattedDateExit = ""
                                    let formattedTimeExit = ""

                                    let dateEnterWaitingArea = ""
                                    let timeEnterWaitingArea = ""

                                    const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                    const formatterTime = new Intl.DateTimeFormat('ru-RU', optionsTime);

                                    formattedDateEntry = formatter.format(entryTime);
                                    formattedTimeEntry = formatterTime.format(entryTime)

                                    formattedDateExit = formatter.format(exitTime);
                                    formattedTimeExit = formatterTime.format(exitTime)

                                    if ((!report.is_flipped_direction && directionCrossing?.from_city?.country.name === "Беларусь") || (report.is_flipped_direction && directionCrossing?.to_city?.country.name === "Беларусь")) {

                                        const enterWaitingAreaTime = new Date(report.time_enter_waiting_area);

                                        // Форматируем дату с учетом временной зоны пользователя
                                        const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                        const formatterTime = new Intl.DateTimeFormat('ru-RU', optionsTime);

                                        dateEnterWaitingArea = formatter.format(enterWaitingAreaTime);
                                        timeEnterWaitingArea = formatterTime.format(enterWaitingAreaTime)
                                    }

                                    if (report.checkpoint_queue !== null) {
                                        const queueTime = new Date(report.checkpoint_queue);

                                        // Форматируем дату с учетом временной зоны пользователя
                                        const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                        const formatterTime = new Intl.DateTimeFormat('ru-RU', optionsTime);
                                        formattedDate = formatter.format(queueTime);
                                        formattedTime = formatterTime.format(queueTime)
                                    }

                                    console.log(report)

                                    return (
                                        <div className={"report-container report-section"} id={"report-section"}>
                                            <div key={index} className={"title-crossing-container"}>
                                                <div className={"border-info-container"}>
                                                    <AvatarStack className={"avatar-stack-report"}>
                                                        <React.Fragment>
                                                            {!report.is_flipped_direction ? (
                                                                <div className={"report-elem-logo-container"}>
                                                                    <Avatar
                                                                        size={20}
                                                                        src={`/${directionCrossing.from_city.country.logo}`}
                                                                    />
                                                                    <Avatar
                                                                        className={"to-logo-size"}
                                                                        size={20}
                                                                        src={`/to_logo.svg`}
                                                                    />
                                                                    <Avatar
                                                                        size={20}
                                                                        src={`/${directionCrossing.to_city.country.logo}`}
                                                                    />
                                                                </div>
                                                            ) : (
                                                                <div className={"report-elem-logo-container"}>
                                                                    <Avatar
                                                                        size={20}
                                                                        src={`/${directionCrossing.to_city.country.logo}`}
                                                                    />
                                                                    <Avatar
                                                                        className={"to-logo-size"}
                                                                        size={20}
                                                                        src={`/to_logo.svg`}
                                                                    />
                                                                    <Avatar
                                                                        size={20}
                                                                        src={`/${directionCrossing.from_city.country.logo}`}
                                                                    />
                                                                </div>
                                                            )}
                                                        </React.Fragment>
                                                    </AvatarStack>

                                                    <Text weight="3" className={"text-card"}>
                                                        {report.time_difference_text}
                                                    </Text>
                                                </div>

                                                {(report.transport.name === "Car") ? (
                                                    <IoCar size={28}/>
                                                ) : (report.transport.name === "Bus") ? (
                                                    <IoBus size={28}/>
                                                ) : (report.transport.name === "Walking") ? (
                                                    <IoWalk size={28}/>
                                                ) : null}
                                            </div>

                                            {((report.checkpoint_queue !== null) && ((!report.is_flipped_direction && directionCrossing?.from_city?.country.name === "Беларусь")
                                                || (report.is_flipped_direction && directionCrossing?.to_city?.country.name === "Беларусь"))) ? (
                                                <div>

                                                    {report.transport.name === "Car" ? (
                                                        <div className={"time-desc-container"}>
                                                            <Text weight="3">
                                                                {`Очередь в зону ожидания`}
                                                            </Text>

                                                            <Text weight="3">
                                                                {formattedDate} в {formattedTime}
                                                            </Text>
                                                        </div>
                                                    ) : (
                                                        <div className={"time-desc-container"}>
                                                            <Text weight="3">
                                                                {`Очередь перед КПП`}
                                                            </Text>

                                                            <Text weight="3">
                                                                {formattedDate} в {formattedTime}
                                                            </Text>
                                                        </div>
                                                    )}


                                                    <hr/>
                                                </div>
                                            ) : (report.checkpoint_queue !== null) && (
                                                <div>
                                                    {report.transport.name === "Car" ? (
                                                        <div className={"time-desc-container"}>
                                                            <Text weight="3">
                                                                {`Очередь перед КПП`}
                                                            </Text>

                                                            <Text weight="3">
                                                                {formattedDate} в {formattedTime}
                                                            </Text>
                                                        </div>
                                                    ) : (
                                                        <div className={"time-desc-container"}>
                                                            <Text weight="3">
                                                                {`Очередь перед КПП`}
                                                            </Text>

                                                            <Text weight="3">
                                                                {formattedDate} в {formattedTime}
                                                            </Text>
                                                        </div>
                                                    )}


                                                    <hr/>
                                                </div>
                                            )}

                                            {(((!report.is_flipped_direction && directionCrossing?.from_city?.country.name === "Беларусь")
                                                || (report.is_flipped_direction && directionCrossing?.to_city?.country.name === "Беларусь")) && report.transport.name !== "Bus") && (
                                                <div>
                                                    <div className={"time-desc-container"}>
                                                        <Text weight="3">
                                                            {`Въезд в зону ожидания:`}
                                                        </Text>

                                                        <Text weight="3">
                                                            {dateEnterWaitingArea} в {timeEnterWaitingArea}
                                                        </Text>
                                                    </div>

                                                    <hr/>

                                                </div>
                                            )}


                                            <div className={"time-desc-container"}>
                                                <Text weight="3">
                                                    {`Въезд на КПП:`}
                                                </Text>

                                                <Text weight="3">
                                                    {formattedDateEntry} в {formattedTimeEntry}
                                                </Text>
                                            </div>

                                            <hr/>

                                            <div className={"time-desc-container"}>
                                                <Text weight="3">
                                                    {`Выезд с КПП:`}
                                                </Text>

                                                <Text weight="3">
                                                    {formattedDateExit} в {formattedTimeExit}
                                                </Text>
                                            </div>

                                            {report.comment !== "" && report.comment != null && (
                                                <>
                                                    <hr/>

                                                    <Accordion
                                                        onChange={() => toggleAccordion(index)}
                                                        expanded={expandedIndexes.includes(index)}
                                                    >
                                                        <AccordionSummary>Комментарий</AccordionSummary>
                                                        <AccordionContent>
                                                            <Text style={{
                                                                wordWrap: 'break-word',
                                                                wordBreak: 'break-all',
                                                                whiteSpace: 'pre-wrap'  // сохраняет форматирование пробелов и переносов строк
                                                            }}>
                                                                {report.comment}
                                                            </Text> </AccordionContent>
                                                    </Accordion>
                                                </>
                                            )}

                                            <ButtonCell
                                                before={<Icon28Chat/>}
                                                onClick={captureAndShare}
                                            >
                                                Поделиться
                                            </ButtonCell>

                                            {((initData.user.id === 747551551 || initData.user.id === 241666959)
                                                || (report.user_id === initData.user.id && report.is_show_button)) && (
                                                <ButtonCell
                                                    before={<Icon28Close/>}
                                                    mode="destructive"
                                                    onClick={() => {
                                                        handleDeleteReport(report.id)
                                                    }}
                                                >
                                                    Удалить
                                                </ButtonCell>
                                            )}

                                            <h4 id={"share-text"}

                                                className={"share-text"}>Очереди на границах:
                                                @bordercrossingsbot</h4>

                                        </div>
                                    )
                                })}

                                <Snackbar
                                    className={`snackbar ${snackbarVisible ? 'snackbar-show' : ''}`}
                                    onClose={() => setSnackbarVisible(false)}
                                    before={snackbarIcon}
                                    duration={3000}
                                >
                                    {snackbarMessage}
                                </Snackbar>
                            </div>
                        </Section>
                    </Section>
                )}
            </div>
        </AppRoot>
    );
}
