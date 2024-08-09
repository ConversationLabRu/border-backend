import React from 'react';
import {
    createBrowserRouter, Route,
    RouterProvider, Routes,
} from 'react-router-dom';

import BorderCrossing from '@/Pages/BorderCrossing/BorderCrossing.jsx';
import Directions from '@/Pages/Directions/Directions.jsx';
import BorderCrossingInfo from "@/Pages/BorderCrossing/BorderCrossingInfo.jsx";
import ReportsPage from "@/Pages/BorderCrossing/Reports/ReportsPage.jsx";
import CreateReportPage from "@/Pages/BorderCrossing/Reports/CreateReportPage.jsx";

export const AppRouting = () => {
    return (
        <div>
            <Routes>
                <Route path="/" element={<Directions/>}/>
                <Route path="/borderCrossing/:id" element={<BorderCrossing/>}/>
                <Route path="/borderCrossing/info/:id" element={<BorderCrossingInfo/>}/>
                <Route path="/borderCrossing/:id/reports" element={<ReportsPage/>}/>
                <Route path="/borderCrossing/:id/reports/create" element={<CreateReportPage/>}/>
            </Routes>
        </div>
    );
};
