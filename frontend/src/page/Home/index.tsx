import {useQuery} from "../../hook/useQuery.tsx";
import {fetchProducts} from "../../service/backend.api.tsx";
import {useState} from "react";

export default () => {
    const [page, setPage] = useState(0)
    const limit = 20

    const {data, triggerAgain, isLoading, isSuccess} = useQuery(
        () => fetchProducts({page, limit})
    )


    return (
        <></>
    )
}