import { api, apiAdmin } from "./axios";


class RoleApi {
    getRoles = (params) => {
        return apiAdmin.get("/roles", { params: params });
    }

    addRole = (name) => {
        return apiAdmin.post("/role/add", { name });
    }
}

const roleApi = new RoleApi();

export default roleApi;


