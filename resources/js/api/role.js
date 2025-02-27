import api from "./axios"


class RoleApi {
    getRoles = (params) => {
        return api.get("/roles", { params: params });
    }

    addRole = (name) => {
        return api.post("/role/add", { name });
    }
}

const roleApi = new RoleApi;

export default roleApi;


