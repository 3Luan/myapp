<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5 d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="#">Navbar</a>
      
      <div class="text-white text-center">
        <h5>{{ admin?.name }} - {{ admin?.role?.name }}</h5>
      </div>
      
      <div class="d-flex gap-3">
        <CustomButton :type="ButtonType.SECONDARY" @click="logout">Logout</CustomButton>
      </div>
    </nav>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import store from '../../store';
  import CustomButton from "../../components/common/CustomButton.vue";
  import { ButtonType } from "../../constants/index.js";
  
  const admin = computed(() => store.getters["authAdmin/admin"]);
  
  const logout = () => {
    store.dispatch("authAdmin/logoutAdmin");
  };
  </script>
  
  <style scoped>
  .navbar h5 {
    margin: 0;
    font-size: 1rem;
  }
  .gap-3 {
    gap: 1rem;
  }
  </style>
  