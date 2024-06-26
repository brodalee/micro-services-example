import {RecoilRoot, useRecoilValue} from 'recoil';
import {createBrowserRouter, RouterProvider} from "react-router-dom";
import Login from "./page/Login";
import Home from "./page/Home";
import ProtectedRoute from "./component/ProtectedRoute";
import {userAtom} from "./store/userAtom.tsx";
import {httpClient} from "./service/backend.api.tsx";
import {Provider} from "./context/basketContext.tsx";
import {NotificationProvider} from "./context/notificationContext.tsx";
import {ToastContainer} from "react-toastify";
import 'react-toastify/dist/ReactToastify.css';
import Basket from "./page/Basket";
import Account from "./page/Account";

function App() {
    return (
        <RecoilRoot>
            <SetupApp/>
        </RecoilRoot>
    )
}

const SetupApp = () => {
    const userState = useRecoilValue(userAtom)
    if (userState) {
        httpClient.defaults.headers.common['Authorization'] = 'Bearer ' + userState.token
    }

    return (
        <>
            <Provider>
                <NotificationProvider>
                    <RouterProvider router={router}/>
                </NotificationProvider>
            </Provider>
            <ToastContainer/>
        </>
    )
}

const router = createBrowserRouter([
    {
        path: '/login',
        element: <Login/>,
    },
    {
        path: '/',
        element: <ProtectedRoute/>,
        children: [
            {
                path: '',
                element: <Home/>
            },
            {
                path: '/basket',
                element: <Basket />
            },
            {
                path: '/account',
                element: <Account />
            }
        ]
    }
])

export default App
