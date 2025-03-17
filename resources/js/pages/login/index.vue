<template>
  <div class="auth-container">
    <div class="auth-form">
      <h3 class="text-center mb-4">Login</h3>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            class="form-control form-control-lg"
            id="email"
            v-model="email"
            placeholder="Enter your email"
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
            placeholder="Enter your password"
            required
          />
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="isLoading">
          <span v-if="isLoading" class="spinner"></span>
          {{ isLoading ? "Logging in..." : "Sign In" }}
        </button>

        <p v-if="errorMessage" class="text-danger mt-3 text-center">{{ errorMessage }}</p>

        <p class="text-center mt-4">
          New here?
          <router-link to="/register" class="register-link">Create an account</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const isLoading = ref(false);
const errorMessage = ref("");
const email = ref('');
const password = ref('');
const store = useStore();
const router = useRouter();

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = "";
  
  try {
    await store.dispatch("auth/login", {
      email: email.value,
      password: password.value
    });
    router.push("/");
  } catch (error) {
    console.log(error);
    errorMessage.value = error.message || "Error: Login";
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(45deg, #e8f0fe 0%, #f9faff 100%);
  padding: 20px;
  overflow: hidden;
}

.auth-form {
  background: rgba(255, 255, 255, 0.95);
  padding: 40px 35px;
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(10, 20, 60, 0.1);
  width: 100%;
  max-width: 450px;
  backdrop-filter: blur(10px);
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

h3 {
  font-size: 32px;
  font-weight: 800;
  color: #16213e;
  margin-bottom: 35px;
  letter-spacing: -0.5px;
  background: linear-gradient(90deg, #6e7dff, #4a58c2);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.form-group {
  margin-bottom: 25px;
  position: relative;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #34495e;
  margin-bottom: 10px;
  display: block;
  transition: all 0.3s ease;
}

.form-control {
  border: none;
  border-bottom: 2px solid #e2e8f0;
  border-radius: 0;
  padding: 12px 0;
  font-size: 16px;
  background: transparent;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  border-color: #6e7dff;
  box-shadow: none;
  outline: none;
}

.form-control:focus + label,
.form-control:not(:placeholder-shown) + label {
  transform: translateY(-20px);
  font-size: 12px;
  color: #6e7dff;
}

.btn-primary {
  background: linear-gradient(135deg, #6e7dff 0%, #4a58c2 100%);
  border: none;
  padding: 15px;
  font-size: 16px;
  font-weight: 700;
  border-radius: 50px;
  transition: all 0.4s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #4a58c2 0%, #3a48a2 100%);
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(110, 125, 255, 0.3);
}

.btn-primary:disabled {
  background: #b0b7c3;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.text-danger {
  font-size: 13px;
  padding: 10px;
  background: rgba(254, 226, 226, 0.8);
  border-radius: 8px;
  border-left: 4px solid #e74c3c;
}

.register-link {
  color: #6e7dff;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  padding: 2px 6px;
  border-radius: 4px;
}

.register-link:hover {
  color: #fff;
  background: #6e7dff;
  text-decoration: none;
}
</style>