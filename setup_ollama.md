# Hướng dẫn cài đặt Ollama để test AI

## 1. Cài đặt Ollama

### Windows:
1. Tải Ollama từ: https://ollama.ai/download
2. Cài đặt và chạy Ollama
3. Mở Command Prompt và chạy:
```bash
ollama run llama2
```

### Linux/Mac:
```bash
curl -fsSL https://ollama.ai/install.sh | sh
ollama run llama2
```

## 2. Cấu hình trong Moodle

1. Truy cập: `http://localhost/moodle/admin/settings.php?section=aiprovider`
2. Click "Add new provider"
3. Chọn "Ollama"
4. Điền thông tin:
   - **Name**: "Ollama Local"
   - **Endpoint**: `http://localhost:11434`
   - **Model**: `llama2` (hoặc model khác)

## 3. Test Ollama

1. Truy cập: `http://localhost/moodle/ai_demo.php`
2. Chọn "Ollama Local"
3. Chọn action "Generate Text"
4. Nhập prompt: "Viết một đoạn văn ngắn về AI"
5. Click "Chạy AI Action"

## 4. Troubleshooting

### Ollama không chạy:
```bash
# Kiểm tra Ollama có chạy không
curl http://localhost:11434/api/tags

# Khởi động lại Ollama
ollama serve
```

### Model chưa tải:
```bash
# Tải model
ollama pull llama2

# Hoặc model nhỏ hơn
ollama pull tinyllama
```

### Lỗi connection:
- Kiểm tra firewall
- Đảm bảo Ollama chạy trên port 11434
- Kiểm tra endpoint trong Moodle
