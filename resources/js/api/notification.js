import { api, apiAdmin } from "@/api/axios";


class NotificationApi {
    getNotifications = (params) => {
        return api.get("/notifications", { params: params });
    }

    readNotification = (id) => {
        return api.post("/notification/read",{id: id});
    }
}

const notificationApi = new NotificationApi();

export default notificationApi;


