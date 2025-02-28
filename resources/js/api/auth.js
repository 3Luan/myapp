import { api, apiAdmin } from "./axios";

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

    /////////////////// Admin ///////////////////
    loginAdmin = (data) => {
        return apiAdmin.post("/auth/loginAdmin", data);
    }

    getProfileAdmin = () => {
        return apiAdmin.get("/auth/getProfileAdmin");
    }
}

const authApi = new AuthApi();

export default authApi;