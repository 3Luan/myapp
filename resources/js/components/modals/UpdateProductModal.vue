<template>
  <a-modal 
    :open="open" 
    :title="`Update Product [ID ${props.data?.id}]`" 
    @ok="handleSubmit" 
    @cancel="handleClose"
    okText="Update"
  >
    <!-- Hiển thị loading khi dữ liệu chưa sẵn sàng -->
    <a-spin :spinning="isLoading" tip="Loading...">
      <a-form layout="vertical">
        <a-form-item label="Name">
          <a-input v-model:value="productData.name" placeholder="Enter name" />
        </a-form-item>
  
        <a-form-item label="Price">
          <a-input-number v-model:value="productData.price" placeholder="Enter price" style="width: 100%;" />
        </a-form-item>
  
        <a-form-item label="Image">
          <div class="upload-container">
            <a-upload
              list-type="picture-card"
              :showUploadList="false"
              :beforeUpload="handleFileUpload"
            >
              <div class="upload-box">
                <template v-if="uploading">
                  <a-spin />
                </template>
                <template v-else-if="imageUrl">
                  <img :src="imageUrl" alt="Upload" class="uploaded-image" />
                </template>
                <template v-else>
                  <plus-outlined class="upload-icon" />
                  <div class="upload-text">Upload</div>
                </template>
              </div>
            </a-upload>
          </div>
        </a-form-item>

        <a-form-item label="Description">
          <a-textarea v-model:value="productData.description" placeholder="Enter description" />
        </a-form-item>
      </a-form>
    </a-spin>
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
    image: null,
  });

  const imageUrl = ref("");
  const isLoading = ref(true);
  const uploading = ref(false);

  const handleFileUpload = (file) => {    
  uploading.value = true;

  setTimeout(() => {
    const reader = new FileReader();
    reader.onload = (e) => {
      imageUrl.value = e.target.result;
    };
    console.log(file);
    
    productData.value.image = file;
    reader.readAsDataURL(file);
    uploading.value = false;
  }, 200);

  return false;
  };

  watch(() => props.data, (newData) => {
    if (newData) {
        productData.value = { ...newData };
        productData.value.image = null;
        imageUrl.value = newData.image ? `/storage/${newData.image}` : "";
      isLoading.value = false;
    }
  }, { immediate: true });

  watch(() => props.open, (data) => {
    if (data) {
      isLoading.value = true
    }
  });

  watch(() => props.reset, (data) => {
    if (data) {
      productData.value = {
        name: "",
        price: "",
        description: "",
        image: null,
      };
      imageUrl.value = "";
    }
  });

  const handleSubmit = () => {
    emit("updateProduct", productData.value, props.data?.id);
  };

  const handleClose = () => {
    emit("update:open", false);
  };
  </script>

  <style scoped>
  .upload-container {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .upload-text {
    margin-top: 8px;
    font-size: 16px;
    color: #1890ff;
  }

  .uploaded-image {
    width: 150px;
    height: 150px;
    object-fit: fill;
    border-radius: 8px;
    border: #188fff83 solid 1px;
  }

  .ant-upload-wrapper.ant-upload-picture-card-wrapper {
  width: auto;
  }
</style>
