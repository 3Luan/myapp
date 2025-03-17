import cartApi from "@/api/cart";
import { message } from "ant-design-vue";

const state = () => ({
    count: null,
    cart: null,
});

// mutations
const mutations = {
    SET_DATA(state, data) {
        state.count = data.data.length || null;
        state.cart = data || null;
    },
    ADD_TO_CART(state, product) {
        const existingProduct = state.cart.data.find(item => item.id === product.id);
        if (existingProduct) {
            existingProduct.count = (existingProduct.count || 1) + 1;
        } else {
            state.cart.data.unshift(product);
            state.count = state.cart.data.length;
        }
    },
    REMOVE_FROM_CART(state, productIds) {
        if (!Array.isArray(productIds)) return;
        state.cart.data = state.cart.data.filter(item => !productIds.includes(item.id));
        state.count = state.cart.data.length;
    },
};


// actions
const actions = {
    async getCarts({ commit }, payload) {
        try {
            const response = await cartApi.getCarts(payload);
            commit("SET_DATA", response.data.original);
        } catch (error) {
            console.error(error);
        }
    },
    
    async addToCart({ commit }, payload) {
        try {
            const response = await cartApi.addCart(payload);
            commit("ADD_TO_CART", response.data.cart);
            console.log(response);
            
            return response.data;
        } catch (error) {
            console.error("Add to cart error:", error);
            throw error;
        }
    },

    async removeCard({ commit }, payload) {
        try {
            const response = await cartApi.deleteCart(payload);

            commit("REMOVE_FROM_CART", [payload.cart_id]);
            return response.data;
        } catch (error) {
            console.error("Add to cart error:", error);
            throw error;
        }
    },

    async orderCarts({ commit }, payload) {
        try {
            const response = await cartApi.checkout(payload);
            
            const cartIds = payload.products.map(item => item.cart_id);
            commit("REMOVE_FROM_CART", cartIds);
            
            message.success(response.data.message);
            return response.data;
        } catch (error) {
            console.error(error);
            throw error;
        }
    },
};

// getters
const getters = {
    count: (state) => state.count,
    cart: (state) => state.cart,
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
