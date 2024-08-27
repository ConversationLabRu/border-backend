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

export default function Directions() {
    const [directions, setDirections] = useState([]);

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
                <img
                    src="/bordersmainnew.png"
                    alt={"das"}
                    className="header-image"
                />

                <List>
                    <Section header="Направления">
                        {directions.length > 0 ? (
                            directions.map((direction) => (
                                <Cell
                                    before={<Avatar size={48} src={`${direction.logo}`}/>}
                                    subtitle="Информация, камера, правила и др."
                                    onClick={() => {
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
                            ))
                        ) : (
                            <p>No directions available</p>
                        )}
                    </Section>

                    <div className="bottom-button-container" onClick={() => openUrlInNewTab("https://t.me/conversationlab")}>
                        <Button
                            before={
                                <Avatar size={24} src={"/images/logo-company.jpg"}/>
                            }
                            mode="filled"
                            size="s"
                        >
                            Связаться с разработчиками
                        </Button>
                    </div>
                </List>

            </div>
        </AppRoot>
    );
}
