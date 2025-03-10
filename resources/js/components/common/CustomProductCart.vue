<template>
  <a-card hoverable class="product-card">
    <template #cover>
      <img
        alt="product"
        :src="data.images[0] ? '/storage/' + data.images[0].path : 'https://gw.alipayobjects.com/zos/rmsportal/JiqGstEfoWAOHiTxclqi.png'"
        class="product-image"
      />
    </template>
    <a-card-meta :title="data.name" :description="formatPrice(data.price)" class="card-meta">
      <template #description>
        <p class="price-text">{{ formatPrice(data.price) }}</p>
      </template>
    </a-card-meta>
    <template #actions>
      <ExclamationCircleOutlined class="action-icon" />
      <ShoppingCartOutlined class="action-icon" @click="showAddModal" />
      <a-button type="primary" @click="showBuyModal" class="buy-button">Mua ngay</a-button>
    </template>

    <PurchaseProductModal
      :data="data"
      :open="isBuyModalVisible"
      @update:open="isBuyModalVisible = $event"
    />
    <AddCardModal
      :data="data"
      :open="isAddModalVisible"
      @update:open="isAddModalVisible = $event"
    />
  </a-card>
</template>

<script setup>
import { ShoppingCartOutlined, ExclamationCircleOutlined, EllipsisOutlined } from '@ant-design/icons-vue';
import PurchaseProductModal from '@/components/modals/PurchaseProductModal.vue';
import { ref } from 'vue';
import AddCardModal from '@/components/modals/AddCardModal.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const isBuyModalVisible = ref(false);
const isAddModalVisible = ref(false);

const formatPrice = (price) => {
  if (!price) return "0đ"; 
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(price);
};

const showBuyModal = () => {
  isBuyModalVisible.value = true;
};

const showAddModal = () => {
  isAddModalVisible.value = true;
};

</script>

<style scoped>
.product-card {
  width: 280px;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.product-card:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.product-image {
  width: 100%;
  height: 220px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.1);
}

.card-meta {
  padding: 15px;
}

.price-text {
  font-size: 1.2rem;
  font-weight: 600;
  color: #ff4d4f;
  margin: 0;
}

.action-icon {
  font-size: 1.2rem;
  color: #595959;
  transition: color 0.3s ease;
}

.action-icon:hover {
  color: #1890ff;
}

.buy-button {
  border-radius: 20px;
  padding: 5px 5px;
  font-weight: 500;
}
</style>
