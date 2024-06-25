import {RecoilRoot} from 'recoil';
import {createBrowserRouter, RouterProvider} from "react-router-dom";
import Login from "./page/Login";
import Home from "./page/Home";
import ProtectedRoute from "./component/ProtectedRoute";

function App() {
  return (
    <RecoilRoot>
      <RouterProvider router={router} />
    </RecoilRoot>
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
