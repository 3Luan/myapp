<template>
  <button :class="buttonClass" :disabled="disabled" @click="onClick">
    <slot></slot>
  </button>
</template>

<script setup>
import { defineProps, computed } from "vue";
import { ButtonType } from "../../constants/index.js";

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => Object.values(ButtonType).includes(value),
  },
  onClick: {
    type: Function,
    required: false,
    default: () => {},
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const buttonClass = computed(() => `btn ${props.type}`);
</script>

<style scoped>
.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s ease;
}

/* Màu sắc theo loại */
.primary {
  background-color: blue;
  color: white;
}

.secondary {
  background-color: gray;
  color: white;
}

.success {
  background-color: green;
  color: white;
}

.warning {
  background-color: orange;
  color: white;
}

.danger {
  background-color: red;
  color: white;
}

/* Trạng thái disabled */
.btn:disabled {
  background-color: lightgray;
  cursor: not-allowed;
}
</style>
