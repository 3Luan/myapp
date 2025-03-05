<template>
  <div class="forbidden">
    <h1>403 - Forbidden</h1>
    <p>You don't have permission to access this page.</p>
  </div>
</template>

<style scoped>
.forbidden {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 50vh;
  text-align: center;
}

h1 {
  font-size: 2rem;
  color: #ff4d4d;
}

p {
  font-size: 1.2rem;
  color: #666;
}
</style>
