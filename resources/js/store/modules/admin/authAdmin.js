import axios from "axios";
import router from "../../../routes";
import authApi from "../../../api/auth";

const state = () => ({
    admin: null,
    // JSON.parse(localStorage.getItem("admin")) || 
    token: localStorage.getItem("token_admin") || null,
});

// mutations
const mutations = {
    SET_TOKEN_ADMIN(state, data) {
        state.token = data.token;
        state.admin = data.user;

        localStorage.setItem("token_admin", data.token);

        axios.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;
    },
    SET_ADMIN(state, data) {
        state.admin = data.user;
    },
    LOGOUT_ADMIN(state) {
        state.admin = null;
        state.token = null;
        localStorage.removeItem("token_admin");
        delete axios.defaults.headers.common["Authorization"];
        router.push("/admin/login");
    },
};

// actions
const actions = {
    async loginAdmin({ commit }, credentials) {
        try {
            const response = await authApi.loginAdmin(credentials);
            commit("SET_TOKEN_ADMIN", response.data);
            router.push("/admin/users");
            return response.data;
        } catch (error) {
            console.log(error);
            throw new Error("Login failed! Please check your credentials.");
        }
    },

    async getProfileAdmin({ commit, state }) {
        if (!state.token) return;
        try {
            const response = await authApi.getProfileAdmin();
            commit("SET_ADMIN", response.data);
        } catch (error) {
            console.log(error);
            commit("LOGOUT_ADMIN");
        }
    },

    logoutAdmin({ commit }) {
        commit("LOGOUT_ADMIN");
    },
};

// getters
const getters = {
    isAuthenticated: (state) => !!state.token,
    admin: (state) => state.admin,
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
