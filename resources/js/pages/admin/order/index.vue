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
                          Chi tiết
                      </a-button>
                      <!-- <a-button
                          v-if="record.state === 'pending'"
                          type="primary"
                          @click="changeStateOrder(record.id)"
                      >
                          Cập nhật
                      </a-button> -->
                  </div>
              </template>
          </a-table>
      </a-spin>

      <!-- Modal Chi tiết đơn hàng -->
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
  </a-card>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { message } from "ant-design-vue";
import orderApi from '../../../api/order';

const orders = ref([]);
const isLoading = ref(false);
const searchText = ref('');
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const modalVisible = ref(false);
const selectedOrder = ref(null);

const columns = [
  { title: 'Mã đơn', dataIndex: 'id', key: 'id', width: 120 },
  { title: 'Ngày đặt', dataIndex: 'created_at', key: 'created_at', width: 150, customRender: ({ text }) => formatDate(text) },
  { title: 'Tổng tiền', dataIndex: 'price', key: 'price', width: 150, customRender: ({ text }) => formatPrice(text) },
  { title: 'Trạng thái', key: 'state', width: 120, slots: { customRender: 'state' } },
  { title: 'Hành động', key: 'action', width: 150, slots: { customRender: 'action' } }
];

const itemColumns = [
  { title: 'Tên sản phẩm', key: 'name', customRender: ({ record }) => record.product?.name || 'N/A' },
  { title: 'Đơn giá', key: 'price', customRender: ({ record }) => formatPrice(record.product?.price || 0) },
  { title: 'Số lượng', dataIndex: 'count', key: 'count' }
];

const fetchOrders = async () => {
  isLoading.value = true;
  try {
      const response = await orderApi.getOrders({
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

const changeStateOrder = async (orderId) => {
  try {
    await orderApi.updateState("canceled",orderId);
    message.success("Đơn hàng đã bị hủy");
    fetchOrders();
  } catch (error) {
    console.error("Lỗi hủy đơn hàng:", error);
    message.error("Không thể hủy đơn hàng");
  }
};

const getStateColor = (state) => ({
  pending: 'yellow',
  processing: 'blue',
  completed: 'green',
  canceled: 'red'
}[state] || 'gray');

const formatPrice = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

const formatDate = (date) => new Intl.DateTimeFormat('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date(date));

onMounted(fetchOrders);
</script>

<style scoped>
.flex {
  display: flex;
  gap: 10px;
}
</style>
