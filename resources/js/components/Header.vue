<template>
    <a-layout-header class="header">
        <!-- Logo với hiệu ứng hover -->
        <div class="logo-wrapper">
            <router-link to="/" class="logo">MyApp</router-link>
        </div>

        <!-- Menu chính -->
    

        <!-- User Dropdown và Logout -->
        <a-dropdown>
            <a-button type="primary" class="user-button">
                <UserOutlined />
                <span class="user-text">Hi, {{ admin?.name }}</span>
                <DownOutlined />
            </a-button>
            <template #overlay>
                <a-menu theme="dark" >
                    <a-menu-item key="cart">
                        <router-link to="/cart">
                            <ShoppingCartOutlined class="menu-icon" />
                            <span class="menu-text">Cart</span>
                            <a-badge
                                :count="cartCount"
                                :number-style="{ backgroundColor: '#ff4d4f' }"
                                offset="[10, 0]"
                            />
                        </router-link>
                    </a-menu-item>

                    <a-menu-item key="history">
                        <router-link to="/history">
                            <HistoryOutlined class="menu-icon" />
                            <span class="menu-text">Purchase History</span>
                        </router-link>
                    </a-menu-item>

                    <a-menu-item key="logout" @click="logout">
                        <LogoutOutlined />
                        <span>Logout</span>
                    </a-menu-item>
                </a-menu>
            </template>
        </a-dropdown>
    </a-layout-header>
</template>

<script setup>
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { ShoppingCartOutlined, HistoryOutlined, LogoutOutlined, UserOutlined, DownOutlined } from "@ant-design/icons-vue";
import store from "../store";

const admin = computed(() => store.getters["auth/user"]);

const router = useRouter();
const cartCount = ref(1);

const logout = () => {
    store.dispatch("auth/logout");
    router.push("/login");
};
</script>

<style scoped>
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(90deg, #001529 0%, #003a8c 100%); /* Gradient background */
    padding: 0 30px;
    height: 64px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo-wrapper {
    display: flex;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.logo:hover {
    color: #40a9ff;
    transform: scale(1.05); /* Hiệu ứng phóng to nhẹ khi hover */
}

.menu {
    flex: 1;
    display: flex;
    justify-content: center;
    background: transparent;
    border-bottom: none;
}

.menu :deep(.ant-menu-item) {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 20px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.menu :deep(.ant-menu-item:hover) {
    background-color: rgba(255, 255, 255, 0.1);
}

.menu-icon {
    font-size: 16px;
}

.menu-text {
    font-size: 14px;
    font-weight: 500;
}

.user-button {
    display: flex;
    align-items: center;
    gap: 6px;
    background-color: #1890ff;
    border-color: #1890ff;
    transition: all 0.3s ease;
}

.user-button:hover {
    background-color: #40a9ff;
    border-color: #40a9ff;
    transform: translateY(-2px); /* Hiệu ứng nâng lên khi hover */
}

.user-text {
    font-size: 14px;
    font-weight: 500;
}

:deep(.ant-badge-count) {
    box-shadow: 0 0 0 2px #001529; /* Viền xung quanh badge cho đẹp */
}
</style>