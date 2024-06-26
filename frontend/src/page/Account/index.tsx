import {useQuery} from "../../hook/useQuery.tsx";
import {fetchBillings} from "../../service/backend.api.tsx";

export default () => {
    const {data, isSuccess, isLoading} = useQuery(
        () => fetchBillings()
    )

    console.log(data)

    return (
        <div className={'container'} style={{marginTop: '5%'}}>
            {(!isLoading && isSuccess) && (
                <>
                    <table className="table">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope='col'>Produits</th>
                            <th scope="col">Prix total €</th>
                            <th scope="col">TVA</th>
                            <th scope="col">Méthode de paiement</th>
                        </tr>
                        </thead>
                        <tbody>
                        {data?.billings.map(b => (
                            <tr key={b.id}>
                                <td>{b.creationDate}</td>
                                <td>
                                    <table className="table">
                                        <thead>
                                        <tr>
                                            <th scope='col'>Nom du produit</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Prix unitaire</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {b.items.map(i => (
                                            <tr key={i.id}>
                                                <td>{i.productName}</td>
                                                <td>{i.quantity}</td>
                                                <td>{i.price / 100} €</td>
                                                <td>{(i.price / 100) * i.quantity} €</td>
                                            </tr>
                                        ))}
                                        </tbody>
                                    </table>
                                </td>
                                <td>{b.totalPrice / 100} €</td>
                                <td>{b.tva} %</td>
                                <td>{b.paymentMethod} ({b.isPaymentSucceeded ? 'Paiement réussi' : 'Paiement échoue'})</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>

                </>
            )}
        </div>
    )
}