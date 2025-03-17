<template>
  <a-modal
    :open="open"
    :title="`User Details [ID ${props.data?.id || 'New User'}]`"
    @ok="handleSubmit"
    @cancel="handleClose"
    okText="Save"
    :width="600"
    :confirm-loading="isSubmitting"
  >
    <a-form
      ref="formRef"
      :model="userData"
      :rules="rules"
      layout="vertical"
      class="user-form"
    >
      <!-- Avatar -->
      <!-- <a-form-item label="Avatar" name="avatar">
        <a-upload
          list-type="picture-card"
          :file-list="avatarList"
          :before-upload="handleAvatarUpload"
          @remove="handleAvatarRemove"
          :max-count="1"
        >
          <div v-if="avatarList.length < 1">
            <plus-outlined />
            <div style="margin-top: 8px">Upload Avatar</div>
          </div>
        </a-upload>
      </a-form-item> -->

      <!-- Name -->
      <a-form-item label="Full Name" name="name">
        <a-input
          v-model:value="userData.name"
          placeholder="Enter full name"
          size="large"
        />
      </a-form-item>

      <!-- Phone Number -->
      <a-form-item label="Phone Number" name="phone">
        <a-input
          v-model:value="userData.phone"
          placeholder="Enter phone number"
          size="large"
          :maxlength="15"
        />
      </a-form-item>

      <!-- Password -->
      <a-form-item label="Password" name="password">
        <a-input-password
          v-model:value="userData.password"
          placeholder="Enter new password (leave blank to keep current)"
          size="large"
        />
      </a-form-item>

      <!-- Role -->
      <a-form-item label="Role" name="role">
        <a-select
          v-model:value="userData.role_id"
          placeholder="Select role"
          size="large"
        >
          <a-select-option
            v-for="role in roles"
            :key="role.id"
            :value="role.id"
          >
            {{ role.name }}
          </a-select-option>
        </a-select>
      </a-form-item>

      <!-- Lock Status -->
      <a-form-item label="Account Status" name="locked">
        <a-switch
          v-model:checked="userData.locked"
          checked-children="Locked"
          un-checked-children="Active"
        />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, onMounted } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import roleApi from "@/api/role";

const props = defineProps({
  open: Boolean,
  reset: Boolean,
  data: Object,
});

const emit = defineEmits(["update:open", "updateUser"]);

const formRef = ref();
const isSubmitting = ref(false);
const roles = ref([]);

const userData = ref({
  name: "",
  phone: "",
  password: "",
  role: "3", // Default role member
  locked: false,
  avatar: null,
});

const avatarList = ref([]);

// Validation rules
const rules = {
  name: [{ required: true, message: "Please enter full name", trigger: "blur" }],
  phone: [
    { required: true, message: "Please enter phone number", trigger: "blur" },
    {
      pattern: /^[0-9]{10,15}$/,
      message: "Phone number must be 10-15 digits",
      trigger: "blur",
    },
  ],
  role: [{ required: true, message: "Please select a role", trigger: "change" }],
};

watch(
  () => props.data,
  (newData) => {
    if (newData) {
      userData.value = {
        name: newData.name || "",
        phone: newData.phone || "",
        password: "",
        role: newData.role.name || null,
        role_id: newData.role.id || null,
        locked: newData.is_locked || false,
        avatar: newData.avatar || null,
      };

      // Update avatar preview
      avatarList.value = newData.avatar
        ? [
            {
              uid: "avatar",
              name: "avatar",
              status: "done",
              url: `/storage/${newData.avatar}`,
            },
          ]
        : [];
    }
  },
  { immediate: true }
);

const fetchRoles = async () => {
  try {
    const response = await roleApi.getRoles();
    roles.value = response.data.original.data;
  } catch (error) {
    console.error("Error fetching roles:", error);
    message.error(error.response?.data?.message ||"Failed to load roles");
  }
};

// Watch reset prop
watch(() => props.reset, (newVal) => {
  if (newVal) {
    resetForm();
  }
});

// const handleAvatarUpload = (file) => {
//   const reader = new FileReader();
//   reader.onload = (e) => {
//     avatarList.value = [
//       {
//         uid: "avatar",
//         name: file.name,
//         status: "done",
//         url: e.target.result,
//         file: file,
//       },
//     ];
//     userData.value.avatar = file;
//   };
//   reader.readAsDataURL(file);
//   return false;
// };

// const handleAvatarRemove = () => {
//   avatarList.value = [];
//   userData.value.avatar = null;
// };

// Handle form submission
const handleSubmit = async () => {
  try {
    await formRef.value.validate();
    isSubmitting.value = true;
    console.log(userData.value);

    const formData = new FormData();
    formData.append("name", userData.value.name);
    formData.append("phone", userData.value.phone);
    if (userData.value.password) {
      formData.append("password", userData.value.password);
    }
    formData.append("is_locked", userData.value.locked ? 1 : 0);
    formData.append("role_id", userData.value.role_id); // Gửi role_id

    // if (userData.value.avatar instanceof File) {
    //   formData.append("avatar", userData.value.avatar);
    // }

    emit("updateUser", formData, props.data?.id);
  } catch (error) {
    console.log("Error submitting form:", error);
    message.error(error.response?.data?.message ||"Please check the form fields");
  } finally {
    isSubmitting.value = false;
  }
};

const handleClose = () => {
  emit("update:open", false);
  if (!props.data?.id) resetForm();
};

const resetForm = () => {
  userData.value = {
    name: "",
    phone: "",
    password: "",
    role: null,
    locked: false,
    avatar: null,
  };
  avatarList.value = [];
  formRef.value?.resetFields();
};

onMounted(() => {
  fetchRoles();
});
</script>

<style scoped>
.user-form :deep(.ant-form-item) {
  margin-bottom: 16px;
}

.ant-upload-wrapper.ant-upload-picture-card-wrapper {
  width: auto;
}

.ant-upload-list-picture-card .ant-upload-list-item {
  width: 100px;
  height: 100px;
}
</style>