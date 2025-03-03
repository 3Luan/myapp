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
                    <a-textarea v-model:value="newProduct.description" placeholder="Enter description" />
        </a-form-item>
      </a-form>
    </a-modal>
  </template>
  
  <script setup>
  import { ref, defineProps, defineEmits } from "vue";
  import { PlusOutlined } from "@ant-design/icons-vue";
  import { watch } from "vue";
  
  const props = defineProps({
    open: Boolean,
    reset: Boolean,
  });
  
  const emit = defineEmits(["update:open", "addProduct"]);
  
  const newProduct = ref({
    name: "",
    price: "",
    description:"",
    image: null,
  });
  
  const imageUrl = ref("");
  const uploading = ref(false);
  
  const handleFileUpload = (file) => {
    uploading.value = true;
  
    setTimeout(() => {
      const reader = new FileReader();
      reader.onload = (e) => {
        imageUrl.value = e.target.result;
      };
      newProduct.value.image = file;
      reader.readAsDataURL(file);
      uploading.value = false;
    }, 200);
  
    return false;
  };

    watch(() => props.reset, (newVal) => {
        if (newVal) {
            newProduct.value = {
                name: "",
                price: "",
                description: "",
                image: null,
            };
            imageUrl.value = "";
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
  