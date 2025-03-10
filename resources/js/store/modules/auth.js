import axios from "axios";
import router from "@/routes";
import authApi from "@/api/auth";
import store from "../index";

const state = () => ({
    user: null,
    token: localStorage.getItem("token") || null,
});

// mutations
const mutations = {
    SET_TOKEN(state, data) {
        state.token = data.token;
        state.user = data.user;

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
        router.push("/login");
    },
};

// actions
const actions = {
    async login({ commit }, credentials) {
        try {
            const response = await authApi.login(credentials);
            commit("SET_TOKEN", response.data);
            router.push("/");
            return response.data;
        } catch (error) {
            console.log(error);
            throw new Error(error.response.data.message);
        }
    },

    async getProfile({ commit, state }) {
        if (!state.token) return;
        try {
            const response = await authApi.getProfile();
            commit("SET_USER", response.data);
        } catch (error) {
            console.log(error);
            commit("LOGOUT");
        }
    },

    logout({ commit }) {
        store.dispatch("resetStore", null, { root: true });
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
