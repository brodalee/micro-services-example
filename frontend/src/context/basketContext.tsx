import {createContext, useEffect, useState} from "react";
import {useQuery} from "../hook/useQuery.tsx";
import {fetchBasket, FetchBasketResponse} from "../service/backend.api.tsx";

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
    const {data, isSuccess, isLoading, triggerAgain} = useQuery<FetchBasketResponse[]>(
        () => fetchBasket()
    )

    const [products, setProducts] = useState<FetchBasketResponse[]>([])

    useEffect(() => {
        if (!isLoading && isSuccess) {
            setProducts(data)
        }
    }, [isLoading, isSuccess]);
    const refresh = () => triggerAgain()

    useEffect(() => {
        const interval = setInterval(() => {
            refresh()
        }, 10000)

        return () => clearInterval(interval)
    });

    return (
        <BasketContext.Provider value={{refresh, products}}>
            {children}
        </BasketContext.Provider>
    )
}