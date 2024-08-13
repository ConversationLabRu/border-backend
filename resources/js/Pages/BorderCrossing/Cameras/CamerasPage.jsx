import { Image, Text } from "@telegram-apps/telegram-ui";
// import { DirectionCard } from "@/Pages/Directions/Components/card.jsx";
import { useFetching } from "@/hooks/useFetching.js";
import DirectionService from "@/API/DirectionService.js";
import React, { useState, useEffect } from "react";
import {useLocation, useNavigate} from "react-router-dom";
import {DirectionCard} from "@/Pages/Directions/Components/card.jsx";
import CameraService from "@/API/CameraService.js";
import {ServerURL} from "@/API/ServerConst.js";

import './styles.css'
import {useBackButton} from "@tma.js/sdk-react";

export default function CamerasPage() {
    const [cameras, setCameras] = useState([]);

    const navigate = useNavigate();

    const location = useLocation();

    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;

    const backButton = useBackButton();

    useEffect(() => {
        backButton.show()
    }, []);

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
        };

        backButton.on("click", handleBackButtonClick);

        return () => {
            backButton.off("click", handleBackButtonClick);
        }
    }, [backButton]);

    useEffect(() => {
        console.log(directionCrossing.id)

        CameraService.getAllByDirectionId(directionCrossing.id).then((r) => {
            if (r instanceof Error) {

            } else {
                console.log(directionCrossing)
                setCameras(r);
            }
        }).catch((r) => {

        })
    }, [],);

    const openUrlInNewTab = (url) => {
        if (url) {
            console.log(url)
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <div>
            <div className="container">
                <Text weight="3" className={"header-text"}>
                    {`Камеры ${directionCrossing.from_city.name} - ${directionCrossing.to_city.name}`}
                </Text>

                {cameras.length > 0 ? (
                    cameras.map((camera) => (
                        <div className={"card-camera"} onClick={() => openUrlInNewTab(camera.url)}>
                            {camera.photo !== undefined && (
                                <img
                                    src={`${ServerURL.URL_STATIC}/${camera.photo}`}
                                    alt={"header"}
                                    className="header-image-camera cameraPic"
                                />
                            )}
                            <Text className={"desc-card"}>
                                {camera.description}
                            </Text>
                        </div>
                    ))
                ) : (
                    <p>No directions available</p>
                )}
            </div>
        </div>
    );
}
