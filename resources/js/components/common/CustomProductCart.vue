<template>
  <a-card hoverable class="product-card">
    <template #cover>
      <div class="image-container">
        <img
          alt="product"
          :src="data.images[0] ? '/storage/' + data.images[0].path : 'https://gw.alipayobjects.com/zos/rmsportal/JiqGstEfoWAOHiTxclqi.png'"
          class="product-image"
          @click="goDetails"
        />
        <a-tag v-if="activeDiscount" color="red" class="discount-badge">
          -{{ parseInt(activeDiscount.percent) }}%
        </a-tag>
      </div>
    </template>
    <a-card-meta :title="data.name" class="card-meta">
      <template #description>
        <div v-if="activeDiscount" class="price-container">
          <p class="original-price">{{ formatPrice(data.price) }}</p>
          <p class="discounted-price">{{ formatPrice(calculateDiscountedPrice(data.price, activeDiscount.percent)) }}</p>
        </div>
        <p v-else class="price-text">{{ formatPrice(data.price) }}</p>
      </template>
    </a-card-meta>
    <template #actions>
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
import { ShoppingCartOutlined } from '@ant-design/icons-vue';
import PurchaseProductModal from '@/components/modals/PurchaseProductModal.vue';
import { ref, computed } from 'vue';
import AddCardModal from '@/components/modals/AddCardModal.vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const isBuyModalVisible = ref(false);
const isAddModalVisible = ref(false);
const router = useRouter();

const activeDiscount = computed(() => {
  if (!props.data.discounts || props.data.discounts.length === 0) return null;

  const now = new Date();

  console.log(props.data.discounts);

  const validDiscounts = props.data.discounts.filter(discount => {
    const start = new Date(discount.start_date);
    const end = new Date(discount.end_date);
    return start <= now && now <= end;
  });

  if (validDiscounts.length > 0) {
    return validDiscounts.reduce((maxDiscount, currentDiscount) =>
      currentDiscount.percent > maxDiscount.percent ? currentDiscount : maxDiscount
    );
  }

  return props.data.discounts[0];
});


const formatPrice = (price) => {
  if (!price) return "0đ";
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(price);
};

const calculateDiscountedPrice = (price, percent) => {
  const discountPercent = parseFloat(percent) / 100;
  return price * (1 - discountPercent);
};

const showBuyModal = () => {
  isBuyModalVisible.value = true;
};

const showAddModal = () => {
  isAddModalVisible.value = true;
};

const goDetails = () => {
  router.push(`/product/${props.data.id}`);
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

.image-container {
  position: relative;
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

.discount-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  font-weight: bold;
  font-size: 14px;
  padding: 2px 8px;
}

.card-meta {
  padding: 15px;
}

.price-container {
  display: flex;
  flex-direction: column;
}

.price-text {
  font-size: 1.2rem;
  font-weight: 600;
  color: #ff4d4f;
  margin: 0;
}

.original-price {
  font-size: 1rem;
  color: #999;
  text-decoration: line-through;
  margin: 0;
}

.discounted-price {
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