<template>
    <Forbidden v-if="isForbidden" />
    <a-card v-else title="Account Management" style="width: 100%; height: 76.5vh;">
        <div class="mb-4 flex items-center justify-between">
            <!-- Search -->
            <a-input-search 
                v-model:value="searchText"
                placeholder="Search..."
                enter-button
                @search="handleSearch"
                style="max-width: 300px;"
            />
        </div>

        <a-spin :spinning="isLoading" tip="Loading...">
            <div class="table-wrapper">
                <a-table
                    :dataSource="users"
                    :columns="columns"
                    :pagination="pagination"
                    :loading="isLoading"
                    rowKey="id"
                    bordered
                    :scroll="{ y: '50vh' }"
                    @change="handleTableChange"
                />
            </div>
        </a-spin>

        <UserModal
            :open="isUserModalOpen" 
            :data="userDetails"
            :reset="resetForm"
            @update:open ="isUserModalOpen = $event"
            @updateUser="handleUpdateUser" 
        />
    </a-card>
</template>

<script setup>
import { h, onMounted, ref } from 'vue';
import userApi from '@/api/user';
import { EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useToast } from 'vue-toast-notification';
import Forbidden from '@/components/Forbidden.vue';
import UserModal from '@/components/modals/UserModal.vue';
import { message } from 'ant-design-vue';

const users = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('name');
const orderType = ref('asc');
const filters = ref({ role: [], gender: [] });
const $toast = useToast();
const isForbidden = ref(false);
const isUserModalOpen = ref(false);
const userDetails = ref("");

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { title: "Name", dataIndex: "name", key: "name", sorter: true },
    { title: "Email", dataIndex: "email", key: "email", sorter: true },
    { title: "Phone", dataIndex: "phone", key: "phone", sorter: true },
    { title: "Role", dataIndex: "role", key: "role_id", sorter: true,
    customRender: ({ record }) => record.role ? record.role.name : "No Role"
    },
    {
        title: "Action",
        key: "action",
        align: "center",
        fixed: "right",
        width: 120,
        customRender: ({ record }) => [
            h(EditOutlined, {
                style: { color: "#1890ff", marginRight: "10px", cursor: "pointer" },
                onClick: () => handleDetails(record),
            }),
            h(DeleteOutlined, {
                style: { color: "red", cursor: "pointer" },
                // onClick: () => handleDelete(record.id),
            })
        ]
    }
];

const fetchUsers = async () => {
    isLoading.value = true;
    try {
        const response = await userApi.getUsers({
            search: searchText.value,
            currentPage: pagination.value.current,
            limit: pagination.value.pageSize,
            order_element: orderElement.value,
            order_type: orderType.value,
            filters: filters.value
        });

        users.value = response.data.original.data;
        pagination.value.total = response.data.original.total;
    } catch (error) {
        if (error?.response?.status === 403) {
            isForbidden.value = true;
            return;
        }
        console.error("Error fetching users:", error);
        $toast.error(error?.response?.data?.message || "Lỗi");
    } finally {
        isLoading.value = false;
    }
};

const handleUpdateUser = async (formData, id) => {
    try {
        const response = await userApi.update(formData, id);
        console.log(response);
        
        message.success("user update successfully!");
        isUserModalOpen.value = false;
        fetchUsers();
    } catch (error) {
        console.error("Error", error);
        message.error(error.response?.data?.message ||"Failed to update user");
    }
};

const handleSearch = async () => {
    pagination.value.current = 1;
    fetchUsers();
};

const handleTableChange = (paginationObj, filters, sorter) => {
    pagination.value = paginationObj;
    if (sorter.order) {
        orderElement.value = sorter.field;
        orderType.value = sorter.order === 'ascend' ? 'asc' : 'desc';
    }
    fetchUsers();
};

const handleDetails =  async (record) =>{
    showUserModal();
    const res = await userApi.getUserById(record.id)
    userDetails.value = res.data
}


const showUserModal = () => {
    isUserModalOpen.value = true;
};


onMounted(() => {
    fetchUsers();
});
</script>

<style scoped>
.flex {
    display: flex;
    gap: 10px;
}
</style>
