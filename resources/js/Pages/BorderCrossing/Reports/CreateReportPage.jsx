import React, {useState, useEffect, useRef} from "react";
import '../styles.css';
import '../border-info-styles.css';
import './reports-styles.css';
import './create-report-styles.css';
import '@telegram-apps/telegram-ui/dist/styles.css';

import {
    AppRoot,
    Section,
    Cell,
    Input,
    Snackbar,
    Text,
    Textarea, Radio
} from "@telegram-apps/telegram-ui";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import ReportService from "@/API/ReportService.js";
import TransportService from "@/API/TransportService.js";
import {retrieveLaunchParams, useBackButton, useMainButton, usePopup} from "@tma.js/sdk-react";
import {IoBus, IoCar, IoCheckmark, IoCloseSharp, IoWalk} from "react-icons/io5";
import Icon from "@/Pages/BorderCrossing/Reports/Icon.jsx";

export default function CreateReportPage() {
    const location = useLocation();
    const [transports, setTransports] = useState([]);
    const [selectedDirection, setSelectedDirection] = useState('');
    const [selectedTransport, setSelectedTransport] = useState('');
    const [checkpointQueue, setCheckpointQueue] = useState('');
    const [timeEnterWaitingArea, setTimeEnterWaitingArea] = useState('');
    const [checkpointEntry, setCheckpointEntry] = useState('');
    const [checkpointExit, setCheckpointExit] = useState('');
    const [comment, setComment] = useState('');
    const [isFlippedDirection, setIsFlippedDirection] = useState(false);

    const { id } = useParams();
    const navigate = useNavigate();
    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;

    const backButton = useBackButton();
    const mainButton = useMainButton();

    // Используем useRef для захвата последнего значения комментария
    const commentRef = useRef(comment);

    useEffect(() => {
        commentRef.current = comment;
    }, [comment]);


    // Определение iOS и macOS
    const isIOS = () => {
        return /iPad|iPhone|iPod/.test(navigator.platform) && !window.MSStream;
    };

    const isMacOS = () => {
        return /Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.platform) && !isIOS();
    };

    // Применение классов к <html>
    useEffect(() => {

        if (isIOS()) {
            document.documentElement.classList.add('ios');
        } else if (isMacOS()) {
            document.documentElement.classList.add('macos');
        }
    }, []);

    useEffect(() => {
        const reportData = {
            border_crossing_id: id,
            transport_id: selectedTransport,
            user_id: 1,  // Adjust according to your user data
            checkpoint_queue: checkpointQueue,
            checkpoint_entry: checkpointEntry,
            checkpoint_exit: checkpointExit,
            comment: commentRef.current,
            is_flipped_direction: isFlippedDirection,
            time_enter_waiting_area: timeEnterWaitingArea,
        };


        if ( ( (!isFlippedDirection && directionCrossing?.from_city?.country.name === "Беларусь")
            || (isFlippedDirection && directionCrossing?.to_city?.country.name === "Беларусь") ) ) {

            if (selectedTransport === "3" && checkpointEntry !== "" && checkpointExit !== "") {
                mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
            } else {
                if (selectedTransport !== "" && checkpointEntry !== "" && checkpointExit !== "" && timeEnterWaitingArea !== "") {
                    mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
                } else {
                    mainButton.setText("Заполните обязательные поля").setBgColor("#808080").show().disable();
                }
            }
        } else {
            if (selectedTransport !== "" && checkpointEntry !== "" && checkpointExit !== "") {
                mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
            } else {
                mainButton.setText("Заполните обязательные поля").setBgColor("#808080").show().disable();
            }
        }

    }, [selectedTransport, checkpointEntry, checkpointExit, selectedDirection, isFlippedDirection, timeEnterWaitingArea]);

    useEffect(() => {
        backButton.show();
    }, []);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/borderCrossing/${directionCrossing.id}/reports`, {
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

    useEffect(() => {
        TransportService.getAll().then((r) => {
            if (!(r instanceof Error)) {
                setTransports(r);
            }
        }).catch((r) => {
            console.error('Error fetching transports:', r);
        });
    }, [id]);

    const popup = usePopup();

    const handleMainButtonClick = () => {

        mainButton.setText("Отправить данные").setBgColor("#808080").show().disable();

        const { initDataRaw, initData } = retrieveLaunchParams();

        const reportData = {
            border_crossing_id: id,
            transport_id: selectedTransport,
            user_id: Number(initData.user.id),  // Adjust according to your user data
            checkpoint_queue: checkpointQueue,
            checkpoint_entry: checkpointEntry,
            checkpoint_exit: checkpointExit,
            comment: commentRef.current,
            is_flipped_direction: isFlippedDirection,
            time_enter_waiting_area: timeEnterWaitingArea,
        };

        ReportService.createReport(reportData)
            .then(response => {
                popup.open({title: "Успешно", message: "Отчет успешно создан", buttons: [{type: "default", text: "Ok"}]});

                backButton.hide();
                mainButton.hide();

                // Переход обратно на страницу отчетов
                navigate(`/borderCrossing/${id}/reports`, {
                    state: {
                        directionCrossing: directionCrossing,
                        direction: direction
                    }
                });

            })
            .catch(error => {

                popup.open({title: "Ошибка", message: "Произошла ошибка при создании отчета", buttons: [{type: "default", text: "Ok"}]});

                mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
                backButton.show();

                console.error('Error creating report:', error);
            });

    };

    useEffect(() => {
        mainButton.on("click", handleMainButtonClick);

        return () => {
            mainButton.off("click", handleMainButtonClick);
        }
    }, [mainButton, selectedTransport, checkpointEntry, checkpointExit, selectedDirection]);

    return (
        <AppRoot>
            <div>
                {directionCrossing === undefined ? (
                    <div className="loader-overlay">
                        <ThreeDots height="80" width="80" color="#007aff" ariaLabel="loading" />
                    </div>
                ) : (

                    <Section header={"Добавление отчета о прохождении границы"}>
                        <div className="container">

                            <Text className="choose-direction-title">
                                Выберите направление
                            </Text>

                            <form>
                                <Cell
                                    Component="label"
                                    before={<Radio name="radio" value={`false`} checked={!isFlippedDirection}/>}
                                    multiline
                                    onChange={() => setIsFlippedDirection(false)}
                                >
                                    {`${directionCrossing.from_city.country.name} -> ${directionCrossing.to_city.country.name}`}
                                </Cell>

                                <Cell
                                    Component="label"
                                    before={<Radio name="radio" value={`true`} checked={isFlippedDirection}/>}
                                    multiline
                                    onChange={() => setIsFlippedDirection(true)}
                                >
                                    {`${directionCrossing.to_city.country.name} -> ${directionCrossing.from_city.country.name}`}
                                </Cell>
                            </form>

                            <hr/>

                            <Text className="choose-direction-title">
                                Выберите тип пересечения границы
                            </Text>


                            <div className="transport-container">
                                <form className="transport-container">
                                    {transports.map((transport, index) => (
                                        <div key={index}>
                                            {(transport.name === "Car" && directionCrossing.is_car) ? (
                                                <Cell
                                                    Component="label"
                                                    before={<Radio name="radio" value={`${transport.id}`}/>}
                                                    multiline
                                                    onChange={(e) => setSelectedTransport(e.target.value)}
                                                >
                                                    <IoCar size={28}/>
                                                </Cell>
                                            ) : (transport.name === "Bus" && directionCrossing.is_bus) ? (
                                                <Cell
                                                    Component="label"
                                                    before={<Radio name="radio" value={`${transport.id}`}/>}
                                                    multiline
                                                    onChange={(e) => setSelectedTransport(e.target.value)}
                                                >
                                                    <IoBus size={28}/>
                                                </Cell>
                                            ) : (transport.name === "Walking" && directionCrossing.is_walking) ? (
                                                <Cell
                                                    Component="label"
                                                    before={<Radio name="radio" value={`${transport.id}`}/>}
                                                    multiline
                                                    onChange={(e) => setSelectedTransport(e.target.value)}
                                                >
                                                    <IoWalk size={28}/>
                                                </Cell>
                                            ) : null}
                                        </div>
                                    ))}
                                </form>
                            </div>

                            <hr/>

                            {(((!isFlippedDirection && directionCrossing?.from_city?.country.name === "Беларусь")
                                || (isFlippedDirection && directionCrossing?.to_city?.country.name === "Беларусь"))
                                && selectedTransport !== "3") ? (
                                <div>
                                    {(isMacOS() || isIOS()) && (
                                        <>
                                            <Text>
                                                {"Выберите время подъезда к очереди в зону ожидания (необязательно)"}
                                            </Text>
                                        </>
                                    )}

                                    <Input
                                        className={"datetime-ios"}
                                        header={"Выберите время подъезда к очереди в зону ожидания (необязательно)"}
                                        name="checkpointQueue"
                                        type="datetime-local"
                                        value={checkpointQueue}
                                        onChange={(e) => setCheckpointQueue(e.target.value)}
                                    />

                                    {(isMacOS() || isIOS()) && (
                                        <>
                                            <Text>
                                                {"Выберите время въезда в зону ожидания"}
                                            </Text>
                                        </>
                                    )}

                                    <Input
                                        className={"datetime-ios"}
                                        header={"Выберите время въезда в зону ожидания"}
                                        name="timeEnterWaitingArea"
                                        type="datetime-local"
                                        value={timeEnterWaitingArea}
                                        onChange={(e) => setTimeEnterWaitingArea(e.target.value)}
                                    />

                                </div>
                            ) : (
                                <div>
                                    {(isMacOS() || isIOS()) && (
                                        <>
                                            <Text>
                                                {"Время подъезда к очереди на КПП (необязательно)"}
                                            </Text>
                                        </>
                                    )}

                                    <Input
                                        className={"datetime-ios"}
                                        header={"Время подъезда к очереди на КПП (необязательно)"}
                                        name="checkpointQueue"
                                        type="datetime-local"
                                        value={checkpointQueue}
                                        onChange={(e) => setCheckpointQueue(e.target.value)}
                                    />
                                </div>
                            )}

                            <hr/>

                            {(isMacOS() || isIOS()) && (
                                <>
                                    <Text>
                                        {"Время въезда на первый КПП"}
                                    </Text>
                                </>
                            )}

                            <Input
                                header={"Время въезда на первый КПП"}
                                name="checkpointEntry"
                                type="datetime-local"
                                value={checkpointEntry}
                                onChange={(e) => setCheckpointEntry(e.target.value)}
                                placeholder="Выберите дату и время"
                                defaultValue=""
                            />

                            <hr/>

                            {(isMacOS() || isIOS()) && (
                                <>
                                    <Text>
                                        {"Время выезда с последнего КПП"}
                                    </Text>
                                </>
                            )}

                            <Input
                                header={"Время выезда с последнего КПП"}
                                name="checkpointExit"
                                type="datetime-local"
                                value={checkpointExit}
                                onChange={(e) => setCheckpointExit(e.target.value)}
                            />

                            <hr/>

                            <Textarea
                                header={"Комментарий (необязательно)"}
                                name="comment"
                                value={comment}
                                onChange={(e) => setComment(e.target.value)}
                                placeholder={"Напишите комментарий (необязательно)"}
                            />
                        </div>
                    </Section>
                )}
            </div>
        </AppRoot>
    );
}
