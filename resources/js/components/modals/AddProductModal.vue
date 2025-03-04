<template>
  <a-modal 
    :open="open" 
    title="Add Product" 
    @ok="handleSubmit" 
    @cancel="handleClose"
  >
    <a-form layout="vertical">
      <a-form-item label="Name">
        <a-input v-model:value="newProduct.name" placeholder="Enter name" />
      </a-form-item>

      <a-form-item label="Price">
        <a-input-number v-model:value="newProduct.price" placeholder="Enter price" style="width: 100%;" />
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
        <a-textarea v-model:value="newProduct.description" placeholder="Enter description" />
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
});
const emit = defineEmits(["update:open", "addProduct"]);

const newProduct = ref({
  name: "",
  price: "",
  description: "",
  images: [],
});

const fileList = ref([]);

const handleFileUpload = (file) => {
  const reader = new FileReader();
  reader.onload = (e) => {
    fileList.value.push({
      uid: file.uid,
      name: file.name,
      status: "done",
      url: e.target.result,
      file: file,
    });
    newProduct.value.images.push(file);
  };
  reader.readAsDataURL(file);
  return false;
};

const handleRemove = (file) => {
  fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
  newProduct.value.images = newProduct.value.images.filter((img) => img.uid !== file.uid);
};

watch(() => props.reset, (newVal) => {
  if (newVal) {
    newProduct.value = { name: "", price: "", description: "", images: [] };
    fileList.value = [];
  }
});

const handleSubmit = () => {
  emit("addProduct", newProduct.value);
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