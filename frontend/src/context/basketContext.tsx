import {createContext, useEffect, useState} from "react";
import {useQuery} from "../hook/useQuery.tsx";
import {fetchBasket, FetchBasketResponse} from "../service/backend.api.tsx";
import {useRecoilValue} from "recoil";
import {userAtom} from "../store/userAtom.tsx";
import {useLazyQuery} from "../hook/useLazyQuery.tsx";

export type basketDefaultValue = {
    refresh: Function
    products: FetchBasketResponse[]
}
export const BasketContext = createContext<basketDefaultValue>({
    refresh: () => {},
    products: []
})

type Props = {
    children: React.ReactNode
}

export const Provider = ({children}: Props) => {
    const {data, isSuccess, isLoading, call} = useLazyQuery<FetchBasketResponse[]>(
        () => fetchBasket()
    )
    const auth = useRecoilValue(userAtom)

    const [products, setProducts] = useState<FetchBasketResponse[]>([])

    useEffect(() => {
        if (!isLoading && isSuccess) {
            setProducts(data)
        }
    }, [isLoading, isSuccess]);
    const refresh = () => {
        if (auth) {
            call()
        }
    }

    useEffect(() => {
        const interval = setInterval(() => {
            //refresh()
        }, 15000)

        return () => clearInterval(interval)
    });

    return (
        <BasketContext.Provider value={{refresh, products}}>
            {children}
        </BasketContext.Provider>
    )
}