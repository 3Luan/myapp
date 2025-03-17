<template>
  <div class="cart-container">
    <header class="header">
      <h1>Cart</h1>
      <a-input v-model:value="searchText" placeholder="Search..." @input="handleSearch" style="width: 300px; margin-bottom: 10px;" />
    </header>

    <a-spin :spinning="isLoading" tip="Loading...">
      <a-table
        :columns="columns"
        :data-source="filteredCartItems"
        row-key="id"
        :pagination="pagination"
        bordered
        :row-selection="{
          selectedRowKeys: selectedKeys.map(item => item.id),
          onChange: handleSelectChange
        }"
        @change="handleTableChange"
      >
        <template #image="{ record }">
          <img :src="`/storage/${record?.product?.images[0]?.path}`" alt="Product Image" class="product-image" />
        </template>

        <template #name="{ record }">
          {{ record?.product?.name}}
        </template>

        <template #price="{ record }">
          {{ formatPrice(record?.product?.price) }}
        </template>

        <template #quantity="{ record }">
          <a-input-number v-model:value="record.count" :min="1" :max="record?.product?.count" @change="updateQuantity(record)" />
        </template>

        <template #total="{ record }">
          {{ formatPrice(record?.product?.price * record.count) }}
        </template>

        <template #action="{ record }">
          <a-button type="primary" danger @click="removeFromCart(record)">Delete</a-button>
        </template>
      </a-table>
    </a-spin>

    <div class="cart-footer">
      <a-checkbox v-model:checked="selectAll" @change="toggleSelectAll">Select all</a-checkbox>
      <h4>Total: {{ formatPrice(selectedTotalPrice) }}</h4>
      <a-button type="primary" size="large" @click="checkout" :disabled="selectedKeys.length === 0">Pay</a-button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, h, watch } from 'vue';
import { message } from 'ant-design-vue';
import cartApi from '@/api/cart';
import orderApi from '@/api/order';
import store from '@/store';

const cartItems = ref([]);
const isLoading = ref(false);
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const searchText = ref('');
const selectedKeys = ref([]);
const selectAll = ref(false);
const orderElement = ref('id');
const orderType = ref('desc');

const columns = [
  { title: "Image", key: "image", slots: { customRender: 'image' }, width: 100 },
  { title: "Name", key: "name", slots: { customRender: 'name' } },
  { title: 'Price', key: 'price', slots: { customRender: 'price' }, width: 150 },
  { title: 'Count', key: 'quantity', slots: { customRender: 'quantity' }, width: 120 },
  { title: 'Total', key: 'total', slots: { customRender: 'total' }, width: 150 },
  { title: 'Action', key: 'action', slots: { customRender: 'action' }, width: 120 }
];

watch(
  () => store.getters["cart/cart"], 
  (newCart) => {
    fetchCart()
  },
  { deep: true }
);

const fetchCart = async () => {
  isLoading.value = true;
  try {
    if (!store.getters["cart/cart"]) {
      await store.dispatch("cart/getCarts", {
        search: searchText.value,
        currentPage: pagination.value.current,
        limit: pagination.value.pageSize,
        order_element: orderElement.value,
        order_type: orderType.value,
      });
    } else {
      cartItems.value = [...store.getters["cart/cart"].data];
      pagination.value.total = store.getters["cart/cart"].total;
    }
  } catch (error) {
    message.error(error.response?.data?.message ||'Unable to load cart');
  } finally {
    isLoading.value = false;
  }
};

const handleTableChange = (pag) => {
  pagination.value.current = pag.current;
  pagination.value.pageSize = pag.pageSize;
  fetchCart();
};

const filteredCartItems = computed(() => {
  if (!cartItems.value || !Array.isArray(cartItems.value)) return [];
  if (!searchText.value) return [...cartItems.value];
  return cartItems.value.filter(item =>
    item.product?.name?.toLowerCase().includes(searchText.value.toLowerCase())
  );
});

const handleSearch = () => {
  pagination.value.current = 1;
  fetchCart();
};

const handleSelectChange = (keys) => {
  selectedKeys.value = cartItems.value
    .filter(item => keys.includes(item.id))
    .map(item => {
      return {
      id: item.id,
      product_id: item.product_id,
      count: item.count
    }});
  
  selectAll.value = selectedKeys.value.length === filteredCartItems.value.length;
};

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedKeys.value = filteredCartItems.value.map(item => ({
      id: item.id,
      product_id: item.product_id,
      count: item.count
    }));
  } else {
    selectedKeys.value = [];
  }
};

// Total Price
const selectedTotalPrice = computed(() => {
  return cartItems.value
    .filter(item => selectedKeys.value.some(selected => selected.id === item.id))
    .reduce((sum, item) => sum + item.product.price * item.count, 0);
});

const updateQuantity = async (item) => {
  try {
    await cartApi.updateCart({cart_id: item.id, count: item.count });
  } catch (error) {
    console.error(error);
  }
};

// Delete
const removeFromCart = async (item) => {
  try {
    await store.dispatch("cart/removeCard", {cart_id : item.id});
  } catch (error) {
    message.error(error.response?.data?.message ||'Cannot delete product');
  }
};

// Checkout
const checkout = async () => {
  try {
    if (!selectedKeys.value || selectedKeys.value.length === 0) {
      message.warning('Please select at least one product to checkout');
      return;
    }

    const payload = {
      products: selectedKeys.value.map(item => ({
        cart_id: item.id,
        product_id: item.product_id,
        count: item.count
      }))
    };

    if (!payload.products || payload.products.length === 0) {
      message.error("There are no valid products to checkout.");
      return;
    }

    await store.dispatch("cart/orderCarts", payload);
    selectedKeys.value = [];
  } catch (error) {
    console.error("Error:", error);
    message.error(error.response?.data?.message || "There was an error while ordering..");
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

onMounted(() => {
  fetchCart();
});
</script>

<style scoped>
.cart-container {
  max-width: 1200px;
  margin: 20px auto;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.product-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}
.cart-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding-top: 10px;
  border-top: 1px solid #f0f0f0;
}
</style>
