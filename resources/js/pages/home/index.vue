<template>
  <div class="home-container" @scroll="handleScroll">
    <header class="header">
      <div class="search-bar">
        <a-input-search
          v-model:value="searchText"
          placeholder="Tìm kiếm sản phẩm..."
          enter-button="Tìm"
          @search="fetchProducts(true, true)"
          class="search-input"
        />
        <a-select 
          v-model:value="sortBy" 
          @change="fetchProducts(true, true)" 
          class="filter-select"
          dropdownClassName="custom-dropdown"
        >
          <a-select-option value="default">Mặc định</a-select-option>
          <a-select-option value="price-asc">Giá: Thấp đến Cao</a-select-option>
          <a-select-option value="price-desc">Giá: Cao đến Thấp</a-select-option>
        </a-select>
      </div>
    </header>

    <a-spin :spinning="isLoading" tip="Đang tải...">
      <section class="products-grid">
        <CustomCart 
          v-for="product in products" 
          :key="product.id" 
          :data="product" 
          class="product-card"
        />
      </section>
      <div v-if="isLoadingMore" class="loading-more">
        <a-spin size="small" /> Đang tải thêm...
      </div>
    </a-spin>
  </div>
</template>

<script setup>

import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import CustomCart from '@/components/common/CustomProductCart.vue';
import productApi from '@/api/product';
import { message } from 'ant-design-vue';

const router = useRouter();
const route = useRoute();
const searchText = ref(route.query.search || '');
const sortBy = ref(route.query.sort || 'default');
const isLoading = ref(false);
const isLoadingMore = ref(false);
const products = ref([]);
const pagination = ref({ current: 1, pageSize: 20, total: 0 });
const hasMore = ref(true);

const getSortParams = () => {
  if (sortBy.value === 'price-asc') return { order_element: 'price', order_type: 'asc' };
  if (sortBy.value === 'price-desc') return { order_element: 'price', order_type: 'desc' };
  return { order_element: 'name', order_type: 'asc' };
};

const fetchProducts = async (reset = false, updateUrl = false) => {
  if ((isLoading.value || isLoadingMore.value) && !reset) return;
  
  if (reset) {
    pagination.value.current = 1;
    products.value = [];
    hasMore.value = true;
  }
  
  isLoading.value = reset;
  isLoadingMore.value = !reset;

  try {
    const { order_element, order_type } = getSortParams();
    const response = await productApi.getProducts({
      search: searchText.value,
      currentPage: pagination.value.current,
      limit: pagination.value.pageSize,
      order_element,
      order_type,
    });

    products.value = reset ? response.data.original.data : [...products.value, ...response.data.original.data];
    pagination.value.total = response.data.original.total;
    pagination.value.current += 1;
    hasMore.value = products.value.length < response.data.original.total;

    if (updateUrl) {
      router.push({ query: { search: searchText.value || undefined, sort: sortBy.value !== 'default' ? sortBy.value : undefined } });
    }
  } catch (error) {
    console.error('Error:', error);
    message.error(error.response?.data?.message || "error.");
  } finally {
    isLoading.value = false;
    isLoadingMore.value = false;
  }
};

const handleScroll = (event) => {
  const container = event.target;
  if (container.scrollTop + container.clientHeight >= container.scrollHeight - 1000 && hasMore.value) {
    fetchProducts();
  }
};

watch(() => route.query, () => fetchProducts(true), { deep: true });

onMounted(() => fetchProducts(true));

</script>

<style scoped>
.home-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 20px;
  max-height: 90vh;
  overflow-y: auto;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.header {
  padding: 0 20px;
  margin-bottom: 30px;
}

.search-bar {
  display: flex;
  gap: 15px;
  max-width: 700px;
  margin: 0 auto;
  align-items: center;
  background: #ffffff;
  padding: 15px;
  border-radius: 40px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.search-input {
  flex: 1;
  border: none;
}

.search-input :deep(.ant-input) {
  border: none;
  background: transparent;
  font-size: 15px;
}

.search-input :deep(.ant-btn) {
  border-radius: 20px;
  background: #1890ff;
  border: none;
  height: 36px;
}

.filter-select {
  width: 200px;
  font-size: 15px;
}

:deep(.ant-select-selector) {
  border-radius: 20px !important;
  height: 36px !important;
  display: flex;
  align-items: center;
  border: none !important;
  background: #f0f2f5 !important;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  padding: 0 20px;
}

.product-card {
  transition: transform 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
}

.loading-more {
  text-align: center;
  padding: 25px;
  color: #666;
  font-size: 14px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
}

/* Animation cho loading */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.products-grid {
  animation: fadeIn 0.5s ease-in;
}

/* Responsive design */
@media (max-width: 768px) {
  .search-bar {
    flex-direction: column;
    padding: 10px;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .home-container {
    padding: 20px 10px;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>