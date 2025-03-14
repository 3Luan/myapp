<!-- NotificationDropdown.vue -->
<template>
  <a-dropdown :visible="isDropdownVisible" placement="bottomRight" @visibleChange="handleVisibleChange">
    <template #overlay>
      <div class="notification-dropdown">
        <!-- Header -->
        <div class="dropdown-header">
          <span class="header-title">Notifications</span>
          <a-button type="link" size="small" @click="markAllAsRead">
            Read All
          </a-button>
        </div>

        <!-- Search Input -->
        <div class="search-wrapper">
          <a-input
            v-model:value="searchText"
            placeholder="Search notifications..."
            size="small"
            allow-clear
          >
            <template #prefix>
              <SearchOutlined />
            </template>
          </a-input>
        </div>

        <!-- Notification Menu -->
        <a-menu class="notification-menu">
          <a-menu-item v-if="filteredNotifications.length === 0" disabled>
            <span>No matching notifications</span>
          </a-menu-item>

          <a-menu-item
            v-for="(notif, index) in filteredNotifications"
            :key="index"
            :class="{ 'unread': !notif.read_at }"
            @click="markAsRead(index)"
          >
            <div class="notification-content">
              <span class="message">{{ notif.data.message }}</span>
              <span class="time">{{ formatTime(notif.created_at) }}</span>
            </div>
          </a-menu-item>

          <!-- View All Button -->
          <a-menu-item v-if="filteredNotifications.length > 0" class="view-all-item">
            <a-button type="link" block @click="viewAllNotifications">
              View All ({{ filteredNotifications.length }})
            </a-button>
          </a-menu-item>
        </a-menu>
      </div>
    </template>
    <div class="nav-item notification-icon">
      <a-badge :count="unread_count" :offset="[5, 0]">
        <BellOutlined class="nav-icon" />
      </a-badge>
    </div>
  </a-dropdown>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { BellOutlined, SearchOutlined } from "@ant-design/icons-vue";
import notificationApi from "../api/notification";
import store from '@/store/index.js';
import echo from '../pusher';
import { useToast } from "vue-toast-notification";

const notifications = ref([]);
const router = useRouter();
const searchText = ref("");
const isDropdownVisible = ref(false);
const pagination = ref({ current: 1, pageSize: 10, total: 0 });
const orderElement = ref('id');
const orderType = ref('desc');
const unread_count = ref(0);
const toast = useToast();

watch(
    () => store.getters["auth/user"], 
    (user) => {
      if (user && user.id) {
        echo.private(`orders.${user.id}`)
            .listen('OrderStatusChanged', (e) => {
                fetchNotifications();
                toast.success(e.message);
            });
      }
    },
    { immediate: true }
  );

const fetchNotifications = async () => {
  try {
    const response = await notificationApi.getNotifications({
      search: searchText.value,
      currentPage: pagination.value.current,
      limit: pagination.value.pageSize,
      order_element: orderElement.value,
      order_type: orderType.value,
    });
    notifications.value = response.data.notifications.data; 
    unread_count.value = response.data.unread_count; 
  } catch (error) {
    console.error("Failed to fetch notifications:", error);
  }
};

onMounted(() => {
  fetchNotifications();
});

const filteredNotifications = computed(() => {
  if (!searchText.value) return notifications.value;
  return notifications.value.filter(notif =>
    notif.message.toLowerCase().includes(searchText.value.toLowerCase())
  );
});

// Format timestamp to a readable string
const formatTime = (timestamp) => {
  const date = new Date(timestamp);
  return date.toLocaleString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    day: "2-digit",
    month: "short",
  });
};

// Mark a single notification as read
const markAsRead = async (index) => {
  try {
    const notification = filteredNotifications.value[index];
    await notificationApi.readNotification(notification.id);
    isDropdownVisible.value = false;
  } catch (error) {
    console.error("Error marking notification as read:", error);
  }
};


// Mark all notifications as read
const markAllAsRead = () => {
  notifications.value.forEach(notif => (notif.read = true));
};

// Navigate to a full notifications page
const viewAllNotifications = () => {
  router.push("/notifications");
  isDropdownVisible.value = false;
};

const handleVisibleChange = (visible) => {
  isDropdownVisible.value = visible;
};
</script>

<style scoped>
.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.15);
  color: #40c4ff;
  transform: translateY(-2px);
}

.nav-icon {
  font-size: 18px;
  color: #fff;
}

.notification-icon {
  cursor: pointer;
  padding-right: 30px;
  transition: all 0.3s ease;
}

.notification-icon:hover .nav-icon {
  color: #40c4ff; /* Blue color on hover */
}

.notification-icon:hover {
  transform: scale(1.1);
}

/* Badge */
:deep(.ant-badge-count) {
  box-shadow: 0 0 0 2px #fff;
  font-size: 12px;
  height: 18px;
  line-height: 18px;
  min-width: 18px;
}

/* Dropdown Container */
.notification-dropdown {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 320px;
}

/* Header */
.dropdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 16px;
  border-bottom: 1px solid #e8e8e8;
}

.header-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

:deep(.ant-btn-link) {
  color: #1890ff;
  font-size: 12px;
}

:deep(.ant-btn-link:hover) {
  color: #40a9ff;
}

/* Search Input */
.search-wrapper {
  padding: 10px 16px;
  border-bottom: 1px solid #e8e8e8;
}

:deep(.ant-input) {
  border-radius: 16px;
  font-size: 13px;
}

/* Notification Menu */
.notification-menu {
  max-height: 250px;
  overflow-y: auto;
  border-radius: 0 0 8px 8px;
}

.notification-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.message {
  font-size: 14px;
  font-weight: 500;
}

.time {
  font-size: 12px;
  color: #999;
}

.unread {
  background-color: rgba(24, 144, 255, 0.1);
}

.unread .message {
  font-weight: 600;
  color: #1890ff;
}

.view-all-item {
  text-align: center;
  border-top: 1px solid #e8e8e8;
}

:deep(.ant-btn-link) {
  color: #1890ff;
  font-weight: 500;
}

:deep(.ant-btn-link:hover) {
  color: #40a9ff;
}
</style>