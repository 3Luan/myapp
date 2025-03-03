<template>
    <a-card title="Products Management" style="width: 100%; height: 86vh;">
        <div class="mb-4 flex items-center justify-between">
            <!-- Search -->
            <a-input-search 
                v-model:value="searchText"
                placeholder="Search..."
                enter-button
                @search="handleSearch"
                style="max-width: 300px;"
            />
            
            <!-- Add Product Button -->
            <a-button type="primary" @click="showAddModal">+</a-button>
        </div>

        <a-spin :spinning="isLoading" tip="Loading...">
            <div class="table-wrapper">
                <a-table
                    :dataSource="product"
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

        <AddProductModal 
            :data="productDetails"
            :open ="isAddModalVisible"
            :reset="resetForm"
            @update:open ="isAddModalVisible = $event"
            @addProduct="handleAddProduct" 
        />

        <UpdateProductModal 
            :open="isUpdateModalOpen"
            :data="productDetails"
            :reset="resetForm"
            @update:open="isUpdateModalOpen = $event"
            @updateProduct="handleUpdateProduct" 
        />
    </a-card>
</template>

<script setup>
import { h, ref, onMounted } from 'vue';
import { message } from "ant-design-vue";
import { EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import productApi from '../../../api/product';
import AddProductModal from '../../../components/modals/AddProductModal.vue';
import UpdateProductModal from '../../../components/modals/UpdateProductModal.vue';

const product = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('name');
const orderType = ref('asc');
const isAddModalVisible = ref(false);
const isUpdateModalOpen = ref(false);
const resetForm = ref(false);
const productDetails = ref("");

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { 
        title: "Image", dataIndex: "image", key: "image", sorter: true,
        customRender: ({ record }) => h('img', { src: `/storage/${record.image}`, width: "85px" })
    },
    { title: "Name", dataIndex: "name", key: "name", sorter: true },
    { title: "Price", dataIndex: "price", key: "price", sorter: true },
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
                onClick: () => handleDelete(record.id),
            })
        ]
    }
];

const handleDetails =  async (record) =>{
    showUpdateModal();
    const res = await productApi.getProductDetails(record.id)
    productDetails.value = res.data
}

const handleDelete =  async (id) =>{
    try {
        isLoading.value = true;
        await productApi.delete(id);
        product.value = product.value.filter(item => item.id !== id);
    } catch (error) {
        console.error("Error:", error);
        message.error("Failed to delete product");
    } finally {
        isLoading.value = false;
    }
}

const fetchProducts = async () => {
    isLoading.value = true;
    try {
        const response = await productApi.getProducts({
            search: searchText.value,
            currentPage: pagination.value.current,
            limit: pagination.value.pageSize,
            order_element: orderElement.value,
            order_type: orderType.value,
        });

        product.value = response.data.data;
        pagination.value.total = response.data.total;
    } catch (error) {
        console.error("Error:", error);
    } finally {
        isLoading.value = false;
        resetForm.value = false;
    }
};

const handleSearch = async () => {
    pagination.value.current = 1;
    fetchProducts();
};

const handleTableChange = (paginationObj, filters, sorter) => {
    pagination.value = paginationObj;
    if (sorter.order) {
        orderElement.value = sorter.field;
        orderType.value = sorter.order === 'ascend' ? 'asc' : 'desc';
    }
    fetchProducts();
};

const showAddModal = () => {
    isAddModalVisible.value = true;
};

const showUpdateModal = () => {
    isUpdateModalOpen.value = true;
};

const handleAddProduct = async (data) => {
    if (!data.name || !data.price) {
        message.error("Please fill all required fields");
        return;
    }

    let formData = new FormData();
    formData.append("name", data.name);
    formData.append("price", data.price);
    formData.append("image", data.image);
    formData.append("description", data.description);

    try {
        await productApi.addProduct(formData);
        message.success("Product added successfully!");
        isAddModalVisible.value = false;
        resetForm.value = true;
        
        fetchProducts();
    } catch (error) {
        console.error("Error", error);
        message.error("Failed to add product");
    }
};

const handleUpdateProduct = async (data, id) => {
    if (!data.name || !data.price) {
        message.error("Please fill all required fields");
        return;
    }

    let formData = new FormData();
    formData.append("name", data.name);
    formData.append("price", data.price);
    formData.append("description", data.description);
    if(data.image){
        formData.append("image", data.image);
    }

    try {
        await productApi.update(formData, id);
        message.success("Product update successfully!");
        isUpdateModalOpen.value = false;
        
        fetchProducts();
    } catch (error) {
        console.error("Error", error);
        message.error("Failed to update product");
    }
};

onMounted(() => {
    fetchProducts();
});
</script>

<style scoped>
.flex {
    display: flex;
    gap: 10px;
}
</style>