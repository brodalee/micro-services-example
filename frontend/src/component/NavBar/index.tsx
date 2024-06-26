import useBasket from "../../hook/useBasket.tsx";

export default () => {
    const basket = useBasket()

    return (
        <>
            <nav className="navbar navbar-light" style={{backgroundColor: 'white'}}>
                <div className="container">
                    <a className="navbar-brand" href="#">Profile</a>
                    <button type="button" className="btn btn-light">
                        Panier <span className="badge" style={{color: 'black'}}>{basket.count}</span>
                    </button>
                </div>
            </nav>
        </>
    )
}