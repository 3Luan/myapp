<template>
    <a-card title="Products Management" style="width: 100%; height: 76.5vh;">
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

            <a-button 
                v-if="selectedRowKeys.length > 0"
                danger
                type="primary"
                @click="handleBulkDelete"
            >
                <DeleteOutlined/> Delete Selected
            </a-button>
        </div>

        <!-- Modal confirm import -->
        <a-modal
            v-model:visible="isImportModalVisible"
            title="Import Products"
            @ok="handleImportConfirm"
            @cancel="resetImport"
        >
            <div v-if="uploadProgress > 0">
                <a-progress :percent="uploadProgress" :status="uploadProgress === 100 ? 'success' : 'active'" />
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
                    :scroll="{ y: '50vh' }"
                    @change="handleTableChange"
                    :row-selection="rowSelection"
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
import { message, Modal } from "ant-design-vue";
import { EditOutlined, DeleteOutlined, PlusOutlined, ExportOutlined, ImportOutlined } from "@ant-design/icons-vue";
import productApi from '@/api/product';
import AddProductModal from '@/components/modals/AddProductModal.vue';
import UpdateProductModal from '@/components/modals/UpdateProductModal.vue';

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
const selectedRowKeys = ref([]);

const columns = [
    { title: "ID", dataIndex: "id", key: "id", sorter: true },
    { 
        title: "Image", dataIndex: "images", key: "images", sorter: true,
        customRender: ({ record }) => {
            return h('img', { src: `/storage/${record?.images[0]?.path}`, width: "85px" })
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

const rowSelection = {
    selectedRowKeys,
    onChange: (selectedKeys) => {
        selectedRowKeys.value = selectedKeys;
    }
};

const handleBulkDelete = async () => {
    if (selectedRowKeys.value.length === 0) {
        message.warning("Please select at least one product to delete");
        return;
    }

    Modal.confirm({
        title: 'Are you sure?',
        content: `Do you want to delete ${selectedRowKeys.value.length} selected product(s)?`,
        async onOk() {
            try {
                isLoading.value = true;

                const ids = [...selectedRowKeys.value];
                await productApi.delete({ids: ids});
                
                product.value = product.value.filter(
                    item => !selectedRowKeys.value.includes(item.id)
                );
                message.success("Selected products deleted successfully!");
                selectedRowKeys.value = [];
                fetchProducts();
            } catch (error) {
                console.error("Error:", error);
                message.error(error.response?.data?.message ||"Failed to delete selected products");
            } finally {
                isLoading.value = false;
            }
        },
        onCancel() {
        },
    });
};

const exportData = async () => {
    try {
        isLoading.value = true;

        const response = await productApi.getAllProducts();
        const products = response.data;

        if (!products.length) {
            message.warning("There are no products to export.");
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
        message.error(error.response?.data?.message ||"Failed to export product");
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

        const config = {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress: e => {
                if (e.lengthComputable) {
                    let percentCompleted = Math.round((e.loaded * 100) / e.total);
                    uploadProgress.value = percentCompleted;
                }
            }
        };

        await productApi.import(formData, config);

        message.success("Products imported successfully!");
        resetImport();
        fetchProducts();
    } catch (error) {
        console.error("Error:", error);
        message.error(error.response?.data?.message ||"Failed to import product");
    } finally {
        isLoading.value = false;
    }
};

const resetImport = () => {
    selectedFile.value = null;
    isImportModalVisible.value = false;
};

const handleDetails =  async (record) =>{
    showUpdateModal();
    const res = await productApi.getProductDetailsAdmin(record.id)
    productDetails.value = res.data
}

const handleDelete = async (id) => {
    try {
        isLoading.value = true;
        
        await productApi.delete({ids:[id]});
        product.value = product.value.filter(item => item.id !== id);
        selectedRowKeys.value = selectedRowKeys.value.filter(key => key !== id);
        message.success("Product deleted successfully!");
    } catch (error) {
        console.error("Error:", error);
        message.error(error.response?.data?.message ||"Failed to delete product");
    } finally {
        isLoading.value = false;
    }
};

const fetchProducts = async () => {
    isLoading.value = true;
    try {
        const response = await productApi.getProductsAdmin({
            search: searchText.value,
            currentPage: pagination.value.current,
            limit: pagination.value.pageSize,
            order_element: orderElement.value,
            order_type: orderType.value,
        });

        product.value = response.data.original.data;
        pagination.value.total = response.data.original.total;
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
        message.error(error.response?.data?.message ||"Failed to add product");
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
        message.error(error.response?.data?.message ||"Failed to update product");
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