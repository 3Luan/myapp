<template>
    <a-card title="Role Management" style="width: 100%; height: 86vh;">
        <div class="mb-4 flex items-center justify-between">
            <!-- Search -->
            <a-input-search 
                v-model:value="searchText"
                placeholder="Search..."
                enter-button
                @search="handleSearch"
                style="max-width: 300px;"
            />
            <!-- <Modal/> -->
            <form @submit.prevent="addRole">
                <div
                    class="input-group input-group-sm" style="max-width: 250px;">
                    <input
                        type="text"
                        class="form-control"
                        id="nameRole"
                        v-model="nameRole"
                        placeholder="Enter role name..."
                        required
                    />
                    <button 
                        type="submit"
                        class="btn btn-primary"
                        :disabled="isLoading">
                        <PlusOutlined />
                    </button>
                </div>
            </form>
        </div>

        <a-spin :spinning="isLoading" tip="Loading...">
            <div class="table-wrapper">
                <a-table
                    :dataSource="roles"
                    :columns="columns"
                    :pagination="pagination"
                    :loading="isLoading"
                    rowKey="id"
                    bordered
                    :scroll="{ y: '55vh' }"
                    @change="handleTableChange"
                />
            </div>
        </a-spin>
    </a-card>
</template>

<script setup>
import { h, ref } from 'vue';
import { EditOutlined, DeleteOutlined, PlusOutlined } from "@ant-design/icons-vue";
import roleApi from '../../../api/role';
import Modal from '../../../components/Modal.vue';


const roles = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('name');
const orderType = ref('asc');
const filters = ref({ role: [], gender: [] });

const nameRole = ref("");

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
            filters: filters.value
        });

        console.log("res", response);
        

        roles.value = response.data.data;
        pagination.value.total = response.data.total;
    } catch (error) {
        console.error("Error fetching roles:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleSearch = async () => {
    pagination.value.current = 1;
    fetchRoles();
};

const handleTableChange = (paginationObj, filters, sorter) => {
    pagination.value = paginationObj;
    if (sorter.order) {
        orderElement.value = sorter.field;
        orderType.value = sorter.order === 'ascend' ? 'asc' : 'desc';
    }
    fetchRoles();
};

const addRole = async () => {
    isLoading.value = true;
    try {
        const response = await roleApi.addRole(nameRole.value);
        const newRole = response.data.role;
        roles.value = [newRole, ...roles.value];
        pagination.value.total += 1;
    } catch (error) {
        console.error("Error adding role:", error);
    } finally {
        isLoading.value = false;
    }
};


fetchRoles();
</script>

<style scoped>
.flex {
    display: flex;
    gap: 10px;
}
</style>
