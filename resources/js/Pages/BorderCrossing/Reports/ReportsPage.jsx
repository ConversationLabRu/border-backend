import '../styles.css';
import '../border-info-styles.css';
import './reports-styles.css';
import {
    Accordion,
    AppRoot,
    Avatar,
    AvatarStack, Badge,
    Blockquote,
    Button, Info,
    List,
    Section,
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
import {declensionHours, declensionMinutes} from "@/Pages/BorderCrossing/BorderCrossingInfo.jsx";
import {
    AccordionSummary
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionSummary/AccordionSummary.js";
import {
    AccordionContent
} from "@telegram-apps/telegram-ui/dist/components/Blocks/Accordion/components/AccordionContent/AccordionContent.js";
import CommentAccordion from "@/Pages/BorderCrossing/Reports/CommentAccrodion.jsx";
import {useBackButton, useMainButton} from "@tma.js/sdk-react";
import {IoCar} from "react-icons/io5";

export default function ReportsPage() {
    const location = useLocation();


    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;

    const backButton = useBackButton();
    const mainButton = useMainButton();


    useEffect(() => {
        backButton.show()
        mainButton.setText("Создать отчет").setBgColor("#007aff").show().enable();
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


    // const mainButton = useMainButton();

    // useEffect(() => {
    //     mainButton.setText("Добавить отчет").setBgColor("#007aff").show().enable();
    // })

    const navigate = useNavigate();



    useEffect(() => {
        const directionIdNumber = Number(id);

        ReportService.getAll(directionIdNumber).then((r) => {
            if (r instanceof Error) {
                // Handle error
            } else {
                setReports(r);
                console.log(directionCrossing)
            }
        }).catch((r) => {
            // Handle error
        });
    }, [id]);


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
                            <Section header={`${directionCrossing.from_city.name} - ${directionCrossing.to_city.name}`}>
                                <Section header={"Отчёты о прохождении"}>
                                    {reports.map((report, index) => {

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

                                        const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                        const formatterTime = new Intl.DateTimeFormat('ru-RU', optionsTime);

                                        formattedDateEntry = formatter.format(entryTime);
                                        formattedTimeEntry = formatterTime.format(entryTime)

                                        formattedDateExit = formatter.format(exitTime);
                                        formattedTimeExit = formatterTime.format(exitTime)

                                        let differenceInMs = exitTime - entryTime;

                                        if (report.checkpoint_queue !== null) {
                                            const queueTime = new Date(report.checkpoint_queue);

                                            // Форматируем дату с учетом временной зоны пользователя
                                            const formatter = new Intl.DateTimeFormat('ru-RU', options);
                                            const formatterTime = new Intl.DateTimeFormat('ru-RU', optionsTime);
                                            formattedDate = formatter.format(queueTime);
                                            formattedTime = formatterTime.format(queueTime)

                                            differenceInMs = exitTime - queueTime
                                        }

                                        // Вычисляем разницу в миллисекундах
                                        // Преобразуем миллисекунды в часы и минуты
                                        const differenceInMinutes = Math.floor(differenceInMs / 60000);
                                        const hours = Math.floor(differenceInMinutes / 60);
                                        const minutes = differenceInMinutes % 60;

                                        // Форматируем разницу во времени
                                        const timeDifference = `${declensionHours(hours)} ${declensionMinutes(minutes)}`;

                                        return (
                                            <div className={"report-container"}>
                                                <div key={index} className={"title-crossing-container"}>
                                                    <div className={"border-info-container"}>
                                                        <AvatarStack className={"avatar-stack-report"}>
                                                            <React.Fragment>
                                                                {report.is_flipped_direction ? (
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
                                                            {timeDifference}
                                                        </Text>
                                                    </div>

                                                    <IoCar
                                                        size={28}
                                                    />
                                                </div>

                                                {report.checkpoint_queue !== null && (
                                                    <div>
                                                        <div className={"time-desc-container"}>
                                                            <Text weight="3">
                                                                {`Очередь на КПП:`}
                                                            </Text>

                                                            <Text weight="3">
                                                                {formattedDate} в {formattedTime}
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
                                                        {/*<CommentAccordion title="Комментарий">*/}
                                                        {/*    {report.comment}*/}
                                                        {/*</CommentAccordion>*/}

                                                        <Accordion
                                                            onChange={() => toggleAccordion(index)}
                                                            expanded={expandedIndexes.includes(index)}
                                                        >
                                                            <AccordionSummary>Комментарий</AccordionSummary>
                                                            <AccordionContent>
                                                                <Text>{report.comment}</Text>
                                                            </AccordionContent>
                                                        </Accordion>
                                                    </>
                                                )}
                                            </div>
                                        )
                                    })}
                                </Section>
                            </Section>

                        </div>
                )}

                {/*<div className="footer">*/}
                {/*    <Button mode="filled" size="l"*/}
                {/*    onClick={() => {navigate(`/borderCrossing/${id}/reports/create`,*/}
                {/*        {*/}
                {/*            state: {*/}
                {/*                directionCrossing: directionCrossing*/}
                {/*            }*/}
                {/*        }*/}
                {/*    );}}>*/}
                {/*        Action*/}
                {/*    </Button>*/}
                {/*</div>*/}
            </div>
        </AppRoot>
    );
}
