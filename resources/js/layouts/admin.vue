<template>
    <div class="admin-layout">
        <Header />
        <div class="container-fluid mt-4">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-sm-3 d-none d-sm-block">
                    <a-card class="sidebar-card">
                        <template #title>
                            <span class="fw-bold">Control Panel</span>
                        </template>
                        <Menu />
                    </a-card>
                </div>

                <!-- Content -->
                <div class="col-12 col-sm-9">
                    <a-card class="content-card">
                        <router-view />
                    </a-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Header from "../components/admin/Header.vue";
import Menu from "../components/Menu.vue";

export default {
    components: {
        Header,
        Menu,
    }
};
</script>

<style scoped>
.admin-layout {
    background: #f4f6f9;
    min-height: 100vh;
}

.sidebar-card {
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    background: #fff;
}

.content-card {
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    background: #fff;
    padding: 20px;
}
</style>
