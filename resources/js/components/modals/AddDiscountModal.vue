<template>
    <a-modal
        :visible="open"
        title="Add Discount"
        @ok="handleAdd"
        @cancel="handleClose"
    >
        <a-form layout="vertical" :model="formData" ref="formRef">
            <a-form-item label="Name" required :validate-status="errors.name ? 'error' : ''" :help="errors.name">
                <a-input v-model:value="formData.name" placeholder="Enter discount name" />
            </a-form-item>

            <a-form-item label="Percentage" required :validate-status="errors.percent ? 'error' : ''" :help="errors.percent">
                <a-input-number v-model:value="formData.percent" :min="1" :max="100" />
            </a-form-item>

            <a-form-item label="Start Date" required :validate-status="errors.start_date ? 'error' : ''" :help="errors.start_date">
                <a-date-picker v-model:value="formData.start_date" show-time />
            </a-form-item>

            <a-form-item label="End Date" required :validate-status="errors.end_date ? 'error' : ''" :help="errors.end_date">
                <a-date-picker v-model:value="formData.end_date" show-time />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script setup>
import dayjs from 'dayjs';
import { ref, watch } from 'vue';

const props = defineProps(["open", "reset"]);
const emit = defineEmits(["update:open", "addDiscount"]);

const formData = ref({
    name: "",
    percent: null,
    start_date: null,
    end_date: null
});

const errors = ref({
    name: null,
    percent: null,
    start_date: null,
    end_date: null
});

const validateForm = () => {
    errors.value = {
        name: formData.value.name ? null : "Please enter discount name",
        percent: formData.value.percent ? null : "Please enter a valid percentage",
        start_date: formData.value.start_date ? null : "Please select a start date",
        end_date: formData.value.end_date ? null : "Please select an end date"
    };

    return Object.values(errors.value).every(error => error === null);
};

const handleAdd = () => {
    if (!validateForm()) {
        return;
    }
    const formattedData = {
        ...formData.value,
        start_date: dayjs(formData.value.start_date).format("YYYY-MM-DD HH:mm:ss"),
        end_date: dayjs(formData.value.end_date).format("YYYY-MM-DD HH:mm:ss"),
    };

    emit("addDiscount", formattedData);
    // resetForm();
};

const handleClose = () => {
    emit("update:open", false);
};

const resetForm = () => {
    formData.value = { name: "", percent: null, start_date: null, end_date: null };
    errors.value = { name: null, percent: null, start_date: null, end_date: null };
    emit("update:open", false);
};

watch(() => props.reset, resetForm);
</script>
