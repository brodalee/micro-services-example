import React from "react";
import {useRecoilValue} from "recoil";
import {userAtom} from "../../store/userAtom.tsx";
import {Navigate, Outlet} from "react-router-dom";

type Props = {
    children: React.ReactNode
}

export default ({children}: Props) => {
    const user = useRecoilValue(userAtom)
    if (!user) {
        return <Navigate to={'/login'} />
    }

    return (
        <Outlet />
    )
}