import {atom} from "recoil";
import {recoilPersist} from "recoil-persist";

const { persistAtom } = recoilPersist()

type UserAtom = null|{
    token: string
}

export const userAtom = atom<UserAtom>({
    key: 'user',
    default: null,
    //effects_UNSTABLE: [persistAtom],
})