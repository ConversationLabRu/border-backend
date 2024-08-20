import React, { useState, useEffect, useRef } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { Section, Text } from "@telegram-apps/telegram-ui";
import { useBackButton } from "@tma.js/sdk-react";
import CameraService from "@/API/CameraService.js";
import './styles.css';

export default function CamerasPage() {
    const [cameras, setCameras] = useState([]);
    const navigate = useNavigate();
    const location = useLocation();
    const directionCrossing = location.state?.directionCrossing;
    const direction = location.state?.direction;
    const backButton = useBackButton();
    const videoRefs = useRef({});

    useEffect(() => {
        backButton.show();
        return () => backButton.hide();
    }, [backButton]);

    useEffect(() => {
        const handleBackButtonClick = () => {
            navigate(`/borderCrossing/info/${directionCrossing.id}`, {
                state: {
                    direction: direction,
                    directionCrossing: directionCrossing
                }
            });
            backButton.hide();
        };

        backButton.on("click", handleBackButtonClick);
        return () => backButton.off("click", handleBackButtonClick);
    }, [backButton, navigate, direction, directionCrossing]);

    useEffect(() => {
        CameraService.getAllByDirectionId(directionCrossing.id)
            .then((r) => {
                if (!(r instanceof Error)) {
                    setCameras(r);
                }
            })
            .catch((error) => {
                console.error(error);
            });
    }, [directionCrossing.id]);

    useEffect(() => {
        const loadHlsScript = () => {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = "https://media.kgd.ru/hls/hls.min.js";
                script.onload = () => resolve();
                script.onerror = () => reject(new Error("Failed to load HLS.js"));
                document.head.appendChild(script);
            });
        };

        const initializeHls = () => {
            if (window.Hls) {
                cameras.forEach((camera) => {
                    if (videoRefs.current[camera.id] && Hls.isSupported()) {
                        const hls = new Hls();
                        const video = videoRefs.current[camera.id];
                        hls.loadSource(`https://media.kgd.ru:11936/${camera.url}`);
                        hls.attachMedia(video);
                        hls.on(Hls.Events.MANIFEST_PARSED, () => {
                            video.play();
                        });
                    } else if (videoRefs.current[camera.id] && videoRefs.current[camera.id].canPlayType("application/vnd.apple.mpegurl")) {
                        videoRefs.current[camera.id].src = `https://media.kgd.ru:11936/${camera.url}`;
                        videoRefs.current[camera.id].addEventListener("canplay", () => {
                            videoRefs.current[camera.id].play();
                        });
                    }
                });
            } else {
                console.error("Hls.js is not available");
            }
        };

        loadHlsScript()
            .then(() => {
                initializeHls();
            })
            .catch((error) => {
                console.error("Error loading HLS.js:", error);
            });

        return () => {
            // Clean up HLS instances if necessary
            if (window.Hls) {
                Object.values(videoRefs.current).forEach((video) => {
                    if (video && Hls.instances) {
                        Hls.instances.forEach((hls) => {
                            if (hls.media === video) {
                                hls.destroy();
                            }
                        });
                    }
                });
            }
        };
    }, [cameras]);

    const openUrlInNewTab = (url) => {
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <div>
            <div className="container">
                <Section header={`Камеры ${directionCrossing.from_city.name} - ${directionCrossing.to_city.name}`}>
                    {cameras.length !== 0 ? (
                        cameras.map((camera) => (
                            <div
                                key={camera.id}
                                className="card-camera"
                            >
                                {camera.url.substring(0, 3) === "pko" ? (
                                    <div className="video-container">
                                        <video
                                            ref={(el) => (videoRefs.current[camera.id] = el)}
                                            controls
                                            muted
                                            autoPlay
                                            playsInline
                                            style={{margin: '10px 0', width: '100%', aspectRatio: '16/9'}}
                                        />
                                    </div>
                                ) : (camera.url.substring(0, 6) === "webcam") ? (
                                    <div>
                                        <img className="header-image-camera" src={`https://customs.gov.by/${camera.url}`} alt={""}/>
                                    </div>
                                ) : (
                                    <div onClick={() => openUrlInNewTab(camera.url)}>
                                        <img
                                            src={`/${camera.photo}`}
                                                alt={"header"}
                                                className="header-image-camera cameraPic"
                                                />
                                    </div>
                                )}
                                <Text className="desc-card" onClick={() => {console.log(camera.url.substring(0, 6))}}>
                                    {camera.description}
                                </Text>
                            </div>
                        ))
                    ) : (
                        <p>No directions available</p>
                    )}
                </Section>
            </div>
        </div>
    );
}
