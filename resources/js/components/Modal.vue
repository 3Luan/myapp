<template>
    <div>
      <div class="mb-1">
        <button @click="showMsgBox" class="btn btn-primary">Show</button>
      </div>
  
      <a-modal v-model:open="isModalVisible" title="Confirmation" ok-text="OK" @ok="handleOk" :centered="true">
        <p>Data was submitted successfully</p>
      </a-modal>
    </div>
</template>
  
<script setup>
  import { ref } from "vue";
  
  const isModalVisible = ref(false);
  const boxTwo = ref("");
  
  const showMsgBox = () => {
    console.log("Opening modal...");
    isModalVisible.value = true;
  };
  
  const handleOk = () => {
    boxTwo.value = "Confirmed";
    isModalVisible.value = false;
  };
</script>
  