import { apiAdmin } from "./axios";


class ProductApi {
    getProducts = (params) => {
        return apiAdmin.get("/products", { params: params });
    }

    addProduct = (formData) => {
        return apiAdmin.post("/product/add", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    getProductDetails = (id) => {
        return apiAdmin.get(`/product/${id}`);
    }

    update = (formData, id) => {
        return apiAdmin.post(`/product/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    delete = (id) => {
        return apiAdmin.delete(`/product/${id}`);
    }
}

const productApi = new ProductApi();

export default productApi;


