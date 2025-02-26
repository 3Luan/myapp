<template>
    <router-view>
    </router-view>
</template>
<script>
import { mapActions } from "vuex";

export default {
  methods: {
    ...mapActions("auth", ["getProfile"]),
  },
  created() {
    this.getProfile();
  },
};
</script>
