<template>
    <a-modal
        :visible="open"
        title="Products in Discount"
        @ok="closeModal"
        @cancel="closeModal"
        width="1000px"
        ok-text="Close"
        :cancel-button-props="{ style: { display: 'none' } }"
    >
        <a-row :gutter="16">
            <!-- All Products Table -->
            <a-col :span="12">
                <h3>All Products</h3>
                <a-row :gutter="16" style="margin-bottom: 16px">
                    <a-col :span="12">
                        <a-input
                            v-model:value="searchAllQuery"
                            placeholder="Search all products..."
                            style="width: 100%"
                            allow-clear
                        />
                    </a-col>
                    <a-col :span="12">
                        <a-button
                            type="primary"
                            @click="addSelectedProducts"
                            :disabled="!selectedAllKeys || selectedAllKeys.length === 0"
                            style="margin-right: 8px"
                        >
                            Add Selected ({{ selectedAllKeys ? selectedAllKeys.length : 0 }})
                        </a-button>
                        <a-button
                            type="dashed"
                            @click="addAllProducts"
                            :disabled="!filteredAllProducts || filteredAllProducts.length === 0"
                        >
                            Add All
                        </a-button>
                    </a-col>
                </a-row>
                <a-table
                    :columns="columns"
                    :data-source="filteredAllProducts || []"
                    :pagination="{ pageSize: 5 }"
                    size="small"
                    :scroll="{ y: 240 }"
                    rowKey="id"
                    :row-selection="allRowSelection"
                />
            </a-col>

            <!-- Applied Products Table -->
            <a-col :span="12">
                <h3>Applied Products</h3>
                <a-row :gutter="16" style="margin-bottom: 16px">
                    <a-col :span="12">
                        <a-input
                            v-model:value="searchAppliedQuery"
                            placeholder="Search applied products..."
                            style="width: 100%"
                            allow-clear
                        />
                    </a-col>
                    <a-col :span="12">
                        <a-button
                            type="danger"
                            @click="removeSelectedProducts"
                            :disabled="!selectedAppliedKeys || selectedAppliedKeys.length === 0"
                        >
                            Remove Selected ({{ selectedAppliedKeys ? selectedAppliedKeys.length : 0 }})
                        </a-button>
                    </a-col>
                </a-row>
                <a-table
                    :columns="columns"
                    :data-source="filteredAppliedProducts || []"
                    :pagination="{ pageSize: 5 }"
                    size="small"
                    :scroll="{ y: 240 }"
                    rowKey="id"
                    :row-selection="appliedRowSelection"
                />
            </a-col>
        </a-row>
    </a-modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import discountApi from '@/api/discount';
import productApi from '@/api/product';
import { message } from 'ant-design-vue';

const props = defineProps(['open', 'discount']);
const emit = defineEmits(['update:open']);

const searchAllQuery = ref('');
const searchAppliedQuery = ref('');
const allProducts = ref([]);
const appliedProducts = ref([]);
const selectedAllKeys = ref([]);
const selectedAppliedKeys = ref([]);

const columns = [
    { title: 'ID', dataIndex: 'id', key: 'id', sorter: (a, b) => a.id - b.id },
    { title: 'Name', dataIndex: 'name', key: 'name', sorter: (a, b) => a.name.localeCompare(b.name) },
];

// Row selection for all products
const allRowSelection = computed(() => ({
    selectedRowKeys: selectedAllKeys.value,
    onChange: (selectedKeys) => {
        selectedAllKeys.value = selectedKeys;
    },
}));

// Row selection for applied products
const appliedRowSelection = computed(() => ({
    selectedRowKeys: selectedAppliedKeys.value,
    onChange: (selectedKeys) => {
        selectedAppliedKeys.value = selectedKeys;
    },
}));

// Fetch all products
const fetchAllProducts = async () => {
    try {
        const response = await productApi.getProductsAdmin({});
        allProducts.value = response.data.original.data.map(product => ({
            id: product.id,
            name: product.name,
        }));
    } catch (error) {
        console.error('Error fetching all products:', error);
        message.error('Failed to load all products');
        allProducts.value = [];
    }
};

// Fetch products in the discount
const fetchAppliedProducts = async (id) => {
    console.log('Fetching applied products for discount ID:', id);
    try {
        const response = await discountApi.getProductsInDiscount(id);
        console.log(response);
        appliedProducts.value = response.data.data.map(product => ({
            id: product.id,
            name: product.name,
        }));
    } catch (error) {
        console.error('Error fetching applied products:', error);
        message.error('Failed to load applied products');
        appliedProducts.value = []; // Fallback to empty array
    }
};

// Filter all products (exclude applied products)
const filteredAllProducts = computed(() => {
    if (!allProducts.value) return [];
    const appliedIds = (appliedProducts.value || []).map(p => p.id);
    const filtered = allProducts.value.filter(product => !appliedIds.includes(product.id));
    if (!searchAllQuery.value) return filtered;
    return filtered.filter(product => 
        product.name.toLowerCase().includes(searchAllQuery.value.toLowerCase())
    );
});

// Filter applied products
const filteredAppliedProducts = computed(() => {
    if (!appliedProducts.value) return [];
    if (!searchAppliedQuery.value) return appliedProducts.value;
    return appliedProducts.value.filter(product => 
        product.name.toLowerCase().includes(searchAppliedQuery.value.toLowerCase())
    );
});

// Add selected products to discount
const addSelectedProducts = async () => {
    if (!selectedAllKeys.value.length || !props.discount?.id) return;

    const formData = new FormData();
    selectedAllKeys.value.forEach(id => formData.append('product_ids[]', id));

    try {
        await discountApi.addProductsToDiscount(formData, props.discount.id);
        await fetchAppliedProducts(props.discount.id);
        selectedAllKeys.value = [];
        message.success('Selected products added successfully');
    } catch (error) {
        console.error('Error adding selected products:', error);
        message.error('Failed to add selected products');
    }
};

// Add all available products to discount
const addAllProducts = async () => {
    if (!filteredAllProducts.value.length || !props.discount?.id) return;

    const formData = new FormData();
    filteredAllProducts.value.forEach(product => formData.append('product_ids[]', product.id));

    try {
        await discountApi.addProductsToDiscount(formData, props.discount.id);
        await fetchAppliedProducts(props.discount.id);
        selectedAllKeys.value = [];
        message.success('All products added successfully');
    } catch (error) {
        console.error('Error adding all products:', error);
        message.error('Failed to add all products');
    }
};

// Remove selected products from discount
const removeSelectedProducts = async () => {
    if (!selectedAppliedKeys.value.length || !props.discount?.id) return;

    const formData = new FormData();
    selectedAppliedKeys.value.forEach(id => formData.append('product_ids[]', id));

    try {
        await discountApi.removeProductsFromDiscount(formData, props.discount.id);
        await fetchAppliedProducts(props.discount.id);
        selectedAppliedKeys.value = [];
        message.success('Selected products removed successfully');
    } catch (error) {
        console.error('Error removing selected products:', error);
        message.error('Failed to remove selected products');
    }
};

// Watch for discount changes
watch(() => props.discount, (data) => {
    if (data) {
        fetchAppliedProducts(data.id);
        fetchAllProducts();
    }
}, { immediate: true });

const closeModal = () => {
    emit('update:open', false);
    searchAllQuery.value = '';
    searchAppliedQuery.value = '';
    selectedAllKeys.value = [];
    selectedAppliedKeys.value = [];
};
</script>

<style scoped>
.ant-table {
    margin-bottom: 16px;
}
h3 {
    margin-bottom: 8px;
}
</style>