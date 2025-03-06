<template>
  <a-modal 
    :open="open" 
    title="Purchase Product" 
    @ok="handleSubmit" 
    @cancel="handleClose"
    okText="Buy"
  >
    <a-form layout="vertical">
      <a-form-item label="Product Name">
        <a-input v-model:value="purchaseData.name" disabled />
      </a-form-item>

      <a-form-item label="Price per unit">
        <a-input-number v-model:value="purchaseData.price" disabled style="width: 100%;" />
      </a-form-item>

      <a-form-item label="Quantity">
        <a-input-number v-model:value="purchaseData.quantity" 
          :min="1" 
          @change="updateTotalPrice" 
          style="width: 100%;" 
        />
      </a-form-item>

      <a-form-item label="Total Price">
        <a-input-number v-model:value="purchaseData.totalPrice" disabled style="width: 100%;" />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script setup>
import { message } from "ant-design-vue";
import { ref, defineProps, defineEmits, watch } from "vue";
import orderApi from "../../api/order";
import { useRouter } from "vue-router";

const props = defineProps({
  open: Boolean,
  reset: Boolean,
  data: Object,
});
const emit = defineEmits(["update:open", "buyProduct"]);
const router = useRouter();

const purchaseData = ref({
  name: "",
  price: 0,
  quantity: 1,
  totalPrice: 0,
});

watch([() => props.data, () => props.open], ([newData, isOpen]) => {
  if (isOpen && newData) {
    purchaseData.value = {
      name: newData.name,
      price: newData.price,
      quantity: 1,
      totalPrice: newData.price,
    };
  }
}, { deep: true });

const updateTotalPrice = () => {
  purchaseData.value.totalPrice = purchaseData.value.price * purchaseData.value.quantity;
};

const handleSubmit = async () => {
  if (!purchaseData.value.quantity || purchaseData.value.quantity < 1) {
    return message.warning("Please enter a valid quantity.");
  }

  if (purchaseData.value.quantity > props.data.count) {
    return message.warning("Product does not have enough stock.");
  }

  const formData = new FormData();
  formData.append("products[0][product_id]", props.data.id);
  formData.append("products[0][count]", purchaseData.value.quantity);

  try {
    const response = await orderApi.addOrder(formData);
      console.log("response",response);

      message.success("Order placed successfully!");
      emit("update:open", false);
      router.push("/history");

  } catch (error) {
    console.log(error);
    message.error(error.response?.data?.message || "An error occurred while ordering.");
  }
};


const handleClose = () => {
  emit("update:open", false);
};
</script>

<style scoped>
.ant-modal-footer {
  display: flex;
  justify-content: space-between;
}
</style>
