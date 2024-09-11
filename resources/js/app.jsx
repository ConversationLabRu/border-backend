import './bootstrap';
import './Pages/styles.css'

import React from 'react';
import ReactDOM from 'react-dom/client'
import {AppRouting} from "@/Pages/AppRouting";
import {BrowserRouter} from "react-router-dom";
import {SDKProvider} from "@tma.js/sdk-react";
import { TwaAnalyticsProvider } from '@tonsolutions/telemetree-react';


ReactDOM.createRoot(document.getElementById('app')).render(
    <TwaAnalyticsProvider
        projectId='2e26843c-c1c0-4e36-bfd4-41ea5d2aecdd'
        apiKey='b8730ede-7f5b-4f31-9772-55a4a3f26e3d'
        appName='GRANICA_APP'
    >
        <SDKProvider debug acceptCustomStyles>
            <BrowserRouter>
                <AppRouting/>
            </BrowserRouter>
        </SDKProvider>
    </TwaAnalyticsProvider>
)


