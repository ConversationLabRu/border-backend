import './bootstrap';

import React from 'react';
import ReactDOM from 'react-dom/client'
import {AppRouting} from "@/Pages/AppRouting";
import {BrowserRouter} from "react-router-dom";
import {SDKProvider} from "@tma.js/sdk-react";

ReactDOM.createRoot(document.getElementById('app')).render(
    <SDKProvider debug acceptCustomStyles>
        <BrowserRouter>
            <AppRouting/>
        </BrowserRouter>
    </SDKProvider>
)


