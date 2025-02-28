import { apiAdmin } from "./axios";


class UserApi {
    getUsers = (params) => {
        return apiAdmin.get("/users", { params: params });
    }

    getUserById = (id) => {
        return apiAdmin.get(`/user/${id}`);
    }
}

const userApi = new UserApi();

export default userApi;


