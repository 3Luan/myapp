<template>
    <a-modal
        :visible="open"
        title="Update Discount"
        @ok="handleUpdate"
        @cancel="resetForm"
    >
        <a-form layout="vertical">
            <a-form-item label="Name">
                <a-input v-model:value="formData.name" />
            </a-form-item>

            <a-form-item label="Percentage">
                <a-input-number v-model:value="formData.percent" :min="1" :max="100" />
            </a-form-item>

            <a-form-item label="Start Date">
                <a-date-picker
                    v-model:value="formData.start_date" 
                    show-time 
                    valueFormat="YYYY-MM-DD HH:mm:ss" 
                />
            </a-form-item>

            <a-form-item label="End Date">
                <a-date-picker
                    v-model:value="formData.end_date" 
                    show-time 
                    valueFormat="YYYY-MM-DD HH:mm:ss" 
                />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps(["open", "data", "reset"]);
const emit = defineEmits(["update:open", "updateDiscount"]);

const formData = ref({});

watch(() => props.data, (newData) => {
    formData.value = { ...newData };
}, { immediate: true });

const handleUpdate = () => {
    if (!formData.value.name || !formData.value.percent) {
        return;
    }
    emit("updateDiscount", formData.value, props.data.id);
    resetForm();
};

const resetForm = () => {
    emit("update:open", false);
};
</script>
