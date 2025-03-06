<template>
  <div class="order-history-container">
    <header class="header">
      <h1>Lịch sử đơn hàng</h1>
      <a-input-search
        v-model:value="searchText"
        placeholder="Tìm kiếm đơn hàng..."
        size="large"
        :loading="isSearching"
        @search="handleSearch"
        style="width: 300px"
      />
    </header>

    <a-spin :spinning="isLoading" tip="Đang tải dữ liệu...">
      <a-table
        :columns="columns"
        :data-source="orders"
        :pagination="pagination"
        row-key="id"
        @change="handleTableChange"
        bordered
      >
        <template #state="{ record }">
          <a-tag :color="getStateColor(record.state)">
            {{ record.state }}
          </a-tag>
        </template>
        <template #action="{ record }">
        <div style="display: flex; gap: 8px;">
          <a-button type="primary" @click="viewOrderDetail(record)">
            Chi tiết
          </a-button>
          <a-button
            v-if="record.state === 'pending'"
            type="primary"
            danger
            style="width: 80px"
            @click="cancelOrder(record.id)"
          >
            Hủy
          </a-button>
        </div>
      </template>
      </a-table>
    </a-spin>

    <a-modal v-model:open="modalVisible" title="Chi tiết đơn hàng" width="600px" :footer="null">
      <div v-if="selectedOrder">
        <a-descriptions :column="1" bordered>
          <a-descriptions-item label="Mã đơn hàng">{{ selectedOrder.id }}</a-descriptions-item>
          <a-descriptions-item label="Ngày đặt">{{ formatDate(selectedOrder.created_at) }}</a-descriptions-item>
          <a-descriptions-item label="Tổng tiền">{{ formatPrice(selectedOrder.price) }}</a-descriptions-item>
          <a-descriptions-item label="Trạng thái">
            <a-tag :color="getStateColor(selectedOrder.state)">
              {{ selectedOrder.state }}
            </a-tag>
          </a-descriptions-item>
        </a-descriptions>

        <h3>Danh sách sản phẩm</h3>
        <a-table :columns="itemColumns" :data-source="selectedOrder.order_details" :pagination="false" row-key="id" />
      </div>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { message } from 'ant-design-vue';
import orderApi from '../../api/order';

const searchText = ref('');
const isLoading = ref(false);
const isSearching = ref(false);
const orders = ref([]);
const selectedOrder = ref(null);
const modalVisible = ref(false);

const pagination = ref({ current: 1, pageSize: 10, total: 0 });

const columns = [
  { title: 'Mã đơn', dataIndex: 'id', key: 'id', width: 120 },
  { title: 'Ngày đặt', dataIndex: 'created_at', key: 'created_at', width: 150, customRender: ({ text }) => formatDate(text) },
  { title: 'Tổng tiền', dataIndex: 'price', key: 'price', width: 150, customRender: ({ text }) => formatPrice(text) },
  { title: 'Trạng thái', key: 'state', width: 120, slots: { customRender: 'state' } },
  { title: 'Hành động', key: 'action', width: 120, slots: { customRender: 'action' } }
];

const itemColumns = [
  { 
    title: 'Tên sản phẩm', 
    key: 'name', 
    customRender: ({ record }) => record.product ? record.product.name : 'N/A'
  },
  { 
    title: 'Đơn giá', 
    key: 'price', 
    customRender: ({ record }) => record.product ? formatPrice(record.product.price) : 'N/A'
  },
  { title: 'Số lượng', dataIndex: 'count', key: 'count' }
];

const fetchOrders = async () => {
  isLoading.value = true;
  try {
    const response = await orderApi.getOrders({
      search: searchText.value,
      currentPage: pagination.value.current,
      limit: pagination.value.pageSize
    });

    orders.value = response.data.data;
    pagination.value.total = response.data.total;
  } catch (error) {
    console.error(error);
    message.error('Không thể tải đơn hàng');
  } finally {
    isLoading.value = false;
  }
};

const handleSearch = async () => {
  isSearching.value = true;
  pagination.value.current = 1;
  await fetchOrders();
  isSearching.value = false;
};

const handleTableChange = (pag) => {
  pagination.value.current = pag.current;
  pagination.value.pageSize = pag.pageSize;
  fetchOrders();
};

const viewOrderDetail = (order) => {
  selectedOrder.value = order;
  modalVisible.value = true;
};


const cancelOrder = async (orderId) => {
  try {
    await orderApi.updateState("canceled",orderId);
    message.success("Đơn hàng đã được hủy!");
    fetchOrders();
  } catch (error) {
    message.error("Không thể hủy đơn hàng!");
    console.error(error);
  }
};

const getStateColor = (state) => {
  return { pending: 'yellow', processing: 'blue', completed: 'green', canceled: 'red' }[state] || 'gray';
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const formatDate = (date) => {
  return new Intl.DateTimeFormat('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date(date));
};

onMounted(fetchOrders);
</script>

<style scoped>
.order-history-container {
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
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
}
</style>
