# PHÁC HỌA Ý TƯỞNG ĐỒ ÁN TỐT NGHIỆP
## Hệ thống AI Agentic hỗ trợ học sinh trong Moodle

---

## 🎯 **Ý TƯỞNG CHÍNH**

**Tên đề tài:** "Xây dựng hệ thống AI Agentic thông minh hỗ trợ học sinh sử dụng LangChain, MCP Server và RAG trong môi trường Moodle"

**Mục tiêu:** Tạo ra một hệ thống AI có khả năng hiểu và hỗ trợ học sinh một cách thông minh, dựa trên tài liệu khóa học thực tế.

---

## 🏗️ **KIẾN TRÚC HỆ THỐNG**

```
┌─────────────────────────────────────────┐
│           AI STUDENT SUPPORT            │
├─────────────────────────────────────────┤
│ Study Agent│Progress Agent│ Motivation  |
|            |              |   Agent     │
│ (Giải bài) │ (Theo dõi)   │ (Động viên) │
├─────────────────────────────────────────┤
│           Student Coordinator           │
│        (Điều phối các Agent)            │
├─────────────────────────────────────────┤
│  RAG System  │ MCP Server  │ LangChain  │
│  (Tìm kiếm)  │ (Giao tiếp) │ (Xử lý)    │
├─────────────────────────────────────────┤
│        Moodle Database & Files          │
└─────────────────────────────────────────┘
```

---

## 🤖 **4 AI AGENT CHÍNH**

### **1. Study Agent (AI Học tập)**
- **Chức năng:** Giải bài tập, giải thích khái niệm
- **Cách hoạt động:** Tìm tài liệu liên quan → Tạo câu trả lời dựa trên tài liệu
- **Ví dụ:** "Giải phương trình x² + 5x + 6 = 0" → Tìm công thức trong sách → Giải từng bước

### **2. Progress Agent (AI Theo dõi)**
- **Chức năng:** Phân tích tiến độ học tập
- **Cách hoạt động:** Lấy điểm số từ Moodle → Phân tích điểm mạnh/yếu → Đề xuất cải thiện
- **Ví dụ:** "Điểm Toán tăng, điểm Lý giảm" → Tìm tài liệu cải thiện Lý

### **3. Motivation Agent (AI Động viên)**
- **Chức năng:** Tạo động lực học tập
- **Cách hoạt động:** Theo dõi tâm trạng → Gửi lời khích lệ → Đặt mục tiêu
- **Ví dụ:** "Bạn đã học 80% chương trình, cố gắng thêm 30 phút nữa!"

### **4. Student Coordinator (Điều phối viên)**
- **Chức năng:** Quản lý và phân phối nhiệm vụ cho các Agent
- **Cách hoạt động:** Phân tích câu hỏi → Chọn Agent phù hợp → Điều phối giao tiếp

---

## 🔍 **RAG SYSTEM (Retrieval-Augmented Generation)**

### **Cách hoạt động:**
1. **Retrieve (Tìm kiếm):** Tìm tài liệu liên quan từ khóa học
2. **Augment (Tăng cường):** Bổ sung thông tin vào câu hỏi
3. **Generate (Tạo sinh):** Tạo câu trả lời dựa trên tài liệu

### **Ví dụ:**
```
Học sinh: "Định lý Bayes là gì?"
↓
RAG System: Tìm trong tài liệu "Xác suất thống kê, Chương 3"
↓
AI: Giải thích dựa trên tài liệu + Đưa ra nguồn tham khảo
```

---

## 🛠️ **CÔNG NGHỆ SỬ DỤNG**

### **Backend:**
- **Python:** LangChain, RAG System
- **PHP:** Tích hợp với Moodle
- **Database:** PostgreSQL + Vector Database

### **AI/ML:**
- **LangChain:** Xử lý ngôn ngữ tự nhiên
- **RAG:** Tăng độ chính xác
- **OpenAI/Ollama:** Large Language Models

### **Frontend:**
- **React.js:** Giao diện chat
- **WebSocket:** Real-time communication

---

## 📊 **TÍNH NĂNG CHÍNH**

### **1. Học tập thông minh**
- Giải bài tập dựa trên tài liệu khóa học
- Giải thích khái niệm với ví dụ cụ thể
- Tạo bài tập luyện tập tương tự

### **2. Theo dõi tiến độ**
- Phân tích điểm số từ Moodle
- Phát hiện điểm mạnh/yếu
- Đề xuất tài liệu cải thiện

### **3. Động viên học tập**
- Theo dõi tâm trạng học sinh
- Gửi lời khích lệ kịp thời
- Đặt mục tiêu học tập phù hợp

### **4. Tìm kiếm thông minh**
- Tìm kiếm trong tài liệu khóa học
- Đưa ra nguồn tham khảo chính xác
- Hiển thị độ tin cậy của câu trả lời

---

## 🎯 **DEMO SCENARIOS**

### **Demo 1: Giải bài tập Toán**
```
Học sinh: "Giải phương trình x² + 5x + 6 = 0"
AI: Tìm công thức trong sách → Giải từng bước → Tạo bài tập tương tự
Nguồn: "Toán học 10, Chương 2, trang 45-60"
```

### **Demo 2: Phân tích tiến độ**
```
AI: "Điểm Toán: 8.5 (tăng 0.5), Điểm Lý: 7.0 (cần cải thiện)"
Đề xuất: Tìm tài liệu "Vật lý 10, Chương 3" để cải thiện
```

### **Demo 3: Động viên học tập**
```
Học sinh: "Tôi chán học quá"
AI: "Bạn đã học 80% chương trình rồi, cố gắng thêm 30 phút nữa!"
```

---

## 📅 **KẾ HOẠCH THỰC HIỆN (3 THÁNG)**

### **Tháng 1: Nghiên cứu và thiết kế**
- Nghiên cứu Agentic AI, LangChain, RAG
- Thiết kế kiến trúc hệ thống
- Chuẩn bị môi trường phát triển

### **Tháng 2: Phát triển core system**
- Tạo AI Agents (Study, Progress, Motivation)
- Tích hợp RAG System
- Tạo giao diện chat cơ bản

### **Tháng 3: Tích hợp và hoàn thiện**
- Tích hợp với Moodle database
- Tạo Knowledge Base từ tài liệu khóa học
- Testing và tối ưu hóa

---

## 🎁 **SẢN PHẨM DEMO**

### **Sau 3 tháng sẽ có:**
- ✅ Hệ thống AI Chatbot hoàn chỉnh
- ✅ 3 AI Agent chuyên biệt
- ✅ RAG System tìm kiếm thông minh
- ✅ Giao diện chat thân thiện
- ✅ Tích hợp với Moodle
- ✅ 5 demo scenarios hoàn chỉnh

---

## 💡 **TÍNH MỚI VÀ ĐÓNG GÓP**

### **Tính mới:**
- AI Agentic đầu tiên cho học sinh Việt Nam
- Tích hợp LangChain + MCP + RAG
- Hệ thống tìm kiếm thông minh trong tài liệu

### **Đóng góp:**
- Cải thiện chất lượng học tập
- Hỗ trợ học sinh 24/7
- Độ chính xác cao với RAG
- Nguồn tham khảo rõ ràng

---

## 🚀 **KẾT LUẬN**

Đây là một đồ án sáng tạo và thực tế, kết hợp các công nghệ AI tiên tiến để tạo ra một hệ thống hỗ trợ học sinh thông minh. Với việc tích hợp vào Moodle, hệ thống sẽ tận dụng được cơ sở hạ tầng có sẵn và dễ dàng triển khai trong thực tế.

**Điểm mạnh:** Tính mới, ứng dụng thực tế, công nghệ tiên tiến
**Thách thức:** Độ phức tạp kỹ thuật, thời gian phát triển
**Tiềm năng:** Có thể mở rộng và thương mại hóa

