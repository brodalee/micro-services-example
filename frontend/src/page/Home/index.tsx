import {useQuery} from "../../hook/useQuery.tsx";
import {fetchProducts, Product} from "../../service/backend.api.tsx";
import {useEffect, useState} from "react";
import InfiniteScroll from "react-infinite-scroll-component";
import useBasket from "../../hook/useBasket.tsx";

export default () => {
    const [page, setPage] = useState(0)
    const limit = 20
    const [items, setItems] = useState<Product[]>([])
    const basket = useBasket()

    const {data, triggerAgain, isLoading, isSuccess} = useQuery(
        () => fetchProducts({page, limit})
    )

    useEffect(() => {
        if (!isLoading && isSuccess) {
            setItems([...items, ...data?.products!])
        }
    }, [isLoading, isSuccess]);

    return (
        <div className={'container'}>
            <div style={{marginTop: "5%"}}></div>

            {(!isLoading && isSuccess) && (
                <>
                    <InfiniteScroll
                        next={triggerAgain}
                        hasMore={data?.nextPage !== null}
                        loader={<>Loading ... </>}
                        dataLength={data?.totalCount!}>
                        <div className={'row'}>
                            {items.map((product, index) => (
                                <div key={index} className="card"
                                     style={{width: "18rem", marginRight: "20px", marginBottom: "10px"}}>
                                    <img src={product.imgUrl} className="card-img-top" alt="..."/>
                                    <div className="card-body">
                                        <h5 className="card-title">{product.brand} {product.designation} ({product.price / 100} €)</h5>
                                    </div>
                                    <ul className="list-group list-group-flush">
                                        <li className="list-group-item">{product.processor}</li>
                                        <li className="list-group-item">{product.resolution}</li>
                                        <li className="list-group-item">{product.ram}</li>
                                    </ul>
                                    <div className={'text-center'}>
                                        <button onClick={() => basket.addProduct(product.id)} className="btn btn-info">Ajouter au panier {(basket.hasProductInBasket(product.id) ? "( " +basket.quantity(product.id) + " )" : '')}</button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </InfiniteScroll>
                </>
            )}
        </div>
    )
}