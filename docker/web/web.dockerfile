FROM nginx:stable-alpine

# Copy cấu hình Nginx (Chỉ dùng 1 file, không cần cả 2)
COPY vhost.conf /etc/nginx/conf.d/default.conf

# Copy thêm nếu cần thiết
# COPY ./nginx.conf /etc/nginx/nginx.conf

# Mở port HTTP & HTTPS
EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
