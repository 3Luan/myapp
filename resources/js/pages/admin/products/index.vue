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
            <a-button type="primary" @click="showAddModal">
                <PlusOutlined/>
            </a-button>

            <!-- Export Product Button -->
            <a-button type="primary" @click="exportData">
                <ExportOutlined/> Export 
            </a-button>

            <!-- Import Product Button -->
            <a-upload
                :before-upload="handleBeforeUpload"
                :show-upload-list="false"
                accept=".csv"
            >
                <a-button type="primary">
                    <ImportOutlined/> Import
                </a-button>
            </a-upload>
        </div>

        <!-- Modal xác nhận import -->
        <a-modal
            v-model:visible="isImportModalVisible"
            title="Import Products"
            @ok="handleImportConfirm"
            @cancel="resetImport"
        >
            <div v-if="uploadProgress > 0">
                <a-progress :percent="uploadProgress" status="active" />
            </div>
            <p v-if="selectedFile">Selected file: {{ selectedFile.name }}</p>
            <p v-else>Please select a CSV file to import</p>
        </a-modal>

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
import { EditOutlined, DeleteOutlined, PlusOutlined, ExportOutlined, ImportOutlined } from "@ant-design/icons-vue";
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

const isImportModalVisible = ref(false);
const selectedFile = ref(null);
const uploadProgress = ref(0);

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { 
        title: "Image", dataIndex: "images", key: "images", sorter: true,
        customRender: ({ record }) => {
            return h('img', { src: `/storage/${record.images[0].path}`, width: "85px" })
        }
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

const exportData = async () => {
    try {
        isLoading.value = true;

        const response = await productApi.getAllProducts();
        const products = response.data;

        if (!products.length) {
            message.warning("Không có sản phẩm để xuất.");
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID,Name,Price,Rate,Count,Description,Image\n";

        products.forEach(product => {
            csvContent += `${product.id},${product.name},${product.price},${product.rate},${product.count},${product.description},${product?.images[0]?.path || null}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "products.csv");
        document.body.appendChild(link);
        link.click();

        message.success("Export file CSV success!");
    } catch (error) {
        console.error("Error:", error);
        message.error("Failed to export product");
    } finally {
        isLoading.value = false;
    }
};

const handleBeforeUpload = (file) => {
    selectedFile.value = file;
    isImportModalVisible.value = true;
    uploadProgress.value = 0;
    return false;
};


const handleImportConfirm = async () => {
    if (!selectedFile.value) {
        message.error("Please select a file first");
        return;
    }

    try {
        isLoading.value = true;
        let formData = new FormData();
        formData.append("file", selectedFile.value);

        await productApi.import(formData);

        message.success("Products imported successfully!");
        resetImport();
        fetchProducts();
    } catch (error) {
        console.error("Error:", error);
        message.error("Failed to import product");
    } finally {
        isLoading.value = false;
    }
};


const resetImport = () => {
    selectedFile.value = null;
    isImportModalVisible.value = false;
};

const importData = async (file) => {
    try {
        isLoading.value = true;
        let formData = new FormData();

        formData.append("file", file);
        const response = await productApi.import(formData);
        console.log("response",response);

    } catch (error) {
        console.error("Error:", error);
        message.error("Failed to import product");
    } finally {
        isLoading.value = false;
    }
};



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

const handleAddProduct = async (data) => {
    if (!data.name || !data.price) {
        message.error("Please fill all required fields");
        return;
    }
    console.log("data.image",data);

    let formData = new FormData();
    formData.append("name", data.name);
    formData.append("price", data.price);
    formData.append("images", data.images);
    formData.append("description", data.description);

    data.images.forEach((image) => {
        formData.append("images[]", image);
    });

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

const handleUpdateProduct = async (data, idDeleteList, id) => {
    if (!data.name || !data.price) {
        message.error("Please fill all required fields");
        return;
    }

    let formData = new FormData();
    formData.append("name", data.name);
    formData.append("price", data.price);
    formData.append("description", data.description);
    data.images.forEach((image) => {
        if(image.file){
            formData.append("images[]", image.file);
        }
    });

    if (idDeleteList.length > 0) {
        idDeleteList.forEach((idDeleteId) => {
            formData.append("idDeleteId[]", idDeleteId);
        });
    } else {
        formData.append("idDeleteId[]", []);
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