import './styles.css';
import { Image, Text } from "@telegram-apps/telegram-ui";
// import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import { useState, useEffect } from "react";
import {useNavigate} from "react-router-dom";

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
        <div>
            <img
                src="/bordersmain.jpg"
                alt={"das"}
                className="header-image"
            />

            <div className="container">
                <Text weight="3" className={"header-text"}>
                    Направления
                </Text>

                {directions.length > 0 ? (
                    directions.map((direction) => (
                        <DirectionCard
                            key={direction.id}
                            direction={direction.name}
                            pathImg={direction.logo}
                            onClick={() => {navigate(`/borderCrossing/${id}`);}}
                        />
                    ))
                ) : (
                    <p>No directions available</p>
                )}
            </div>
        </div>
    );
}
