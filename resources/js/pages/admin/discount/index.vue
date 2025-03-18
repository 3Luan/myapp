<template>
    <a-card title="Discount Management" style="width: 100%; height: 76.5vh;">
        <div class="mb-4 flex items-center justify-between">
            <!-- Search -->
            <a-input-search 
                v-model:value="searchText"
                placeholder="Search discounts..."
                enter-button
                @search="handleSearch"
                style="max-width: 300px;"
            />
            
            <!-- Add Discount Button -->
            <a-button type="primary" @click="showAddModal">
                <PlusOutlined/> Add Discount
            </a-button>
        </div>

        <a-spin :spinning="isLoading" tip="Loading...">
            <div class="table-wrapper">
                <a-table
                    :dataSource="discounts"
                    :columns="columns"
                    :pagination="pagination"
                    :loading="isLoading"
                    rowKey="id"
                    bordered
                    :scroll="{ y: '50vh' }"
                    @change="handleTableChange"
                    :row-selection="rowSelection"
                />
            </div>
        </a-spin>

        <AddDiscountModal 
            :data="discountDetails"
            :open="isAddModalVisible"
            :reset="resetForm"
            @update:open="isAddModalVisible = $event"
            @addDiscount="handleAddDiscount" 
        />

        <UpdateDiscountModal 
            :open="isUpdateModalOpen"
            :data="discountDetails"
            :reset="resetForm"
            @update:open="isUpdateModalOpen = $event"
            @updateDiscount="handleUpdateDiscount" 
        />

        <ViewDiscountProductsModal
            :open="isViewProductsModalOpen"
            :discount="selectedDiscount"
            @update:open="isViewProductsModalOpen = $event"
        />
    </a-card>
</template>

<script setup>
import { h, ref, onMounted } from 'vue';
import { message } from "ant-design-vue";
import { EditOutlined, DeleteOutlined, PlusOutlined, EyeOutlined } from "@ant-design/icons-vue";
import discountApi from '@/api/discount';
import AddDiscountModal from '@/components/modals/AddDiscountModal.vue';
import UpdateDiscountModal from '@/components/modals/UpdateDiscountModal.vue';
import ViewDiscountProductsModal from '@/components/modals/ViewDiscountProductsModal.vue';
import dayjs from "dayjs";

const discounts = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const isAddModalVisible = ref(false);
const isUpdateModalOpen = ref(false);
const isViewProductsModalOpen = ref(false);
const resetForm = ref(false);
const discountDetails = ref({});
const selectedRowKeys = ref([]);
const selectedDiscount = ref(null);

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { title: "Name", dataIndex: "name", key: "name", sorter: true },
    { 
        title: "Percent", 
        dataIndex: "percent", 
        key: "percent", 
        sorter: true,
        customRender: ({ text }) => `${parseInt(text)}%`
    },
    { 
        title: "Start Date", 
        dataIndex: "start_date", 
        key: "start_date", 
        sorter: true,
        customRender: ({ text }) => dayjs(text).format("DD/MM/YYYY HH:mm")
    },
    { 
        title: "End Date", 
        dataIndex: "end_date", 
        key: "end_date", 
        sorter: true,
        customRender: ({ text }) => dayjs(text).format("DD/MM/YYYY HH:mm")
    },
    {
        title: "Action",
        key: "action",
        align: "center",
        fixed: "right",
        width: 150,
        customRender: ({ record }) => [
            h(EyeOutlined, {
                style: { color: "#52c41a", marginRight: "10px", cursor: "pointer" },
                onClick: () => handleViewProducts(record),
            }),
            h(EditOutlined, {
                style: { color: "#1890ff", marginRight: "10px", cursor: "pointer" },
                onClick: () => handleDetails(record),
            }),
        ]
    }
];

const rowSelection = {
    selectedRowKeys,
    onChange: (selectedKeys) => {
        selectedRowKeys.value = selectedKeys;
    }
};

const handleAddDiscount = async (formData) => {
    try {
        isLoading.value = true;
        const response = await discountApi.create(formData);
        console.log(response);
        
        message.success("Discount added successfully!");
        isAddModalVisible.value = false;
        fetchDiscounts();
    } catch (error) {
        console.error("Error:", error);
        message.error(error.response?.data?.message || "Failed to add discount");
    } finally {
        isLoading.value = false;
    }
};

const handleUpdateDiscount = async (formData, id) => {
    try {
        isLoading.value = true;
        const response = await discountApi.update(formData, id);
        console.log(response);
        
        discounts.value = discounts.value.map(discount => 
            discount.id === id ? response.data.discount : discount
        );

        message.success("Discount updated successfully!");
        isUpdateModalOpen.value = false;
    } catch (error) {
        console.error("Error:", error);
        message.error(error.response?.data?.message || "Failed to update discount");
    } finally {
        isLoading.value = false;
    }
};

const handleSearch = async () => {
    pagination.value.current = 1;
    fetchDiscounts();
};

const handleTableChange = (paginationObj, filters, sorter) => {
    pagination.value = paginationObj;
    fetchDiscounts();
};

const showAddModal = () => {
    discountDetails.value = {};
    isAddModalVisible.value = true;
};

const handleDetails = async (record) => {
    isUpdateModalOpen.value = true;
    const res = await discountApi.getDiscountById(record.id);
    console.log(res);
    discountDetails.value = res.data.data;
};

const handleViewProducts = (record) => {
    selectedDiscount.value = record;
    isViewProductsModalOpen.value = true;
};

const fetchDiscounts = async () => {
    isLoading.value = true;
    try {
        const response = await discountApi.getDiscounts({ search: searchText.value });
        discounts.value = response?.data?.data?.data;
        pagination.value.total = response?.data?.data?.total;
    } catch (error) {
        console.error("Error:", error);
        message.error("Failed to fetch discounts");
    } finally {
        isLoading.value = false;
        resetForm.value = false;
    }
};

onMounted(() => {
    fetchDiscounts();
});
</script>

<style scoped>
.flex {
    display: flex;
    gap: 10px;
}
</style>