import {useState} from "react";
import {useLazyQuery} from "../../hook/useLazyQuery.tsx";
import {register} from "../../service/backend.api.tsx";

export default () => {
    const [form, setForm] = useState({
        email: '',
        password: '',
    })

    const {call, isLoading, hasLoaded, isSuccess} = useLazyQuery(
        () => register({email: form.email, password: form.password})
    )

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
                            <button type="submit" className="btn btn-outline-primary">Inscription</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    )
}