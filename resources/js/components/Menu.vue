<template>
    <div>
        <a-menu style="width: 100%; height: 75vh;" mode="vertical" :selectedKeys="selectedKeys">
            <a-menu-item key="admin-users">
                <router-link :to="{ name: 'admin-users' }" />
                Accounts
            </a-menu-item>
            <a-menu-item key="admin-roles">
                <router-link :to="{ name: 'admin-roles' }" />
                Roles
            </a-menu-item>
            <a-menu-item key="admin-products">
                <router-link :to="{ name: 'admin-products' }" />
                Products
            </a-menu-item>
            <a-menu-item key="admin-orders">
                <router-link :to="{ name: 'admin-orders' }" />
                Orders
            </a-menu-item>
            <a-menu-item key="admin-discounts">
                <router-link :to="{ name: 'admin-discounts' }" />
                Discounts
            </a-menu-item>
        </a-menu>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { useRoute } from 'vue-router';

export default defineComponent({
    setup() {
        const route = useRoute();

        const selectedKeys = computed(() => {
            if (route.name === 'admin-users') {
                return ['admin-users'];
            } else if (route.name === 'admin-roles') {
                return ['admin-roles'];
            } else if (route.name === 'admin-products') {
                return ['admin-products'];
            } else if (route.name === 'admin-orders') {
                return ['admin-orders'];
            }
            return [];
        });

        return {
            selectedKeys,
        };
    }
});
</script>
