import {useQuery} from "../../hook/useQuery.tsx";
import {fetchBasket} from "../../service/backend.api.tsx";

export default () => {
    const {data, isLoading, isSuccess} = useQuery(
        () => fetchBasket()
    )

    return (
        <div className={'container'} style={{marginTop: '5%'}}>
            {(!isLoading && isSuccess) && (
                <>
                    <table className="table">
                        <thead>
                        <tr>
                            <th scope="col">Désignation</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">Prix unitaire (€)</th>
                            <th scope="col">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        {data?.map(b => (
                            <tr>
                                <td>{b.name}</td>
                                <td>{b.quantity}</td>
                                <td>{b.price / 100} €</td>
                                <td>{(b.price / 100) * b.quantity} €</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>

                    <button className={'btn btn-success'}>Commander</button>
                </>
            )}
        </div>
    )
}