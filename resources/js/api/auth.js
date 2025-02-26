import api from "./axios"


export const loginAPI = (data) => {
    return api.post("/auth/login", data);
}

export const getProfileAPI = () => {
    return api.get("/auth/getProfile");
}
