<template>
    <a-layout-header class="header">
      <div class="logo-wrapper">
        <router-link to="/" class="logo">MyApp</router-link>
      </div>
  
      <!-- Cart and Purchase History -->
      <div class="nav-actions">
        <router-link to="/cart" class="nav-item">
          <ShoppingCartOutlined class="nav-icon" />
          <span class="nav-text">Cart</span>
          <a-badge
            :count="cartCount"
            :number-style="{ backgroundColor: '#ff4d4f', fontSize: '12px' }"
            :offset="[10, 0]"
          />
        </router-link>
  
        <router-link to="/history" class="nav-item">
          <HistoryOutlined class="nav-icon" />
          <span class="nav-text">Purchase History</span>
        </router-link>
      </div>
  
      <!-- User Dropdown-->
      <a-dropdown>
        <a-button type="primary" class="user-button">
          <UserOutlined />
          <span class="user-text">Hi, {{ admin?.name }}</span>
          <DownOutlined />
        </a-button>
        <template #overlay>
          <a-menu theme="dark">
            <a-menu-item key="logout" @click="logout">
              <LogoutOutlined class="menu-icon" />
              <span class="menu-text">Logout</span>
            </a-menu-item>
          </a-menu>
        </template>
      </a-dropdown>
    </a-layout-header>
  </template>

<script setup>
import { computed, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { ShoppingCartOutlined, HistoryOutlined, LogoutOutlined, UserOutlined, DownOutlined } from "@ant-design/icons-vue";
import store from "@/store";

const admin = computed(() => store.getters["auth/user"] || {});
const router = useRouter();
const cartCount = computed(() => store.getters["cart/count"]);

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
  background: linear-gradient(90deg, #001529 0%, #003a8c 100%);
  padding: 0 40px;
  height: 70px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.logo-wrapper {
  display: flex;
  align-items: center;
}

.logo {
  font-size: 28px;
  font-weight: 800;
  color: #fff;
  text-decoration: none;
  letter-spacing: 1px;
  transition: all 0.3s ease;
}

.logo:hover {
  color: #40c4ff;
  transform: scale(1.05) rotate(2deg);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.15);
  color: #40c4ff;
  transform: translateY(-2px);
}

.nav-icon {
  font-size: 18px;
}

.nav-text {
  font-size: 14px;
  font-weight: 500;
}

/* User Dropdown */
.user-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #1890ff;
  border-color: #1890ff;
  border-radius: 20px;
  padding: 0 15px;
  height: 40px;
  transition: all 0.3s ease;
}

.user-button:hover {
  background-color: #40a9ff;
  border-color: #40a9ff;
  transform: translateY(-2px);
}

.user-text {
  font-size: 14px;
  font-weight: 500;
}

/* Menu Dropdown */
.menu-icon {
  font-size: 16px;
  margin-right: 8px;
}

.menu-text {
  font-size: 14px;
  font-weight: 500;
}

/* Badge */
:deep(.ant-badge-count) {
  box-shadow: 0 0 0 2px #fff;
  font-size: 12px;
  height: 18px;
  line-height: 18px;
  min-width: 18px;
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    padding: 0 20px;
    flex-wrap: wrap;
    height: auto;
    padding-top: 10px;
    padding-bottom: 10px;
  }
  .nav-actions {
    gap: 15px;
  }
  .nav-item {
    padding: 6px 12px;
  }
  .user-button {
    margin-left: auto;
  }
}
</style>