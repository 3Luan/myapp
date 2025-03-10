import { api, apiAdmin } from "@/api/axios";


class OrderApi {
    getOrders = (params) => {
        return api.get("/orders", { params: params });
    }

    addOrder = (formData) => {
        return api.post("/order/add", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    updateState = (state, id) => {
        return api.post("/order/updateState", {state, id});
    }

    /////////////// Admin ///////////////
    getOrdersAdmin = (params) => {
        return apiAdmin.get("/orders", { params: params });
    }

    updateStateAdmin = (state, id) => {
        return apiAdmin.post("/order/updateState", {state, id});
    }
}

const orderApi = new OrderApi();

export default orderApi;


