import React, {createContext, useEffect, useState} from "react";
import {useLazyQuery} from "../hook/useLazyQuery.tsx";
import {fetchNotifications, markNotificationAsSeen} from "../service/backend.api.tsx";
import {useRecoilValue} from "recoil";
import {userAtom} from "../store/userAtom.tsx";
import {toast} from "react-toastify";

type NotificationContext = {
    refresh: () => void
    notifications: []
}

export const notificationContext = createContext<NotificationContext>({
    refresh: () => {},
    notifications: []
})

type Props = {
    children: React.ReactNode
}

export const NotificationProvider = ({children}: Props) => {
    const [notifications, setNotifications] = useState([])
    const {call, isSuccess, isLoading, data, hasLoaded} = useLazyQuery(
        () => fetchNotifications()
    )
    const auth = useRecoilValue(userAtom)
    const refresh = () => {
        if (auth) {
            call()
        }
    }

    useEffect(() => {
        if (!isLoading && hasLoaded && isSuccess) {
            setNotifications(data)
        }
    }, [isLoading, hasLoaded, isSuccess]);

    useEffect(() => {
        const interval = setInterval(() => {
            refresh()
        }, 30000)

        return () => clearInterval(interval)
    });

    useEffect(() => {
        if (notifications.length > 0) {
            notifications.forEach(n => {
                toast(n.message, {type: 'warning'})
                markNotificationAsSeen(n.id)
            })
        }
    }, [notifications]);

    return (
        <notificationContext.Provider value={{refresh, notifications}}>
            {children}
        </notificationContext.Provider>
    )
}