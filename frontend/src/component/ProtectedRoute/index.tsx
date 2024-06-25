import React from "react";
import {useRecoilValue} from "recoil";
import {userAtom} from "../../store/userAtom.tsx";
import {Navigate, Outlet} from "react-router-dom";
import NavBar from "../NavBar";

type Props = {
    children: React.ReactNode
}

export default ({children}: Props) => {
    const user = useRecoilValue(userAtom)
    if (!user) {
        return <Navigate to={'/login'} />
    }

    return (
        <>
            <NavBar />
            <Outlet />
        </>
    )
}