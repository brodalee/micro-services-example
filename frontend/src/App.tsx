import {RecoilRoot, useRecoilValue} from 'recoil';
import {createBrowserRouter, RouterProvider} from "react-router-dom";
import Login from "./page/Login";
import Home from "./page/Home";
import ProtectedRoute from "./component/ProtectedRoute";
import {userAtom} from "./store/userAtom.tsx";
import {httpClient} from "./service/backend.api.tsx";

function App() {
  return (
    <RecoilRoot>
      <SetupApp />
    </RecoilRoot>
  )
}

const SetupApp = () => {
  const userState = useRecoilValue(userAtom)
  if (userState) {
    httpClient.defaults.headers.common['Authorization'] = 'Bearer ' + userState.token
  }

  return (
      <RouterProvider router={router} />
  )
}

const router = createBrowserRouter([
  {
    path: '/login',
    element: <Login />,
  },
  {
    path: '/',
    element: <ProtectedRoute />,
    children: [
      {
        path: '',
        element: <Home />
      }
    ]
  }
])

export default App
