<template>
  <div class="product-detail">
    <a-spin :spinning="isLoading">
      <div v-if="product" class="product-content">
        <h1 class="product-title">{{ product.name }}</h1>

        <div class="product-images">
          <a-carousel autoplay>
            <div v-for="(image, index) in product.images" :key="index" class="carousel-item">
              <img :src="`/storage/${image?.path}`" :alt="`${product.name} - ${index + 1}`" />
            </div>
            <div v-if="!product.images?.length" class="no-images">
              No Images Available
            </div>
          </a-carousel>
        </div>

        <div class="product-info">
          <p class="price">{{ formatPrice(product.price) }}</p>
          <p class="description">{{ product.description || 'No description available.' }}</p>
          <p><strong>Stock:</strong> {{ product?.count > 0 ? `${product.count} in stock` : 'Out of stock' }}</p>

          <div class="quantity-input">
            <strong>Quantity:</strong>
            <a-input-number 
              v-model:value="quantity" 
              :min="1" 
              :max="product?.count || 1" 
              @change="validateQuantity"
            />
          </div>

          <div class="action-buttons">
            <a-button 
              type="primary" 
              @click="buyNow" 
              :disabled="!product?.count || product.count <= 0"
            >
              Buy Now
            </a-button>
            <a-button 
              type="default" 
              @click="addToCart" 
              :disabled="!product?.count || product.count <= 0"
            >
              Add to Cart
            </a-button>
          </div>
        </div>


        <div class="back-button">
          <a-button @click="goBack">Back to Products</a-button>
        </div>
      </div>

      <div v-else class="not-found">
        <div v-if="!isLoading">
          Product not found.
        </div>
      </div>
    </a-spin>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import productApi from '@/api/product';
import { message } from 'ant-design-vue';
import orderApi from "@/api/order";
import store from "@/store";

const router = useRouter();
const route = useRoute();
const isLoading = ref(false);
const product = ref(null);
const quantity = ref(1);

const fetchProduct = async () => {
  isLoading.value = true;
  try {
    const productId = route.params.id;
    const response = await productApi.getProductDetails(productId);
    product.value = response.data;
    
  } catch (error) {
    console.error('Error fetching product:', error);
    message.error(error.response?.data?.message ||'Failed to load product details');
    product.value = null;
  } finally {
    isLoading.value = false;
  }
};

const validateQuantity = () => {
  if (quantity.value > product.value.count) {
    quantity.value = product.value.count;
  } else if (quantity.value < 1) {
    quantity.value = 1;
  }
};

const addToCart = async() => {
  try {
    if (quantity.value <= 0) {
      return message.warning("Please enter a valid quantity.");
    }
    if (product.value.count <= quantity.value) {
      return message.warning("Product does not have enough stock.");
    }

    console.log({
      product_id: product.value.id,
      count: quantity.value
    });
    
    await store.dispatch("cart/addToCart",{
      product_id: product.value.id,
      count: quantity.value
    })
  } catch (error) {
    console.error(error);
    message.error(error.response?.data?.message || "An error occurred while ordering.");
  }
};

const buyNow = async ()  => {
  if (quantity.value <= 0) {
    return message.warning("Please enter a valid quantity.");
  }

  if (product.value.count <= quantity.value) {
    return message.warning("Product does not have enough stock.");
  }

  const formData = new FormData();
  formData.append("products[0][product_id]", product.value.id);
  formData.append("products[0][count]", quantity.value);

  try {
    await orderApi.addOrder(formData);

    message.success("Order placed successfully!");
    router.push("/history");
  } catch (error) {
    console.error(error);
    message.error(error.response?.data?.message || "An error occurred while ordering.");
  }
};

const goBack = () => {
  router.push({ name: 'home' });
};

const formatPrice = (price) => {
  return price ? `$${Number(price).toFixed(2)}` : 'N/A';
};

onMounted(fetchProduct);
</script>

<style scoped>
.product-detail {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.product-title {
  margin-bottom: 20px;
  font-size: 24px;
}

.product-images {
  margin-bottom: 20px;
}

.carousel-item img {
  width: 100%;
  max-height: 400px;
  object-fit: cover;
}

.no-images {
  text-align: center;
  padding: 20px;
  color: #888;
}

.product-info {
  margin-bottom: 20px;
}

.price {
  font-size: 20px;
  color: #1890ff;
  margin-bottom: 10px;
}

.description {
  margin-bottom: 10px;
  color: #666;
}

.action-buttons {
  margin-top: 20px;
  display: flex;
  gap: 10px;
}

.back-button {
  margin-top: 20px;
}

.not-found {
  text-align: center;
  padding: 20px;
  color: #ff4d4f;
}

.quantity-input {
  margin-top: 10px;
  margin-bottom: 10px;
}

</style>