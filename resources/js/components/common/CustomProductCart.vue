<template>
  <a-card hoverable style="width: 270px">
    <template #cover>
      <img
        alt="example"
        :src="data.images[0] ? '/storage/' + data.images[0].path : 'https://gw.alipayobjects.com/zos/rmsportal/JiqGstEfoWAOHiTxclqi.png'"
        class="w-full h-48 object-cover rounded-t-lg"
      />
    </template>
    <template #actions>
      <ExclamationCircleOutlined />
      <ShoppingCartOutlined />
      <div @click="showBuyModal">
        Buy
      </div>
    </template>
    <a-card-meta :title="data.name" :description="formatPrice(data.price)">
    </a-card-meta>
  </a-card>

  <PurchaseProductModal 
      :data="data"
      :open ="isBuyModalVisible"
      @update:open ="isBuyModalVisible = $event"
  />
</template>

<script setup>
import { ShoppingCartOutlined, ExclamationCircleOutlined, EllipsisOutlined } from '@ant-design/icons-vue';
import PurchaseProductModal from '../modals/PurchaseProductModal.vue';
import { ref } from 'vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const isBuyModalVisible = ref(false);

const formatPrice = (price) => {
  if (!price) return "0đ"; 
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(price);
};

const showBuyModal = () => {
  isBuyModalVisible.value = true;
};

</script>

<style>
img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  transition: transform 0.3s ease-in-out;
}
img:hover {
  transform: scale(1.05);
}
</style>
