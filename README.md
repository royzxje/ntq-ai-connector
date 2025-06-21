# NTQ AI Connector

![Version](https://img.shields.io/badge/version-1.2.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-brightgreen.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-orange.svg)

## 📑 Tổng quan

NTQ AI Connector là plugin kết nối WordPress với OpenRouter API để tự động tóm tắt bài viết và phân tích dữ liệu bằng AI. Plugin cung cấp widget tương tác trên frontend giúp người đọc có thể nhanh chóng nhận được bản tóm tắt của bài viết đang đọc, tiết kiệm thời gian và tăng trải nghiệm người dùng.

## ✨ Tính năng chính

- 🤖 **Tóm tắt bài viết tự động** bằng AI thông qua OpenRouter API
- 🎨 **Widget tương tác** với giao diện đẹp, dễ sử dụng
- ⚙️ **Tùy biến** vị trí hiển thị, màu sắc, văn bản theo ý muốn
- 📊 **Thống kê sử dụng** và quản lý lịch sử tóm tắt
- 📝 **Tùy chỉnh prompt** để tạo những bản tóm tắt theo phong cách riêng
- 🔒 **Giới hạn sử dụng** để kiểm soát chi phí API
- 🌈 **Hỗ trợ Markdown** trong kết quả tóm tắt
- 📱 **Responsive** hoạt động tốt trên mọi thiết bị

## 🔧 Yêu cầu

- WordPress 5.0 trở lên
- PHP 7.4 trở lên
- OpenRouter API key

## 📥 Cài đặt

1. Tải plugin từ [trang chủ](https://q2k1.com) hoặc [GitHub repository](https://github.com/yourusername/ntq-ai-connector)
2. Upload và cài đặt plugin qua menu Plugins trong WordPress Admin
3. Kích hoạt plugin
4. Cấu hình API key và các tùy chọn khác trong trang cài đặt của plugin

## ⚙️ Cấu hình

### Cài đặt API

1. Đăng nhập vào trang quản trị WordPress
2. Truy cập **NTQ AI Connector > Cài đặt**
3. Nhập **API Key** từ OpenRouter
4. Chọn **Model mặc định** (khuyến nghị: deepseek/deepseek-chat-v3-0324:free)
5. Đặt **Giới hạn request/ngày** để kiểm soát chi phí
6. Lưu cài đặt

### Tùy chỉnh Widget

1. Truy cập **NTQ AI Connector > Cài đặt > Cài đặt Widget**
2. Chọn **Vị trí Widget** (góc phải dưới, góc trái dưới, góc phải trên, góc trái trên)
3. Tùy chỉnh **Tiêu đề widget** và **Footer widget**
4. Thay đổi **Màu gradient** theo ý thích
5. Bật/tắt **Hiệu ứng animation**
6. Lưu cài đặt

### Tùy chỉnh Prompt

Plugin cho phép bạn tùy chỉnh prompt gửi đến AI để tạo ra bản tóm tắt theo phong cách mong muốn. Trong cài đặt, bạn có thể chỉnh sửa prompt với các placeholder:

- `{title}`: Tiêu đề bài viết
- `{content}`: Nội dung bài viết

## 🧩 Cách sử dụng

### Đối với người đọc

1. Truy cập bất kỳ bài viết hoặc trang nào trên website
2. Nhấp vào biểu tượng **AI** ở vị trí đã cấu hình
3. Chọn **"Tóm tắt bài viết hiện tại"** từ menu
4. Đợi vài giây để AI tạo bản tóm tắt
5. Đọc bản tóm tắt được định dạng đẹp mắt với Markdown

### Đối với quản trị viên

1. Truy cập **NTQ AI Connector > Dashboard** để xem thống kê sử dụng
2. Truy cập **NTQ AI Connector > Lịch sử** để xem chi tiết các yêu cầu tóm tắt
3. Theo dõi số lượng request đã sử dụng trong ngày và thống kê theo thời gian

## 📊 Thống kê và Báo cáo

Plugin cung cấp các báo cáo và thống kê:

- **Lượt sử dụng theo thời gian** (ngày, tuần, tháng, năm)
- **Model AI được sử dụng nhiều nhất**
- **Thời gian phản hồi trung bình**
- **Bài viết được tóm tắt nhiều nhất**

## 🔌 Tích hợp và Mở rộng

NTQ AI Connector được thiết kế với cấu trúc mở và mô-đun hóa, cho phép dễ dàng mở rộng:

- Thêm các model AI mới từ OpenRouter
- Tích hợp với các plugin phân tích nội dung khác
- Mở rộng chức năng ngoài việc tóm tắt bài viết

## 🔍 Khắc phục sự cố

### API Key không hoạt động

- Kiểm tra lại API key đã nhập đúng chưa
- Đảm bảo API key vẫn còn hiệu lực và có đủ credit
- Kiểm tra logs của plugin để xem chi tiết lỗi

### Widget không hiển thị

- Đảm bảo bạn đã nhập API key hợp lệ
- Kiểm tra xem bạn có đang ở trang bài viết/trang không
- Kiểm tra xem widget có bị tắt trong cài đặt không

### Tóm tắt không chính xác

- Thử điều chỉnh prompt tùy chỉnh
- Thử thay đổi model AI
- Đảm bảo bài viết có đủ nội dung để AI có thể tóm tắt

## 🔄 Cập nhật

### Phiên bản 1.2.0
- Thêm tính năng lưu kết quả tóm tắt trong database
- Cải thiện hiệu suất và tối ưu hóa yêu cầu API
- Thêm tùy chọn nhiệt độ (temperature) và max tokens

### Phiên bản 1.1.0
- Thêm animation và hiệu ứng CSS
- Hỗ trợ nhiều model AI hơn
- Cải thiện giao diện admin và báo cáo thống kê

### Phiên bản 1.0.0
- Phát hành lần đầu
- Tính năng tóm tắt bài viết cơ bản
- Widget tùy chỉnh vị trí

## 📄 Giấy phép

Plugin này được phát hành dưới [Giấy phép GPL v2 hoặc mới hơn](https://www.gnu.org/licenses/gpl-2.0.html).

## 👨‍💻 Tác giả

**NTQ AI Connector** được phát triển bởi ntquan.

Website: [https://q2k1.com](https://q2k1.com)

## 🙏 Cảm ơn

Cảm ơn bạn đã sử dụng NTQ AI Connector! Nếu bạn thấy plugin này hữu ích, hãy đánh giá và để lại nhận xét trên trang plugin của chúng tôi.

---

**Lưu ý:** Plugin này sử dụng OpenRouter API để cung cấp dịch vụ AI. Vui lòng xem xét [điều khoản dịch vụ của OpenRouter](https://openrouter.ai/terms) để biết thêm chi tiết về giới hạn sử dụng và chi phí.
