import { createStore } from 'vuex'
import authAdmin from './modules/admin/authAdmin'
import auth from './modules/auth'

export default createStore({
    modules: {
        authAdmin,
        auth
    },
})