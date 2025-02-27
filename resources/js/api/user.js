import api from "./axios"


class UserApi {
    getUsers = (params) => {
        return api.get("/users", { params: params });
    }

    getUserById = (id) => {
        return api.get(`/user/${id}`);
    }
}

const userApi = new UserApi;

export default userApi;


