<template>
    <Forbidden v-if="isForbidden" />
    <a-card v-else title="Role Management" style="width: 100%; height: 86vh;">
        <div class="mb-4 flex items-center justify-between">
            <!-- Search -->
            <form @submit.prevent="handleSearch">
                <div class="form-group">
                    <CustomInput v-model="searchText" type="text" placeholder="Search..." />
                    <CustomButton :type="ButtonType.PRIMARY">
                        <SearchOutlined />
                    </CustomButton>
                </div>
            </form>

            <!-- Add role -->
            <form @submit.prevent="addRole">
                <div class="form-group">
                    <CustomInput v-model="nameRole" type="text" placeholder="Enter role name..." />
                    <CustomButton :type="ButtonType.PRIMARY">
                        <PlusOutlined />
                    </CustomButton>
                </div>
            </form>
        </div>

        <!-- Table data -->
        <a-spin :spinning="isLoading" tip="Loading...">
            <CustomTable
                :data="roles"
                :columns="columns"
                :pagination="pagination"
                :loading="isLoading"
                @tableChange="handleTableChange"
            />
        </a-spin>
    </a-card>
</template>

<script setup>
import { h, onMounted, ref } from 'vue';
import { EditOutlined, DeleteOutlined, SearchOutlined, PlusOutlined } from "@ant-design/icons-vue";
import roleApi from '@/api/role';
import CustomTable from "@/components/common/CustomTable.vue"; 
import CustomButton from "@/components/common/CustomButton.vue";
import { ButtonType } from "@/constants/index.js";
import CustomInput from '@/components/common/CustomInput.vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import Forbidden from '@/components/Forbidden.vue';

const roles = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('name');
const orderType = ref('asc');
const $toast = useToast();
const nameRole = ref("");
const isForbidden = ref(false);

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { title: "Name", dataIndex: "name", key: "name", sorter: true },
    {
        title: "Action",
        key: "action",
        align: "center",
        fixed: "right",
        width: 120,
        customRender: ({ record }) => [
            h(EditOutlined, {
                style: { color: "#1890ff", marginRight: "10px", cursor: "pointer" },
                // onClick: () => handleEdit(record),
            }),
            h(DeleteOutlined, {
                style: { color: "red", cursor: "pointer" },
                // onClick: () => handleDelete(record.id),
            })
        ]
    }
];

const fetchRoles = async () => {
    isLoading.value = true;
    try {
        const response = await roleApi.getRoles({
            search: searchText.value,
            currentPage: pagination.value.current,
            limit: pagination.value.pageSize,
            order_element: orderElement.value,
            order_type: orderType.value,
        });

        roles.value = response.data.data;
        pagination.value.total = response.data.total;
    } catch (error) {
        if (error?.response?.status === 403) {
            isForbidden.value = true;
            return;
        }
        $toast.error(error?.response?.data?.message || "Lỗi");
        console.error("Error:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleTableChange = (data) => {
    pagination.value.current = data.current;
    if (data.orderElement) {
        orderElement.value = data.orderElement;
        orderType.value = data.orderType;
    }
    fetchRoles();
};

const handleSearch = async () => {
    pagination.value.current = 1;
    fetchRoles();
};

const addRole = async () => {
    try {
        const response = await roleApi.addRole(nameRole.value);
        const newRole = response.data.role;
        isLoading.value = true;

        roles.value = [newRole, ...roles.value];
        pagination.value.total += 1;
        nameRole.value = "";

        $toast.success(response.data.message);
    } catch (error) {
        console.error("Error:", error);
        $toast.error(error?.response?.data?.message || "Lỗi");
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchRoles();
});
</script>


<style scoped>
.flex {
    display: flex;
    gap: 10px;
}

.form-group {
    display: flex;
    align-items: center;
}
</style>