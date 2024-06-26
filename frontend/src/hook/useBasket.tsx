import {useContext, useEffect, useState} from "react";
import {BasketContext, basketDefaultValue} from "../context/basketContext.tsx";
import {useLazyQuery} from "./useLazyQuery.tsx";
import {addProductInBasket} from "../service/backend.api.tsx";

export default () => {
    const basket = useContext<basketDefaultValue>(BasketContext)

    const addProduct = addProductHook(basket)

    const countProduct = () => {
        return basket.products.map(p => p.quantity)
            .reduce((a, b) => a + b, 0)
    }

    const hasProductInBasket = (productId: string) => {
        return basket.products.find(p => p.id === productId) !== undefined
    }

    const quantity = (productId: string) => {
        return basket.products.find(p => p.id === productId).quantity
    }

    return {addProduct, count: countProduct(), hasProductInBasket, quantity}
}

const addProductHook = (basket: basketDefaultValue) => {
    const [productId, setProductId] = useState<string|null>(null)
    const {call, hasLoaded, isLoading, isSuccess} = useLazyQuery(
        () => addProductInBasket(productId!)
    )

    useEffect(() => {
        if (productId !== null) {
            call()
        }
    }, [productId]);

    useEffect(() => {
        if (hasLoaded && !isLoading && isSuccess) {
            setProductId(null)
            basket.refresh()
        }
    }, [isSuccess, isLoading, hasLoaded]);

    return (productId: string) => {
        setProductId(productId)
    }
}