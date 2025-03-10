<template>
  <div class="login-container">
    <div class="login-form">
      <h3 class="text-center mb-4">Login Admin</h3>
      <form @submit.prevent="loginUser">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            class="form-control form-control-lg"
            id="email"
            v-model="email"
            placeholder="Enter email"
            required
          />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            class="form-control form-control-lg"
            id="password"
            v-model="password"
            placeholder="Password"
            required
          />
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            {{ loading ? "Logging in..." : "Login" }}
          </button>
        </div>

        <p v-if="errorMessage" class="text-danger mt-2">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useStore } from "vuex";

const email = ref("admin@gmail.com");
const password = ref("123");
const loading = ref(false);
const errorMessage = ref("");
const store = useStore();

const loginUser = async () => {
  loading.value = true;
  errorMessage.value = "";
  try {
    await store.dispatch("authAdmin/loginAdmin", { 
      email: email.value, 
      password: password.value 
    });
  } catch (error) {
    console.log("error", error);
    errorMessage.value = error.message || "Login failed";
  } finally {
    loading.value = false;
  }
};
</script>


<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(135deg, #ffffff, #ffffff);
}

.login-form {
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h3 {
  font-size: 28px;
  font-weight: 600;
  color: #333;
}

.form-group {
  margin: 10px 0;
}

.form-group label {
  font-size: 16px;
  font-weight: 500;
}

.form-control {
  border-radius: 5px;
}

.form-control-lg {
  padding: 12px 16px;
  font-size: 16px;
}

.btn-primary {
  background-color: #6e7dff;
  border: none;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
}

.btn-primary:hover {
  background-color: #5f5fa0;
}

.text-center a {
  color: #6e7dff;
  text-decoration: none;
}

.text-center a:hover {
  text-decoration: underline;
}
</style>
