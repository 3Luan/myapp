<template>
  <div class="home-container">
    <!-- Header Section -->
    <header class="header">
      <!-- Search Bar -->
      <div class="search-bar">
        <a-input-search 
            v-model:value="searchText"
            placeholder="Search..."
            enter-button
            @search="handleSearch"
            style="width: 50%;"
        />
      </div>
    </header>

    <!-- Filter Section -->
    <section class="filter-section">
      <div class="filters">
        <select v-model="sortBy" @change="sortProducts" class="filter-select">
          <option value="default">Sắp xếp mặc định</option>
          <option value="price-asc">Giá: Thấp đến Cao</option>
          <option value="price-desc">Giá: Cao đến Thấp</option>
          <option value="name">Tên: A-Z</option>
        </select>
      </div>
    </section>

    <!-- Products Grid -->
    <section class="products-grid">
      <div v-for="product in filteredProducts" :key="product.id">
        <div class="cart-wrapper">
          <CustomCart :data="product"/>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import CustomCart from '../../components/common/CustomProductCart.vue';
import productApi from '../../api/product';

const searchText = ref('');
const selectedCategory = ref('');
const sortBy = ref('default');
const isLoading = ref(false);
const products = ref([]);
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('name');
const orderType = ref('asc');

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

      products.value = response.data.data;
      pagination.value.total = response.data.total;
  } catch (error) {
      console.error("Error:", error);
  } finally {
      isLoading.value = false;
  }
};

const filteredProducts = computed(() => {
  let result = [...products.value];

  if (selectedCategory.value) {
    result = result.filter(product => product.category === selectedCategory.value);
  }

  if (searchText.value) {
    result = result.filter(product => 
      product.name.toLowerCase().includes(searchText.value.toLowerCase())
    );
  }

  switch (sortBy.value) {
    case 'price-asc':
      return result.sort((a, b) => a.price - b.price);
    case 'price-desc':
      return result.sort((a, b) => b.price - a.price);
    case 'name':
      return result.sort((a, b) => a.name.localeCompare(b.name));
    default:
      return result;
  }
});

const handleSearch = () => {
  console.log('Searching for:', searchText.value);
};

const filterProducts = () => {
  console.log('Filtering by category:', selectedCategory.value);
};

const sortProducts = () => {
  console.log('Sorting by:', sortBy.value);
};

const formatPrice = (price) => {
  return price.toLocaleString('vi-VN') + ' đ';
};

const addToCart = (product) => {
  console.log('Added to cart:', product);
};

fetchProducts();
</script>

<style scoped>
.home-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  align-items: center;
  justify-content: space-between;
  margin-bottom: 30px;
}

.store-title {
  font-size: 2rem;
  color: #333;
}

.filter-section {
  margin-bottom: 30px;
}

.filters {
  display: flex;
  gap: 20px;
}

.filter-select {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
  gap: 50px;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 20px;
  }
  
  .filters {
    flex-direction: column;
  }
}
</style>