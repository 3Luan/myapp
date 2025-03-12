<template>
  <a-card title="Orders Management" style="width: 100%; height: 86vh;">
      <div class="mb-4 flex items-center justify-between">
          <!-- Search -->
          <a-input-search 
              v-model:value="searchText"
              placeholder="Search..."
              enter-button
              @search="handleSearch"
              style="max-width: 300px;"
          />
      </div>

      <a-spin :spinning="isLoading" tip="Loading...">
          <a-table
              :dataSource="orders"
              :columns="columns"
              :pagination="pagination"
              :loading="isLoading"
              rowKey="id"
              bordered
              :scroll="{ y: '55vh' }"
              @change="handleTableChange"
          >
          <template #state="{ record }">
            <a-tag :color="getStateColor(record.state)">
              {{ record.state }}
            </a-tag>
          </template>
          <template #action="{ record }">
            <div style="display: flex; gap: 8px;">
              <a-button type="primary" @click="viewOrderDetail(record)">
                Details
              </a-button>
              <a-dropdown v-if="shouldShowDropdown(record.state)">
                <template #overlay>
                  <a-menu @click="({ key }) => handleStateChange(record.id, key)">
                    <a-menu-item v-if="record.state === 'pending'" key="processing">Processing</a-menu-item>
                    <a-menu-item v-if="record.state === 'processing'" key="completed">Completed</a-menu-item>
                    <a-menu-item v-if="record.state !== 'completed' && record.state !== 'canceled'" key="canceled">Canceled</a-menu-item>
                  </a-menu>
                </template>
                <a-button>
                  Update State
                  <DownOutlined />
                </a-button>
              </a-dropdown>
            </div>
          </template>
          </a-table>
      </a-spin>

      <!-- Modal order details -->
      <OrderDetailModal
        :open="modalVisible"
        :selected-order="selectedOrder"
        @update:open="modalVisible = $event"
      />
  </a-card>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { message } from "ant-design-vue";
import { DownOutlined } from '@ant-design/icons-vue';
import orderApi from '@/api/order';
import OrderDetailModal from '@/components/modals/OrderDetailModal.vue';

const orders = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const modalVisible = ref(false);
const selectedOrder = ref(null);

const columns = [
  { title: 'ID', dataIndex: 'id', key: 'id', width: 120 },
  { title: 'Order date', dataIndex: 'created_at', key: 'created_at', width: 150, customRender: ({ text }) => formatDate(text) },
  { title: 'Total amount', dataIndex: 'price', key: 'price', width: 150, customRender: ({ text }) => formatPrice(text) },
  { title: 'State', key: 'state', width: 120, slots: { customRender: 'state' } },
  { title: 'Action', key: 'action', width: 150, slots: { customRender: 'action' } }
];

const fetchOrders = async () => {
  isLoading.value = true;
  try {
      const response = await orderApi.getOrdersAdmin({
          search: searchText.value,
          currentPage: pagination.value.current,
          limit: pagination.value.pageSize,
      });
      orders.value = response.data.data;
      pagination.value.total = response.data.total;
  } catch (error) {
      console.error("Error:", error);
  } finally {
      isLoading.value = false;
  }
};

const handleSearch = () => {
  pagination.value.current = 1;
  fetchOrders();
};

const handleTableChange = (paginationObj) => {
  pagination.value = paginationObj;
  fetchOrders();
};

const viewOrderDetail = (order) => {
  selectedOrder.value = order;
  modalVisible.value = true;
};

const handleStateChange = async (orderId, newState) => {
  try {
    await orderApi.updateStateAdmin(newState, orderId);
    message.success(`Order state updated to ${newState}`);
    fetchOrders();
  } catch (error) {
    console.error("Order state update error:", error);
    message.error("Failed to update order state.");
  }
};

const shouldShowDropdown = (state) => {
  return state !== 'completed' && state !== 'canceled';
};

const getStateColor = (state) => ({
  pending: 'yellow',
  processing: 'blue',
  completed: 'green',
  canceled: 'red'
}[state] || 'gray');

const formatPrice = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

const formatDate = (date) => new Intl.DateTimeFormat('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date(date));

onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.flex {
  display: flex;
  gap: 10px;
}
</style>