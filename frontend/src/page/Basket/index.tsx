import {useQuery} from "../../hook/useQuery.tsx";
import {fetchBasket, validateBasket} from "../../service/backend.api.tsx";
import Modal from "../../component/Modal";
import {useEffect, useRef, useState} from "react";
import {useLazyQuery} from "../../hook/useLazyQuery.tsx";
import {loadStripe} from "@stripe/stripe-js"
import {toast} from "react-toastify";

export default () => {
    const {data, isLoading, isSuccess} = useQuery(
        () => fetchBasket()
    )

    const {call: validateBasketCall, data: validateBasketData, isSuccess: validateBasketIsSuccess, hasLoaded: validateBasketHasLoaded, isLoading: validateBasketIsLoading} = useLazyQuery(
        () => validateBasket()
    )
    const [showModal, setShowModal] = useState(false)
    const cardRef = useRef(null)
    const [stripeElements, setStripeElements] = useState(null)
    const [isStripeLoading, setIsStripeLoading] = useState(false)
    const [stripe, setStripe] = useState(null)
    const [isWaitingForResultCheckout, setIsWaitingForResultCheckout] = useState(false)

    const submitPayment = async (e) => {
        e.preventDefault()

        if (!stripe) {
            return
        }

        setIsWaitingForResultCheckout(true)
        const response = await stripe.confirmPayment({
            elements: stripeElements,
            redirect: 'if_required'
        })

        if (response.paymentIntent.status === 'succeeded') {
            toast('Votre paiement à réussi !', {type: 'success'})
        } else {
            toast('Oups, une erreur est survenue lors du paiement.', {type: 'error'})
        }

        setShowModal(false)
    }

    useEffect(() => {
        (async () => {
            if (showModal && validateBasketIsSuccess && validateBasketData) {
                setIsStripeLoading(true)
                const stripe = await loadStripe(import.meta.env.VITE_APP_STRIPE_PUBLIC_KEY)
                setStripe(stripe)

                const elements = stripe!.elements({
                    locale: 'auto',
                    clientSecret: validateBasketData.clientRef,
                })

                const paymentElement = elements.create('payment')
                paymentElement.mount(cardRef.current!)

                setStripeElements(elements)
                setIsStripeLoading(false)
            }
        })()
    }, [showModal, validateBasketIsSuccess]);

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
                            <tr key={b.id}>
                                <td>{b.name}</td>
                                <td>{b.quantity}</td>
                                <td>{b.price / 100} €</td>
                                <td>{(b.price / 100) * b.quantity} €</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>

                    <button onClick={() => {
                        setShowModal(true)
                        validateBasketCall()
                    }} className={'btn btn-success'}>Commander</button>
                </>
            )}

            <Modal onClose={() => setShowModal(false)} title={"Payment"} show={showModal}>
                {isLoading && (
                    <>
                        <span className="spinner-border" role="status" style={{color: 'yellowgreen'}}></span>
                    </>
                )}
                {(!validateBasketIsLoading && validateBasketIsSuccess && validateBasketHasLoaded) && (
                    <>
                        <div style={{display: isWaitingForResultCheckout ? 'none' : 'block'}} ref={cardRef}></div>

                        {(isStripeLoading || isWaitingForResultCheckout) && (
                            <span className="spinner-border" role="status" style={{color: 'yellowgreen'}}></span>
                        )}

                        {(!isStripeLoading || !isWaitingForResultCheckout) && (
                            <button onClick={submitPayment} className={'btn btn-success'}>Payer</button>
                        )}
                    </>
                )}
            </Modal>
        </div>
    )
}