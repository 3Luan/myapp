import axios from "axios";
import api from "../../api/axios";
import router from "../../routes";
import { getProfileAPI, loginAPI } from "../../api/auth";

const state = () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
});

// mutations
const mutations = {
    SET_TOKEN_USER(state, data) {
        state.token = data.token;
        state.user = data.user;
        console.log("data", data);

        localStorage.setItem("token", data.token);

        axios.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;
    },
    SET_USER(state, data) {
        state.user = data.user;
    },
    LOGOUT(state) {
        state.user = null;
        state.token = null;
        localStorage.removeItem("token");
        delete axios.defaults.headers.common["Authorization"];
        router.push("/admin/login");
    },
};

// actions
const actions = {
    async login({ commit }, credentials) {
        try {
            const response = await loginAPI(credentials);
            commit("SET_TOKEN_USER", response.data);

            return response.data;
        } catch (error) {
            console.log(error);
            throw new Error("Login failed! Please check your credentials.");
        }
    },

    async getProfile({ commit, state }) {
        if (!state.token) return;
        try {
            const response = await getProfileAPI();

            commit("SET_USER", response.data);
        } catch (error) {
            console.log(error);
            commit("LOGOUT");
        }
    },

    logout({ commit }) {
        commit("LOGOUT");
    },
};

// getters
const getters = {
    isAuthenticated: (state) => !!state.token,
    user: (state) => state.user,
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
