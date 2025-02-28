<template>
  <router-view>
  </router-view>
</template>

<!-- <script setup>
import { onMounted } from "vue";
import { useStore } from "vuex";

const store = useStore(); // Lấy store

const getProfile = async () => {
try {
  await store.dispatch("authAdmin/getProfileAdmin");
} catch (error) {
  console.error("Error fetching profile:", error);
}
};

onMounted(() => {
getProfile();
});
</script> -->

