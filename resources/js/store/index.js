import { createStore } from 'vuex'
import authAdmin from '@/store/modules/admin/authAdmin';
import auth from '@/store/modules/auth';
import cart from '@/store/modules/cart';

const initialState = {
    authAdmin: () => authAdmin.state(),
    auth: () => auth.state(),
    cart: () => cart.state(),
};

export default createStore({
    modules: {
        authAdmin,
        auth,
        cart
    },
    mutations: {
        RESET_STORE(state) {
            Object.keys(initialState).forEach((moduleName) => {
                if (moduleName !== 'authAdmin') {
                    Object.assign(state[moduleName], initialState[moduleName]());
                }
            });
        },
    },
    actions: {
        resetStore({ commit }) {
            commit("RESET_STORE");
        },
    },
});