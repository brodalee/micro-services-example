import {useEffect, useState} from "react";
import {useLazyQuery} from "../../hook/useLazyQuery.tsx";
import {httpClient, login, LoginResponse} from "../../service/backend.api.tsx";
import {useNavigate} from "react-router-dom";
import {useSetRecoilState} from "recoil";
import {userAtom} from "../../store/userAtom.tsx";

export default () => {
    const [form, setForm] = useState({
        email: '',
        password: '',
    })
    const navigate = useNavigate()
    const setUser = useSetRecoilState(userAtom)

    const {call, isLoading, hasLoaded, isSuccess, data} = useLazyQuery<LoginResponse>(
        () => login({email: form.email, password: form.password})
    )

    useEffect(() => {
        if (!isLoading && hasLoaded && isSuccess) {
            setUser({token: data?.token!})
            httpClient.defaults.headers.common['Authorization'] = 'Bearer ' + data?.token!
            navigate('/')
        }
    }, [isLoading, hasLoaded, isSuccess])

    const submitForm = (e) => {
        e.preventDefault()

        call()
    }

    return (
        <div className="col-lg-12 login-form">
            <div className="col-lg-12 login-form">
                <form onSubmit={submitForm}>
                    <div className="form-group">
                        <label className="form-control-label">Email</label>
                        <input onChange={e => setForm({...form, email: e.target.value})} type="text" className="form-control"/>
                    </div>
                    <div className="form-group">
                        <label className="form-control-label">Mot de passe</label>
                        <input onChange={e => setForm({...form, password: e.target.value})} type="password" className="form-control"/>
                    </div>

                    <div className="col-lg-12 loginbttm">
                        <div className="col-lg-7 login-btm login-button">
                            <button type="submit" className="btn btn-outline-primary">Connexion</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    )
}