<template>
  <input
    :type="type"
    :class="inputClass"
    :value="modelValue"
    :placeholder="placeholder"
    @input="updateValue"
  />
</template>

<script setup>
import { defineProps, defineEmits, computed } from "vue";

const props = defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    required: false,
    default: "Enter text...",
  },
  type: {
    type: String,
    default: "text",
  },
});

const emits = defineEmits(["update:modelValue"]);

const updateValue = (event) => {
  emits("update:modelValue", event.target.value);
};

const inputClass = computed(() => "custom-input");
</script>

<style scoped>
.custom-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s ease-in-out;
}

.custom-input:focus {
  border-color: #1890ff;
  outline: none;
}
</style>
