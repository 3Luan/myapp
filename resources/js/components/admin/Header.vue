<template>
  <nav class="navbar">
    <div class="navbar__brand">
      <a href="#" class="navbar__logo">Admin</a>
    </div>

    <div class="navbar__admin">
      <div class="navbar__avatar-container">
        <img :src="adminAvatar" alt="Avatar" class="navbar__avatar" />
      </div>
      <div class="navbar__info">
        <h5 class="navbar__name">{{ admin?.name }}</h5>
        <span class="navbar__role">{{ admin?.role?.name }}</span>
      </div>
    </div>

    <div class="navbar__actions">
      <CustomButton :type="ButtonType.SECONDARY" @click="logout">Logout</CustomButton>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import store from "@/store";
import CustomButton from "@/components/common/CustomButton.vue";
import { ButtonType } from "@/constants/index.js";

const admin = computed(() => store.getters["authAdmin/admin"]);
const adminAvatar = computed(() => admin.value?.avatar || "https://i.pravatar.cc/50");

const logout = () => {
  store.dispatch("authAdmin/logoutAdmin");
};
</script>

<style scoped>
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(31, 31, 31, 0.85);
  backdrop-filter: blur(10px);
  padding: 15px 25px;
  box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.25);
  transition: all 0.3s ease-in-out;
}

.navbar:hover {
  box-shadow: 0px 8px 18px rgba(0, 0, 0, 0.3);
}

.navbar__logo {
  font-size: 1.7rem;
  font-weight: bold;
  color: white;
  text-decoration: none;
  transition: color 0.3s, transform 0.3s;
}

.navbar__logo:hover {
  color: #00eaff;
  transform: scale(1.05);
}

.navbar__admin {
  display: flex;
  align-items: center;
  gap: 15px;
}

.navbar__avatar-container {
  position: relative;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 0 8px #00eaff;
  transition: transform 0.3s ease-in-out;
}

.navbar__avatar-container:hover {
  transform: scale(1.1);
}

.navbar__avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.navbar__info {
  text-align: left;
}

.navbar__name {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  color: white;
}

.navbar__role {
  font-size: 0.9rem;
  color: #c4c4c4;
  font-style: italic;
}

.navbar__actions {
  display: flex;
  gap: 12px;
}

.navbar__actions button {
  transition: all 0.3s ease-in-out;
}

.navbar__actions button:hover {
  transform: scale(1.08);
}
</style>
