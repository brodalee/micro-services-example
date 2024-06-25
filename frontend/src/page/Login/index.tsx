import './style.scss'
import {useRecoilValue} from "recoil";
import {userAtom} from "../../store/userAtom.tsx";
import {Navigate} from "react-router-dom";
import {useState} from "react";
import Login from "../../form/Login";
import Register from "../../form/Register";

export default () => {
    const user = useRecoilValue(userAtom)
    const [mode, setMode] = useState('LOGIN')
    if (user) {
        return <Navigate to={'/'} />
    }


    return (
        <>
            <div className="container">
                <div className="row">
                    <div className="col-lg-3 col-md-2"></div>
                    <div className="col-lg-6 col-md-8 login-box">
                        <div className="col-lg-12 login-key">
                            <i className="fa fa-key" aria-hidden="true"></i>
                        </div>
                        <div className="col-lg-12 login-title">
                            {mode === 'LOGIN' && (<>Connexion</>) }
                            {mode === 'REGISTER' && (<>Inscription</>) }
                        </div>
                        {mode === 'LOGIN' && (<span onClick={() => setMode('REGISTER')} style={{color: 'white', cursor: 'pointer'}}>Pas encore de compte ? cliquez ici</span>) }
                        {mode === 'REGISTER' && (<span onClick={() => setMode('LOGIN')} style={{color: 'white', cursor: 'pointer'}}>Vous avez déjà un compte ? cliquez ici</span>) }

                        {mode === 'LOGIN' && (<Login />) }
                        {mode === 'REGISTER' && (<Register />) }
                    </div>
                </div>
            </div>
        </>
    )
}