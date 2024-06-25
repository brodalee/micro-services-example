import {useEffect, useState} from "react";

export function useQuery<T>(callback: () => Promise<T>) {
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [data, setData] = useState<T|null>(null)
    const [error, setError] = useState<any>(null)
    const [trigger, setTriggerAgain] = useState(true)

    useEffect(() => {
        // @ts-ignore
        (async () => {
            try {
                setError(null)
                setIsLoading(true)
                setData(await callback())
            } catch (e) {
                setError(e)
            } finally {
                setIsLoading(false)
            }
        })()
    }, [trigger]);

    const triggerAgain = () => setTriggerAgain(!trigger)

    return {isLoading, data, error, isSuccess: !isLoading && error === null, triggerAgain}
}