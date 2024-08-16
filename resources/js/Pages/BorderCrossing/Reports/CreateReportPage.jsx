import React, { useState, useEffect } from "react";
import '../styles.css';
import '../border-info-styles.css';
import './reports-styles.css';
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
import {useBackButton, useMainButton, usePopup} from "@tma.js/sdk-react";
import { IoCheckmark, IoCloseSharp } from "react-icons/io5";
import Icon from "@/Pages/BorderCrossing/Reports/Icon.jsx";

export default function CreateReportPage() {
    const location = useLocation();
    const [transports, setTransports] = useState([]);
    const [selectedDirection, setSelectedDirection] = useState('');
    const [selectedTransport, setSelectedTransport] = useState('');
    const [checkpointQueue, setCheckpointQueue] = useState('');
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

    useEffect(() => {
        if (selectedTransport !== "" && checkpointEntry !== "" && checkpointExit !== "" && selectedDirection !== "") {
            mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
        } else {
            mainButton.setText("Заполните обязательные поля").setBgColor("#808080").show().disable();
        }
    }, [selectedTransport, checkpointEntry, checkpointExit, selectedDirection]);

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
        const reportData = {
            border_crossing_id: id,
            transport_id: selectedTransport,
            user_id: 1,  // Adjust according to your user data
            checkpoint_queue: checkpointQueue,
            checkpoint_entry: checkpointEntry,
            checkpoint_exit: checkpointExit,
            comment: comment,
            is_flipped_direction: isFlippedDirection
        };

        ReportService.createReport(reportData)
            .then(response => {
                popup.open({title: "Успешно", message: "Отчет успешно создан", buttons: [{type: "default", text: "Ok"}]});


                // Переход обратно на страницу отчетов
                navigate(`/borderCrossing/${id}/reports`, {
                    state: {
                        directionCrossing: directionCrossing
                    }
                });

                console.log('Report created successfully:', response);
            })
            .catch(error => {

                popup.open({title: "Ошибка", message: "Произошла ошибка при создании отчета", buttons: [{type: "default", text: "Ok"}]});

                console.error('Error creating report:', error);
            });

        backButton.hide();
        mainButton.hide();
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
                    <div className="container">
                        <Section header={"Добавление отчета о прохождении границы"}>
                            <Text weight="1" className="choose-direction-title">
                                Выберите направление
                            </Text>

                            <form>
                                <Cell
                                    Component="label"
                                    before={<Radio name="radio" value={`false`} />}
                                    multiline
                                    onChange={() => setSelectedDirection('forward')}
                                >
                                    {`${directionCrossing.from_city.country.name} -> ${directionCrossing.to_city.country.name}`}
                                </Cell>

                                <Cell
                                    Component="label"
                                    before={<Radio name="radio" value={`true`} />}
                                    multiline
                                    onChange={() => setSelectedDirection('backward')}
                                >
                                    {`${directionCrossing.to_city.country.name} -> ${directionCrossing.from_city.country.name}`}
                                </Cell>
                            </form>

                            <Text weight="1" className="choose-direction-title">
                                Выберите тип пересечения границы
                            </Text>

                            <div className="transport-container">
                                <form className="transport-container">
                                    {transports.map((transport, index) => (
                                        <Cell
                                            key={index}
                                            Component="label"
                                            before={<Radio name="radio" value={`${transport.id}`} />}
                                            multiline
                                            onChange={(e) => setSelectedTransport(e.target.value)}
                                        >
                                            <img className="label-img-margin" src={`/${transport.icon}`} alt="" />
                                        </Cell>
                                    ))}
                                </form>
                            </div>

                            <Text weight="1" className="choose-direction-title">
                                Время подъезда к очереди на КПП (необязательно)
                            </Text>
                            <Input
                                name="checkpointQueue"
                                type="datetime-local"
                                value={checkpointQueue}
                                onChange={(e) => setCheckpointQueue(e.target.value)}
                            />

                            <Text weight="1" className="choose-direction-title">
                                Время въезда на первый КПП
                            </Text>
                            <Input
                                name="checkpointEntry"
                                type="datetime-local"
                                value={checkpointEntry}
                                onChange={(e) => setCheckpointEntry(e.target.value)}
                            />

                            <Text weight="1" className="choose-direction-title">
                                Время выезда с последнего КПП
                            </Text>
                            <Input
                                name="checkpointExit"
                                type="datetime-local"
                                value={checkpointExit}
                                onChange={(e) => setCheckpointExit(e.target.value)}
                            />

                            <Text weight="1" className="choose-direction-title">
                                Комментарий (необязательно)
                            </Text>
                            <Textarea
                                name="comment"
                                value={comment}
                                onChange={(e) => setComment(e.target.value)}
                            />
                        </Section>
                    </div>
                )}
            </div>
        </AppRoot>
    );
}
