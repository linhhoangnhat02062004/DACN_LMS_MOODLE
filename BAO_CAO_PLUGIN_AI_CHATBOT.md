# BÁO CÁO PHÂN TÍCH PLUGIN AI CHATBOT
## Hệ thống quản lý học tập (LMS) hỗ trợ AI

---

**Thông tin dự án:**
- **Tên đề tài:** Nghiên cứu và phát triển một hệ thống quản lý học tập (Learning Management Systems – LMS) hỗ trợ AI
- **Sinh viên thực hiện:** Hoàng Nhật Linh (2211847), Huỳnh Nga (2111818)
- **CBHD:** PGS. TS. Thoại Nam, TS. Nguyễn Quang Hùng
- **Ngành:** Khoa học máy tính - Chương trình CQ

---

## 1. TỔNG QUAN DỰ ÁN

### 1.1 Mục tiêu chính
Dự án nhằm nghiên cứu và phát triển một hệ thống LMS dựa trên nền tảng Moodle, tích hợp các công cụ AI (GPT, Gemini, Grok) để tạo ra một **trợ giảng ảo (Virtual Teaching Assistant)** với các chức năng:
- Trả lời câu hỏi liên quan đến khóa học
- Sinh câu hỏi mới cho các môn học
- Hỗ trợ học tập đa dạng các môn học

### 1.2 Phạm vi nghiên cứu
- Nghiên cứu kiến trúc Moodle và khả năng tích hợp plugin
- Tích hợp AI API (OpenAI/GPT) vào Moodle
- Phát triển giao diện chat thân thiện
- Xây dựng hệ thống prompt engineering chuyên biệt

---

## 2. PHÂN TÍCH PLUGIN AI CHATBOT HIỆN CÓ

### 2.1 Thông tin plugin
- **Tên plugin:** `local_aichatbot`
- **Phiên bản:** 1.0.0 (2024120100)
- **Loại:** Local Plugin (Plugin địa phương)
- **Yêu cầu:** Moodle 4.0+ (2022041900)
- **Trạng thái:** MATURITY_STABLE

### 2.2 Cấu trúc plugin

```
local/aichatbot/
├── version.php                    # Thông tin phiên bản và hooks
├── settings.php                   # Cài đặt cơ bản (API, model)
├── index.php                      # Giao diện chat chính
├── ajax.php                       # Xử lý AJAX cho chat cơ bản
├── educational_settings.php       # Cài đặt tính năng giáo dục
├── educational_ajax.php           # Xử lý AJAX cho tính năng giáo dục
├── educational_features.php       # Logic tính năng giáo dục
├── floating_chatbot.php           # Chatbot nổi
├── footer_hook.php                # Hook hiển thị chatbot
└── lib.php                        # Thư viện hỗ trợ
```

### 2.3 Các tính năng đã triển khai

#### A. Cấu hình AI API
- **API Key:** Cấu hình key cho OpenAI/GPT
- **API URL:** Endpoint API (mặc định: https://api.openai.com/v1/chat/completions)
- **Model:** Model AI sử dụng (gpt-3.5-turbo)
- **Max Tokens:** Giới hạn độ dài phản hồi (1000)
- **Temperature:** Độ sáng tạo của AI (0.7)

#### B. Giao diện người dùng
- **Chat container:** Thiết kế đẹp mắt, responsive
- **Message display:** Hỗ trợ format markdown
- **Typing indicator:** Hiệu ứng đang gõ
- **Auto-scroll:** Tự động cuộn khi có tin nhắn mới
- **Mobile-friendly:** Tương thích với thiết bị di động

#### C. Xử lý thông minh
- **Dual-mode operation:**
  - Real AI API: Gọi trực tiếp OpenAI/GPT
  - Rule-based: Phản hồi theo quy tắc có sẵn (fallback)
- **Error handling:** Xử lý lỗi API và fallback an toàn

---

## 3. TÍNH NĂNG GIÁO DỤC CHUYÊN BIỆT

### 3.1 Tự động phát hiện môn học
Plugin có khả năng phân tích tên khóa học để tự động xác định môn học:

```php
function detect_subject_from_course($course) {
    // Hỗ trợ các môn: Toán, Lý, Hóa, Sinh, Tin học, 
    // Tiếng Anh, Văn, Sử, Địa, GDCD
}
```

### 3.2 Phân loại câu hỏi thông minh
Hệ thống tự động phân loại câu hỏi thành các loại:
- Giải thích khái niệm
- Hướng dẫn làm bài
- Giải bài tập
- Sinh câu hỏi
- Tài liệu tham khảo
- Ôn tập
- Thực hành

### 3.3 Prompt Engineering chuyên biệt

#### A. Prompt theo môn học:
- **Toán học:** Chuyên về công thức, tính toán, ví dụ minh họa
- **Vật lý:** Định luật vật lý, hiện tượng tự nhiên, ứng dụng thực tế
- **Hóa học:** Phản ứng hóa học, cấu trúc phân tử, tính chất chất
- **Sinh học:** Quá trình sinh học, cơ thể sống, môi trường
- **Tin học:** Lập trình, code examples, best practices
- **Tiếng Anh:** Ngữ pháp, từ vựng, kỹ năng giao tiếp

#### B. Prompt theo loại câu hỏi:
Mỗi loại câu hỏi có cấu trúc trả lời riêng biệt để đảm bảo chất lượng phản hồi.

### 3.4 Tính năng hỗ trợ học tập

#### A. Mẹo học tập theo môn học:
- **Toán:** Học công thức, luyện tập thường xuyên, ghi chép rõ ràng
- **Lý:** Quan sát thực tế, vẽ sơ đồ, tính toán cẩn thận
- **Hóa:** Hiểu cấu trúc, phản ứng hóa học, bảng tuần hoàn
- **Sinh:** Quan sát tự nhiên, học từ hình ảnh, hiểu quá trình

#### B. Hệ thống động viên:
- 30% cơ hội hiển thị lời động viên trong câu trả lời
- 8 mẫu lời động viên khác nhau
- Tập trung vào khuyến khích học tập tích cực

#### C. Sinh câu hỏi tự động:
- Tạo câu hỏi theo chủ đề và độ khó
- Hỗ trợ đa dạng loại câu hỏi (trắc nghiệm, tự luận, thực hành)
- Cung cấp gợi ý đáp án

---

## 4. KIẾN TRÚC HỆ THỐNG

### 4.1 Luồng xử lý tin nhắn

```
1. User gửi tin nhắn → index.php
2. JavaScript gọi ajax.php
3. Phân tích câu hỏi (môn học, loại câu hỏi)
4. Tạo prompt chuyên biệt
5. Gọi AI API hoặc rule-based response
6. Trả về kết quả với format đẹp
7. Hiển thị trong chat interface
```

### 4.2 Cơ chế fallback
- **Primary:** Gọi AI API (OpenAI/GPT)
- **Fallback:** Rule-based responses khi API lỗi
- **Error handling:** Xử lý lỗi mạng, API, timeout

### 4.3 Bảo mật và hiệu suất
- **API Key:** Lưu trữ an toàn trong cấu hình Moodle
- **Timeout:** Giới hạn thời gian gọi API (30s)
- **Rate limiting:** Có thể mở rộng để giới hạn số lượng request
- **Caching:** Có thể mở rộng để cache câu trả lời phổ biến



## 5. HƯỚNG PHÁT TRIỂN TIẾP THEO

### 5.1 Giai đoạn 2 - Nâng cao
1. **Tích hợp đa AI models:**
   - Thêm hỗ trợ Gemini API
   - Thêm hỗ trợ Grok API
   - So sánh và chọn model tốt nhất

2. **Tính năng phân tích học tập:**
   - Theo dõi tiến độ học tập
   - Phân tích điểm yếu của học sinh
   - Đề xuất lộ trình học tập cá nhân hóa

3. **Tích hợp sâu hơn với Moodle:**
   - Tích hợp với hệ thống quiz
   - Tích hợp với gradebook
   - Tích hợp với forum discussions

### 5.2 Giai đoạn 3 - Tối ưu
1. **Hiệu suất và bảo mật:**
   - Caching system
   - Rate limiting
   - Data privacy compliance

2. **Tính năng nâng cao:**
   - Voice chat
   - Image analysis
   - Multi-language support

---

**Sinh viên thực hiện:** Hoàng Nhật Linh, Huỳnh Nga  
**CBHD:** PGS. TS. Thoại Nam, TS. Nguyễn Quang Hùng
