<template>
    <a-modal
      :open="open"
      title="Order details"
      width="600px"
      :footer="null"
      @cancel="handleClose"
    >
      <div v-if="selectedOrder">
        <a-descriptions :column="1" bordered>
          <a-descriptions-item label="ID">{{ selectedOrder.id }}</a-descriptions-item>
          <a-descriptions-item label="Order date">{{ formatDate(selectedOrder.created_at) }}</a-descriptions-item>
          <a-descriptions-item label="Total amount">{{ formatPrice(selectedOrder.price) }}</a-descriptions-item>
          <a-descriptions-item label="State">
            <a-tag :color="getStateColor(selectedOrder.state)">
              {{ selectedOrder.state }}
            </a-tag>
          </a-descriptions-item>
        </a-descriptions>
  
        <h3>Product list</h3>
        <a-table :columns="itemColumns" :data-source="selectedOrder.order_details" :pagination="false" row-key="id" />
      </div>
    </a-modal>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    open: {
      type: Boolean,
      required: true,
    },
    selectedOrder: {
      type: Object,
      default: null,
    },
  });
  
  const emit = defineEmits(['update:open']);
  
  const handleClose = () => {
    emit('update:open', false);
  };
  
  const itemColumns = [
    { 
      title: 'Name', 
      key: 'name', 
      customRender: ({ record }) => record.product ? record.product.name : 'N/A'
    },
    { 
      title: 'Price', 
      key: 'price', 
      customRender: ({ record }) => record.product ? formatPrice(record.product.price) : 'N/A'
    },
    { title: 'Count', dataIndex: 'count', key: 'count' }
  ];
  
  const getStateColor = (state) => {
    return { pending: 'yellow', processing: 'blue', completed: 'green', canceled: 'red' }[state] || 'gray';
  };
  
  const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
  };
  
  const formatDate = (date) => {
    return new Intl.DateTimeFormat('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date(date));
  };
  </script>