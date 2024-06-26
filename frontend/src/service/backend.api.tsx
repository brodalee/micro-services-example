import axios from "axios";

export const httpClient = axios.create({
    baseURL: import.meta.env.VITE_APP_BACKEND_URI,
    headers: {
        'Content-Type': 'application/json'
    }
})

httpClient.interceptors.response.use((d) => {
    if (d.status >= 200 && d.status < 300) {
        return d.data
    }

    throw new Error(d.data)
}, e => {
    if (e.response.data) {
        throw new Error(e.response.data)
    }

    throw new Error(e.message)
})

type RegisterParams = {
    email: string,
    password: string
}
export const register = async (params: RegisterParams): Promise<boolean> => {
    return httpClient.post('authentication/register', params)
        .then(d => true)
        .catch(() => false)
}

type LoginParams = {
    email: string,
    password: string
}

export type LoginResponse = null|{
    token: string
}
export const login = async (params: LoginParams): Promise<LoginResponse> => {
    return await httpClient.post('authentication/login', params)
}

type FetchProductsParams = {
    page: number
    limit: number
}
type FetchProductsResponse = {
    totalPage: number
    totalCount: number
    nextPage: number|null
    previousPage: number|null
    products: Product[]
}

export type Product = {
    id: string
    designation: string
    price: number
    brand: string
    imgUrl: string
    ram: string
    processor: string
    resolution: string
}
export const fetchProducts = async ({limit = 20, page} : FetchProductsParams): Promise<FetchProductsResponse> => {
    return await httpClient.get('products', {params: {limit, page}})
}

export type FetchBasketResponse = {
    id: string
    name: string
    price: number
    reference: string
    quantity: number
}
export const fetchBasket = async (): Promise<FetchBasketResponse[]> => {
    return await httpClient.get('basket')
}

export const addProductInBasket = async (productId: string) => {
    return await httpClient.post('basket/add', {productId})
}

type FetchNotificationResponse = {
    id: string
    message: string
}

export const fetchNotifications = async (): Promise<FetchNotificationResponse[]> => {
    return await httpClient.get('notifications')
}

export const markNotificationAsSeen = async (notificationId: string) => {
    return await httpClient.patch('notifications/' + notificationId)
}

type ValidateBasketResponse = {
    billingId: string
    clientRef: string
    paymentReference: string
}

export const validateBasket = async (): Promise<ValidateBasketResponse> => {
    return await httpClient.post('basket/validate')
}

type FetchBillingsResponse = {
    billings: Billing[]
}

type Billing = {
    id: string
    totalPrice: number
    creationDate: string
    tva: number
    items: Item[]
    paymentMethod: string
    isPaymentSucceeded: boolean
}

type Item = {
    id: string
    price: number
    quantity: number
    productName: string
}

export const fetchBillings = async (): Promise<FetchBillingsResponse> => {
    return await httpClient.get('billings')
}