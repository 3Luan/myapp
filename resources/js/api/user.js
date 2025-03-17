import { apiAdmin } from "@/api/axios";

class UserApi {
    getUsers = (params) => {
        return apiAdmin.get("/users", { params: params });
    }

    getUserById = (id) => {
        return apiAdmin.get(`/user/${id}`);
    }

    update = (formData, id) => {
        return apiAdmin.post(`/user/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }
}

const userApi = new UserApi();

export default userApi;