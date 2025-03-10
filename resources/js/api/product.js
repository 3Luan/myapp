import { apiAdmin, api } from "@/api/axios";

class ProductApi {
    getProducts = (params) => {
        return api.get("/products", { params: params });
    }

    getProductDetails = (id) => {
        return api.get(`/product/${id}`);
    }

    /////////////// Admin ///////////////

    getProductsAdmin = (params) => {
        return apiAdmin.get("/products", { params: params });
    }

    getProductDetailsAdmin = (id) => {
        return apiAdmin.get(`/product/${id}`);
    }

    getAllProducts = () => {
        return apiAdmin.get("/allProducts");
    }

    addProduct = (formData) => {
        return apiAdmin.post("/product/add", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    update = (formData, id) => {
        return apiAdmin.post(`/product/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    delete = (ids) => {
        return apiAdmin.post(`/product`, ids);
    }

    import = (formData, config = {}) => {
        return apiAdmin.post(`/import`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            ...config,
        });
    };

}

const productApi = new ProductApi();

export default productApi;


