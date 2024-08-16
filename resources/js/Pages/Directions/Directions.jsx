import './styles.css';
import {AppRoot, Avatar, Cell, Image, List, Section, Text} from "@telegram-apps/telegram-ui";
// import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import { useState, useEffect } from "react";
import {useNavigate} from "react-router-dom";
import {DirectionCard} from "@/Pages/Directions/Components/card.jsx";
import {ServerURL} from "@/API/ServerConst.js";

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

    return (
        <AppRoot>
            <div>
                <img
                    src="/bordersmain.jpg"
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
                                    onClick={() => {navigate(`/borderCrossing/${direction.id}`,
                                        {
                                            state: {
                                                direction: direction
                                            }
                                        });}}

                                >
                                    {direction.name}
                                </Cell>
                            ))
                        ) : (
                            <p>No directions available</p>
                        )}
                    </Section>
                </List>

                {/*<div className="container">*/}
                {/*    /!*<Text weight="3" className={"header-text"}>*!/*/}
                {/*    /!*    Направления*!/*/}
                {/*    /!*</Text>*!/*/}

                {/*    */}
                {/*</div>*/}
            </div>
        </AppRoot>
    );
}
