<template>
  <a-modal 
  :open="open" 
    :title="`Update Product [ID ${props.data?.id}]`" 
    @ok="handleSubmit" 
    @cancel="handleClose"
    okText="Update"
  >
    <a-form layout="vertical">
      <a-form-item label="Name">
        <a-input v-model:value="productData.name" placeholder="Enter name" />
      </a-form-item>

      <a-form-item label="Price">
        <a-input-number v-model:value="productData.price" placeholder="Enter price" style="width: 100%;" />
      </a-form-item>

      <a-form-item label="Images">
        <a-upload
          list-type="picture-card"
          :file-list="fileList"
          :beforeUpload="handleFileUpload"
          @remove="handleRemove"
          :multiple="true"
        >
          <div v-if="fileList.length < 5">
            <plus-outlined />
            <div style="margin-top: 8px">Upload</div>
          </div>
        </a-upload>
      </a-form-item>

      <a-form-item label="Description">
        <a-textarea v-model:value="productData.description" placeholder="Enter description" />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";

const props = defineProps({
  open: Boolean,
  reset: Boolean,
  data: Object,
});
const emit = defineEmits(["update:open", "updateProduct"]);

const productData = ref({
  name: "",
  price: "",
  description: "",
  images: [],
});

const isLoading = ref(true);
const fileList = ref([]);
const idDeleteList = ref([]);

watch(
  () => props.data,
  (newData) => {
    if (newData) {
      productData.value = { ...newData };
      productData.value.images = newData.images || [];

      // Cập nhật fileList để hiển thị ảnh cũ
      fileList.value = newData.images
        ? newData.images.map((img, index) => ({
            id: img.id,
            uid: `old-${index}`, // Đánh dấu là ảnh cũ
            name: img.name || `image-${index}`,
            status: "done",
            url: `/storage/${img.path}`, // Đường dẫn ảnh
          }))
        : [];

      isLoading.value = false;
    }
  },
  { immediate: true }
);

watch(() => props.open, (data) => {
  if (data) {
    isLoading.value = true
  }
});

watch(() => props.reset, (newVal) => {
  if (newVal) {
    productData.value = { name: "", price: "", description: "", images: [] };
    fileList.value = [];
  }
});

const handleFileUpload = (file) => {
  const reader = new FileReader();
  reader.onload = (e) => {
    fileList.value.push({
      id: null,
      uid: file.uid || `new-${Date.now()}`,
      name: file.name,
      status: "done",
      url: e.target.result, // Hiển thị ảnh mới
      file: file,
    });
  };

  productData.value.images = fileList.value;
  reader.readAsDataURL(file);
  return false;
};

const handleRemove = (file) => {
  if (file.uid.startsWith("old-")) {
    fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
    idDeleteList.value.push(file.id)
    return;
  }

  fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
  productData.value.images = productData.value.images.filter(
    (img) => img.uid !== file.uid
  );
};

const handleSubmit = () => {
  emit("updateProduct", productData.value, idDeleteList.value, props.data?.id);
  idDeleteList.value = [];
};

const handleClose = () => {
  emit("update:open", false);
};

</script>

<style scoped>
.ant-upload-wrapper.ant-upload-picture-card-wrapper {
  width: auto;
}
</style>