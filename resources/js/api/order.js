import { api, apiAdmin } from "./axios";


class OrderApi {
    getOrders = (params) => {
        return apiAdmin.get("/orders", { params: params });
    }

    addOrder = (formData) => {
        return apiAdmin.post("/order/add", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    updateState = (state, id) => {
        return apiAdmin.post("/order/updateState", {state, id});
    }
}

const orderApi = new OrderApi();

export default orderApi;


