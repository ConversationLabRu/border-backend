import React from 'react';
import {
    createBrowserRouter, Route,
    RouterProvider, Routes,
} from 'react-router-dom';

import BorderCrossing from '@/Pages/BorderCrossing/BorderCrossing.jsx';
import Directions from '@/Pages/Directions/Directions.jsx';

export const AppRouting = () => {
    return (
        <div>
            <Routes>
                <Route path="/" element={<Directions/>}/>
                <Route path="/borderCrossing/:id" element={<BorderCrossing/>}/>
            </Routes>
        </div>
    );
};
