import React from 'react';
import {
    createBrowserRouter,
    RouterProvider,
} from 'react-router-dom';

import BorderCrossing from '@/Pages/BorderCrossing/BorderCrossing.jsx';
import Directions from '@/Pages/Directions/Directions.jsx';

const router = createBrowserRouter([
    {
        path: "borderCrossing",
        element: <BorderCrossing />,
    },
    {
        path: "/",
        element: <Directions />,
    },
]);

export const AppRouting = () => {
    return (
        <RouterProvider router={router} />
    );
};
