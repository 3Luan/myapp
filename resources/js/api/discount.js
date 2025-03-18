import { apiAdmin } from "@/api/axios";

class DiscountApi {
    getDiscounts = (params) => {
        return apiAdmin.get("/discounts", { params: params });
    }

    getDiscountById = (id) => {
        return apiAdmin.get(`/discount/${id}`);
    }

    create = (formData) => {
        return apiAdmin.post(`/discount/create`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    update = (formData, id) => {
        return apiAdmin.post(`/discount/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    getProductsInDiscount = (id, params) => {
        return apiAdmin.get(`/discount/${id}/products`, { params: params });
    }

    addProductsToDiscount = (formData, id) => {
        return apiAdmin.post(`/discount/${id}/products/add`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }

    removeProductsFromDiscount = (formData, id) => {
        return apiAdmin.post(`/discount/${id}/products/remove`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    }
}

const discountApi = new DiscountApi();

export default discountApi;