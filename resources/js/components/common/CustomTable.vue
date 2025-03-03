<template>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column.key" @click="sortTable(column.key)">
                        {{ column.title }}
                        <span v-if="sortableColumns.includes(column.key)">
                            {{ getSortIndicator(column.key) }}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in paginatedData" :key="row.id">
                    <td v-for="column in columns" :key="column.key">
                        {{ row[column.key] }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, computed } from "vue";

const props = defineProps({
    data: Array,
    columns: Array,
    pagination: Object,
    loading: Boolean
});

const emit = defineEmits(["tableChange"]);

const orderElement = ref(null);
const orderType = ref("asc");
const currentPage = ref(1);
const pageSize = computed(() => props.pagination?.pageSize || 5);
const totalPages = computed(() => Math.ceil(props.pagination.total / pageSize.value));

const sortableColumns = computed(() => props.columns.map(col => col.key));

const sortTable = (columnKey) => {
    if (!sortableColumns.value.includes(columnKey)) return;

    orderElement.value = columnKey;
    orderType.value = orderType.value === "asc" ? "desc" : "asc";

    emit("tableChange", {orderElement: orderElement.value, orderType: orderType.value });
    
    currentPage.value = 1;
};

const getSortIndicator = (columnKey) => {
    if (orderElement.value !== columnKey) return "";
    return orderType.value === "asc" ? "⬆" : "⬇";
};

const paginatedData = computed(() => {
    if (!props.data || props.data.length === 0) return [];

    return props.data.sort((a, b) => {
        if (!orderElement.value) return 0;
        
        const valA = a[orderElement.value];
        const valB = b[orderElement.value];

        if (typeof valA === "number" && typeof valB === "number") {
            return orderType.value === "asc" ? valA - valB : valB - valA;
        } else {
            return orderType.value === "asc" ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }
    });
});

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        emit("tableChange", { current: currentPage.value,});
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        emit("tableChange", { current: currentPage.value});
    }
};
</script>

<style scoped>
.table-container {
    width: 100%;
    overflow-x: auto;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table th, .custom-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.custom-table th {
    background-color: #f4f4f4;
    cursor: pointer;
}

.custom-table th:hover {
    background-color: #ddd;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 10px;
    gap: 10px;
}

button {
    padding: 5px 10px;
    cursor: pointer;
}
</style>