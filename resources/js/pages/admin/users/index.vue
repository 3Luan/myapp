<template>
    <a-card title="Tài khoản" style="width: 100%;">
        <div class="row">
            <div class="col-12">
                <a-spin :spinning="isLoading" tip="Đang tải...">
                    <a-table :dataSource="users" :columns="columns" rowKey="id" />
                </a-spin>
            </div>
        </div>
    </a-card>
</template>

<script>
import { defineComponent, ref } from 'vue';
import api from '../../../api/axios';
import store from "../../../store";

export default defineComponent({
    setup() {
        const users = ref([]);
        const isLoading = ref(true);

        const fields = [
            { title: "ID", key: "id" },
            { title: "Họ tên", key: "name" },
            { title: "Email", key: "email" },
            { title: "Số điện thoại", key: "phone" },
            { title: "Giới tính", key: "gender" },
            { title: "Vai trò", key: "role" },
        ];

        const columns = fields.map(field => ({
            title: field.title,
            dataIndex: field.key,
            key: field.key,
        }));

        const getUsers = () => {
                api.get("/users")
                .then((response) => {
                    users.value = response.data;
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    isLoading.value = false;
                });
        };

        getUsers();

        return {
            users,
            columns,
            isLoading,
        };
    }
});
</script>
