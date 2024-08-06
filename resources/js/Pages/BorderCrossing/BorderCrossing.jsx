import './styles.css';
import { Image, Text } from "@telegram-apps/telegram-ui";
import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import { useState, useEffect } from "react";
import BorderCrossingService from "@/API/BorderCrossingService.js";
import {useParams} from "react-router-dom";

export default function BorderCrossing() {
    const [directionCrossings, setDirectionCrossings] = useState([]);

    const {directionId} = useParams();


    useEffect(() => {
        const directionIdNumber = Number(directionId);


        BorderCrossingService.getAllById(directionIdNumber).then((r) => {
            if (r instanceof Error) {

            } else {
                setDirectionCrossings(r);
            }
        }).catch((r) => {

        })
    }, [],);

    return (
        <div>
            <img
                src={`d}`}
                alt={"das"}
                className="header-image"
            />

            <div className="container">
                <Text weight="3" className={"header-text"}>
                    Направления
                </Text>

                {/*{directions.length > 0 ? (*/}
                {/*    directions.map((direction) => (*/}
                {/*        <DirectionCard*/}
                {/*            key={direction.id}*/}
                {/*            direction={direction.name}*/}
                {/*            pathImg={direction.logo}*/}
                {/*            onClick={() => console.log(`Click ${direction.id}`)}*/}
                {/*        />*/}
                {/*    ))*/}
                {/*) : (*/}
                {/*    <p>No directions available</p>*/}
                {/*)}*/}
            </div>
        </div>
    );
}
