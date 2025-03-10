<template>
  <div class="home-container" @scroll="handleScroll">
    <header class="header">
      <a-input-search
        v-model:value="searchText"
        placeholder="Search..."
        enter-button="Search"
        @search="fetchProducts(true, true)"
        class="search-input"
      />
    </header>

    <section class="filter-section">
      <a-select v-model:value="sortBy" @change="fetchProducts(true, true)" class="filter-select">
        <a-select-option value="default">Default</a-select-option>
        <a-select-option value="price-asc">Price: Low to High</a-select-option>
        <a-select-option value="price-desc">Price: High to Low</a-select-option>
      </a-select>
    </section>

    <a-spin :spinning="isLoading">
      <section class="products-grid">
        <CustomCart v-for="product in products" :key="product.id" :data="product" />
      </section>
      <div v-if="isLoadingMore" class="loading-more">Loading...</div>
    </a-spin>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import CustomCart from '@/components/common/CustomProductCart.vue';
import productApi from '@/api/product';

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

    products.value = reset ? response.data.data : [...products.value, ...response.data.data];
    pagination.value.total = response.data.total;
    pagination.value.current += 1;
    hasMore.value = products.value.length < response.data.total;

    if (updateUrl) {
      router.push({ query: { search: searchText.value || undefined, sort: sortBy.value !== 'default' ? sortBy.value : undefined } });
    }
  } catch (error) {
    console.error('Error:', error);
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
  max-width: 1300px;
  margin: auto;
  padding: 30px;
  max-height: 90vh;
  overflow-y: auto;
}

.header, .filter-section {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.search-input, .filter-select {
  width: 100%;
  max-width: 300px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.loading-more {
  text-align: center;
  padding: 20px;
}
</style>
