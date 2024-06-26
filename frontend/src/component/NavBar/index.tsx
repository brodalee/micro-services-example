import useBasket from "../../hook/useBasket.tsx";
import {toast} from "react-toastify";
import {useNavigate} from "react-router-dom";

export default () => {
    const basket = useBasket()
    const navigate = useNavigate()

    const goToBasket = () => {
        if (basket.count === 0) {
            toast('Veuillez ajouter des produits.')
            return
        }

        navigate('/basket')
    }

    return (
        <>
            <nav className="navbar navbar-light" style={{backgroundColor: 'white'}}>
                <div className="container">
                    <a onClick={() => navigate('/account')} className="navbar-brand" href="#">Profile</a>
                    <a onClick={() => navigate('/')} className="navbar-brand" href="#">Accueil</a>
                    <button onClick={goToBasket} type="button" className="btn btn-light">
                        Panier <span className="badge" style={{color: 'black'}}>{basket.count}</span>
                    </button>
                </div>
            </nav>
        </>
    )
}