# Hướng dẫn nhúng AI vào Moodle

## 1. Truy cập cấu hình AI

### Bước 1: Đăng nhập với quyền admin
- Truy cập: `http://localhost/moodle/admin`
- Đăng nhập với tài khoản admin

### Bước 2: Vào phần AI Settings
- Điều hướng đến: **Site administration** → **AI** → **AI providers**
- Hoặc truy cập trực tiếp: `http://localhost/moodle/admin/settings.php?section=aiprovider`

## 2. Cấu hình AI Providers

### A. Cấu hình OpenAI (Khuyến nghị)

1. **Tạo tài khoản OpenAI:**
   - Truy cập: https://platform.openai.com/
   - Tạo API key

2. **Thêm OpenAI Provider:**
   - Click "Add new provider"
   - Chọn "OpenAI"
   - Điền thông tin:
     - **Name**: "OpenAI GPT-4"
     - **API Key**: [API key từ OpenAI]
     - **Organization ID**: (tùy chọn)

3. **Cấu hình Actions:**
   - **Generate Text**: Sử dụng cho tạo nội dung
   - **Generate Image**: Sử dụng DALL-E
   - **Summarise Text**: Tóm tắt văn bản
   - **Explain Text**: Giải thích nội dung

### B. Cấu hình Ollama (Chạy cục bộ)

1. **Cài đặt Ollama:**
   ```bash
   # Windows
   # Tải từ: https://ollama.ai/download
   
   # Linux/Mac
   curl -fsSL https://ollama.ai/install.sh | sh
   ```

2. **Chạy model:**
   ```bash
   ollama run llama2
   # hoặc
   ollama run mistral
   ```

3. **Cấu hình trong Moodle:**
   - Endpoint: `http://localhost:11434`
   - Model: `llama2` hoặc `mistral`

### C. Cấu hình Azure AI

1. **Tạo Azure AI Service:**
   - Truy cập Azure Portal
   - Tạo Cognitive Services resource

2. **Cấu hình trong Moodle:**
   - **API Key**: Từ Azure portal
   - **Endpoint**: URL của Azure service

## 3. Kích hoạt AI Placements

### A. Course Assist
- Điều hướng: **Site administration** → **AI** → **AI placements**
- Kích hoạt "Course Assist"
- Cấu hình các tính năng hỗ trợ khóa học

### B. Editor Integration
- Kích hoạt "Editor" placement
- Tích hợp AI vào trình soạn thảo

## 4. Cấu hình Policy và Permissions

### A. AI Policy
- Điều hướng: **Site administration** → **AI** → **AI policy**
- Thiết lập chính sách sử dụng AI
- Yêu cầu người dùng chấp nhận policy

### B. Permissions
- **moodle/ai:useai**: Quyền sử dụng AI
- **moodle/ai:viewaipolicyacceptancereport**: Xem báo cáo policy
- **moodle/ai:viewaiusagereport**: Xem báo cáo sử dụng

## 5. Kiểm tra và Test

### A. Test AI Provider
1. Vào **AI providers** → Click vào provider đã tạo
2. Test connection
3. Kiểm tra logs nếu có lỗi

### B. Test AI Actions
1. Tạo một khóa học test
2. Thử sử dụng AI trong editor
3. Test các tính năng Course Assist

## 6. Monitoring và Reports

### A. AI Usage Report
- **Site administration** → **Reports** → **AI reports** → **AI usage**
- Theo dõi việc sử dụng AI

### B. Policy Acceptance Report
- **Site administration** → **Reports** → **AI reports** → **AI policy acceptance**
- Theo dõi việc chấp nhận policy

## 7. Troubleshooting

### Lỗi thường gặp:

1. **API Key không hợp lệ:**
   - Kiểm tra API key
   - Kiểm tra quota/credit

2. **Connection timeout:**
   - Kiểm tra firewall
   - Kiểm tra network

3. **Model không hỗ trợ:**
   - Kiểm tra model name
   - Kiểm tra provider capabilities

## 8. Best Practices

1. **Bảo mật:**
   - Không chia sẻ API keys
   - Sử dụng HTTPS
   - Thiết lập rate limiting

2. **Performance:**
   - Cache responses khi có thể
   - Sử dụng async processing
   - Monitor usage

3. **User Experience:**
   - Cung cấp clear instructions
   - Hiển thị loading states
   - Handle errors gracefully

## 9. Advanced Configuration

### Custom AI Actions
- Tạo custom actions trong `/ai/classes/aiactions/`
- Extend `\core_ai\aiactions\base`

### Custom Providers
- Tạo custom providers trong `/ai/provider/`
- Extend `\core_ai\provider`

### Custom Placements
- Tạo custom placements trong `/ai/placement/`
- Extend `\core_ai\placement`
