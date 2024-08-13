import '../styles.css';
import '../border-info-styles.css';
import './reports-styles.css';
import '@telegram-apps/telegram-ui/dist/styles.css';

import {
    Accordion,
    AppRoot,
    Avatar,
    AvatarStack,
    Blockquote,
    Button,
    Cell, Input,
    List,
    Radio,
    Text, Textarea
} from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { ThreeDots } from "react-loader-spinner";
import { ServerURL } from "@/API/ServerConst.js";
import ReportService from "@/API/ReportService.js";
import { declensionHours, declensionMinutes } from "@/Pages/BorderCrossing/BorderCrossingInfo.jsx";
import {
    AccordionSummary
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionSummary/AccordionSummary.js";
import {
    AccordionContent
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionContent/AccordionContent.js";
import CommentAccordion from "@/Pages/BorderCrossing/Reports/CommentAccrodion.jsx";
import {useBackButton, useMainButton} from "@tma.js/sdk-react";
import RadioButton from "@/Pages/BorderCrossing/Reports/RadioButton.jsx";
import TransportService from "@/API/TransportService.js";

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

        if ( (selectedTransport !== "") && (checkpointEntry !== "") && (checkpointExit !== "") && (selectedDirection !== "")) {
            mainButton.setText("Отправить данные").setBgColor("#007aff").show().enable();
        } else {
            mainButton.setText("Заполните обязательные поля").setBgColor("#808080").show().disable();
        }

        console.log(selectedTransport)

    }, [selectedTransport, checkpointEntry, checkpointExit, selectedDirection])

    useEffect(() => {
        backButton.show()
    }, []);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/borderCrossing/${directionCrossing.id}/reports`,
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

    useEffect(() => {
        TransportService.getAll().then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setTransports(r);
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);

    const handleDirectionChange = (event) => {
        setSelectedDirection(event.target.value);

        if (event.target.value) {
            setIsFlippedDirection(true);
        } else {
            setIsFlippedDirection(false);

        }
    };

    const handleTransportChange = (event) => {
        setSelectedTransport(event.target.value);
    };

    const handleCheckpointQueueChange = (event) => {
        setCheckpointQueue(event.target.value);
    };

    const handleCheckpointEntryChange = (event) => {
        setCheckpointEntry(event.target.value);
    };

    const handleCheckpointExitChange = (event) => {
        setCheckpointExit(event.target.value);
    };

    const handleCommentChange = (event) => {
        setComment(event.target.value);
    };

    useEffect(() => {
        const handleMainButtonClick = () => {
            // Prepare data to send
            const reportData = {
                border_crossing_id: id,  // Adjust this if needed
                transport_id: selectedTransport,  // Map transport name to ID if necessary
                user_id: 1,  // Adjust according to your user data
                checkpoint_queue: checkpointQueue,
                checkpoint_entry: checkpointEntry,
                checkpoint_exit: checkpointExit,
                comment: comment,
                is_flipped_direction: isFlippedDirection

            };

            console.log(reportData)

            // Send data to API
            ReportService.createReport(reportData).then(response => {
                navigate(`/borderCrossing/${id}/reports`,
                    {
                        state: {
                            directionCrossing: directionCrossing
                        }
                    }
                )
                console.log('Report created successfully:', response);
            }).catch(error => {
                // Handle error
                console.error('Error creating report:', error);
            });

            backButton.hide();
            mainButton.hide();
        };

        mainButton.on("click", handleMainButtonClick);

        return () => {
            mainButton.off("click", handleMainButtonClick);
        }
    }, [mainButton, selectedTransport, checkpointEntry, checkpointExit, selectedDirection]);

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
                    <div className="container">
                        <Text weight={"3"} className={"title"}>
                            {`Добавление отчета о прохождении границы`}
                        </Text>

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Выберите направление
                        </Text>

                        <form>

                            <Cell
                                Component="label"
                                before={<Radio name="radio" value={`false`}/>}
                                multiline
                                onChange={handleDirectionChange}
                            >
                                {`${directionCrossing.from_city.country.name} -> ${directionCrossing.to_city.country.name}`}
                            </Cell>

                            <Cell
                                Component="label"
                                before={<Radio name="radio" value={`true`}/>}
                                multiline
                                onChange={handleDirectionChange}
                            >
                                {`${directionCrossing.to_city.country.name} -> ${directionCrossing.from_city.country.name}`}
                            </Cell>
                        </form>

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Выберите тип пересечения границы
                        </Text>

                        <div className={"transport-container"}>
                            <form className={"transport-container"}>
                                {transports && transports.map((transport, index) => (
                                    <dv key={index}>
                                        <Cell
                                            Component="label"
                                            before={<Radio name="radio" value={`${transport.id}`}/>}
                                            multiline
                                            onChange={handleTransportChange}
                                        >
                                            <img
                                                className={"label-img-margin"}
                                                src={`${ServerURL.URL_STATIC}/${transport.icon}`}
                                                alt=""
                                            />
                                        </Cell>
                                    </dv>
                                ))}
                            </form>
                        </div>

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Выберите время подъезда к очереди на КПП, если таковая была (необязательно)
                        </Text>

                        <Input
                            name="checkpointQueue"
                            type="datetime-local"
                            value={checkpointQueue}
                            onChange={handleCheckpointQueueChange}
                        />

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Выберите время въезда на первый КПП
                        </Text>

                        <Input
                            name="checkpointEntry"
                            type="datetime-local"
                            value={checkpointEntry}
                            onChange={handleCheckpointEntryChange}
                        />

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Выберите время выезда с последнего КПП
                        </Text>

                        <Input
                            name="checkpointExit"
                            type="datetime-local"
                            value={checkpointExit}
                            onChange={handleCheckpointExitChange}
                        />

                        <hr/>

                        <Text weight="1" className={"choose-direction-title"}>
                            Напишите комментарий (необязательно)
                        </Text>

                        <Textarea
                            name="comment"
                            value={comment}
                            onChange={handleCommentChange}
                        />

                    </div>
                )}
            </div>
        </AppRoot>
    );
}
