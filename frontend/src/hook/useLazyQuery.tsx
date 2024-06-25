import {useState} from "react";

export function useLazyQuery<T>(callback: () => Promise<T>) {
    const [hasLoaded, setHasLoaded] = useState<boolean>(false)
    const [isLoading, setIsLoading] = useState<boolean>(false)
    const [data, setData] = useState<T|null>(null)
    const [error, setError] = useState<any>(null)

    // @ts-ignore
    const call = async () => {
        setIsLoading(true)
        setHasLoaded(false)
        try {
            setData(await callback())
        } catch (e) {
            setError(e)
        } finally {
            setIsLoading(false)
            setHasLoaded(true)
        }
    }

    return {hasLoaded, isLoading, data, error, isSuccess: !isLoading && error === null && hasLoaded, call}
}