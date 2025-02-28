<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5">
        <a class="navbar-brand" href="#">Header</a>
        <div class="text-white">
            MyApp
        </div>

        <button 
            type="button" 
            class="btn btn-secondary btn-xl" 
            @click="logout">
            Logout
        </button>
    </nav>
</template>

<script setup>
import store from '../store';

const logout = () => {
    store.dispatch("auth/logout");
};

</script>
