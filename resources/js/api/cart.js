import { api, apiAdmin } from "@/api/axios";


class CartApi {
    getCarts = (params) => {
        return api.get("/carts", { params: params });
    }

    addCart = (data) => {
        return api.post("/cart/add", data);
    }

    updateCart = (data) => {
        return api.post("/cart/update", data);
    }

    deleteCart = (data) => {
        return api.post("/cart/delete", data);
    }

    checkout = (formData) => {
        return api.post("/cart/checkout", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }
}

const cartApi = new CartApi();

export default cartApi;


