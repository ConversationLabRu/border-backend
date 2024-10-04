import React, {useEffect} from 'react';
import {
    createBrowserRouter, Route,
    RouterProvider, Routes,
} from 'react-router-dom';

import BorderCrossing from '@/Pages/BorderCrossing/BorderCrossing.jsx';
import Directions from '@/Pages/Directions/Directions.jsx';
import BorderCrossingInfo from "@/Pages/BorderCrossing/BorderCrossingInfo.jsx";
import ReportsPage from "@/Pages/BorderCrossing/Reports/ReportsPage.jsx";
import CreateReportPage from "@/Pages/BorderCrossing/Reports/CreateReportPage.jsx";
import CamerasPage from "@/Pages/BorderCrossing/Cameras/CamerasPage.jsx";
import InformationPage from "@/Pages/BorderCrossing/Information/InformationPage.jsx";
import {
    bindMiniAppCSSVars, bindThemeParamsCSSVars, bindViewportCSSVars,
    useHapticFeedback,
    useLaunchParams,
    useMiniApp,
    useThemeParams,
    useViewport
} from "@tma.js/sdk-react";
import {AppRoot} from "@telegram-apps/telegram-ui";
import AboutProject from "@/Pages/Directions/AboutProject/AboutProject.jsx";

export const AppRouting = () => {

    const lp = useLaunchParams();
    const miniApp = useMiniApp();
    const themeParams = useThemeParams();
    const viewport = useViewport();
    const haptic = useHapticFeedback();

    useEffect(() => {
        return bindMiniAppCSSVars(miniApp, themeParams);
    }, [miniApp, themeParams]);

    useEffect(() => {
        return bindThemeParamsCSSVars(themeParams);
    }, [themeParams]);

    useEffect(() => {
        return viewport && bindViewportCSSVars(viewport);
    }, [viewport]);

    return (
        <AppRoot
            appearance={miniApp.isDark ? 'dark' : 'light'}
            platform={['macos', 'ios'].includes(lp.platform) ? 'ios' : 'base'}
        >
            <div>
                <Routes>
                    <Route path="/" element={<Directions/>}/>
                    <Route path="/aboutProject" element={<AboutProject/>}/>
                    <Route path="/borderCrossing/:id" element={<BorderCrossing/>}/>
                    <Route path="/borderCrossing/info/:id" element={<BorderCrossingInfo/>}/>
                    <Route path="/borderCrossing/:id/info" element={<InformationPage/>}/>
                    <Route path="/borderCrossing/:id/cameras" element={<CamerasPage/>}/>
                    <Route path="/borderCrossing/:id/reports" element={<ReportsPage/>}/>
                    <Route path="/borderCrossing/:id/reports/create" element={<CreateReportPage/>}/>
                </Routes>
            </div>
        </AppRoot>
    );
};
