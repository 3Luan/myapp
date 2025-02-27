import api from "./axios"

class AuthApi {
    login = (data) => {
        return api.post("/auth/login", data);
    }

    register = (data) => {
        return api.post("/auth/register", data);
    }

    getProfile = () => {
        return api.get("/auth/getProfile");
    }
}

const authApi = new AuthApi;

export default authApi;