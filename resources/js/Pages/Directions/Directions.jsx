import './styles.css';
import {AppRoot, Avatar, Button, Cell, Image, List, Section, Text} from "@telegram-apps/telegram-ui";
// import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import {useNavigate} from "react-router-dom";
import {DirectionCard} from "@/Pages/Directions/Components/card.jsx";
import {ServerURL} from "@/API/ServerConst.js";
import {Icon20Copy} from "@telegram-apps/telegram-ui/dist/icons/20/copy.js";
import {ThreeDots} from "react-loader-spinner";
import {useTWAEvent} from "@tonsolutions/telemetree-react";
import LogService from "@/API/LogService.js";

export default function Directions() {
    const [directions, setDirections] = useState([]);
    const eventBuilder = useTWAEvent();

    const navigate = useNavigate();

    useEffect(() => {

        DirectionService.getAll().then((r) => {
            if (r instanceof Error) {

            } else {
                setDirections(r);
            }
        }).catch((r) => {

        })
    }, [],);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppRoot>
            <div>

                {directions.length !== 0 ? (
                    <>
                        <img
                            src="/bordersmainnew2.png"
                            alt={"das"}
                            className="header-image-direction"
                        />
                        <List>
                            <Section header="Направления">
                                {directions.map((direction) => (
                                    <Cell
                                        before={<Avatar size={48} src={`${direction.logo}`}/>}
                                        subtitle="Информация, камера, правила и др."
                                        onClick={() => {

                                            eventBuilder.track('Button Clicked', {
                                                label: 'Subscribe Button', // Additional info about the button
                                                category: 'User Engagement', // Categorize the event
                                            });

                                            navigate(`/borderCrossing/${direction.id}`,
                                                {
                                                    state: {
                                                        direction: direction
                                                    }
                                                });
                                        }}

                                    >
                                        {direction.name}
                                    </Cell>
                                ))}
                            </Section>

                            <div className="bottom-button-container"
                                 onClick={() => {
                                     navigate(`/aboutProject`)
                                     LogService.sendLog("Перешел на страницу \"О проекте\"")
                                 }
                            }>
                                <Button
                                    mode="filled"
                                    size="s"
                                >
                                    О проекте
                                </Button>
                            </div>
                        </List>
                    </>
                ) : (
                    <>
                        <div className="loader-overlay">
                            <ThreeDots height="80" width="80" color="#007aff" ariaLabel="loading"/>
                        </div>
                    </>
                )}

            </div>
        </AppRoot>
    );
}
