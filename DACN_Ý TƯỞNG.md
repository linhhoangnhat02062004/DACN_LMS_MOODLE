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
│  Study Agent  │ Progress Agent │ Motivation Agent │
│  (Giải bài)   │ (Theo dõi)     │ (Động viên)      │
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

---

## 🔧 **CHI TIẾT KỸ THUẬT**

### **1. Cách AI Agents hoạt động**

#### **Study Agent - Quy trình giải bài tập:**
```
1. Nhận câu hỏi: "Giải phương trình x² + 5x + 6 = 0"
2. RAG System tìm kiếm: "phương trình bậc 2" trong tài liệu
3. Tìm thấy: "Toán học 10, Chương 2, trang 47: Công thức nghiệm"
4. Tạo prompt: "Dựa trên công thức trong sách, giải phương trình..."
5. LLM xử lý và trả lời từng bước
6. Hiển thị: Giải pháp + Nguồn tham khảo + Confidence score
```

#### **Progress Agent - Phân tích tiến độ:**
```
1. Lấy dữ liệu từ Moodle: Điểm quiz, assignment, thời gian học
2. Phân tích xu hướng: "Điểm Toán tăng 0.5, điểm Lý giảm 0.3"
3. RAG tìm tài liệu: "Vật lý 10, Chương 3" cho điểm Lý thấp
4. Tạo báo cáo: "Bạn cần cải thiện Lý, tài liệu đề xuất:..."
5. Đặt mục tiêu: "Học thêm 1 giờ Lý/ngày trong 2 tuần tới"
```

#### **Motivation Agent - Động viên thông minh:**
```
1. Theo dõi tâm trạng: "Tôi chán học quá"
2. Phân tích ngữ cảnh: Học sinh đã học 80% chương trình
3. RAG tìm câu chuyện: "Câu chuyện thành công của học sinh khác"
4. Tạo động lực: "Bạn đã cố gắng rất nhiều, chỉ còn 20% nữa thôi!"
5. Đặt mục tiêu nhỏ: "Học 30 phút nữa rồi nghỉ"
```

### **2. RAG System hoạt động như thế nào**

#### **Bước 1: Document Processing**
```
- Lấy tài liệu từ Moodle course
- Chia nhỏ thành chunks (1000 ký tự/chunk)
- Tạo embeddings bằng sentence-transformers
- Lưu vào vector database
```

#### **Bước 2: Query Processing**
```
- Học sinh hỏi: "Định lý Bayes là gì?"
- Tạo embedding cho câu hỏi
- Tìm kiếm similarity trong vector database
- Lấy top 5 chunks liên quan nhất
```

#### **Bước 3: Answer Generation**
```
- Tạo prompt: "Dựa trên tài liệu: [chunks]... Trả lời: [câu hỏi]"
- LLM tạo câu trả lời dựa trên context
- Trả về: Câu trả lời + Nguồn tham khảo + Confidence score
```

### **3. Tích hợp với Moodle**

#### **Database Integration:**
```sql
-- Lấy dữ liệu học sinh từ Moodle
SELECT u.username, g.finalgrade, gi.itemname 
FROM mdl_user u
JOIN mdl_grade_grades g ON u.id = g.userid
JOIN mdl_grade_items gi ON g.itemid = gi.id
WHERE gi.courseid = ? AND u.id = ?
```

#### **File System Integration:**
```php
// Lấy tài liệu từ Moodle course
$context = context_course::instance($course_id);
$files = $fs->get_area_files($context->id, 'course', 'content', 0);
foreach ($files as $file) {
    if ($file->get_filename() != '.') {
        $content = $this->extract_content($file);
        $this->process_document($content, $course_id);
    }
}
```

---

## 🎯 **SCENARIOS THỰC TẾ**

### **Scenario 1: Học sinh gặp khó khăn với bài tập**

**Tình huống:** Học sinh lớp 10 gặp khó khăn với bài tập phương trình bậc 2

**Quy trình:**
1. **Học sinh:** "Tôi không biết giải bài này: x² + 5x + 6 = 0"
2. **Student Coordinator:** Phân tích → Giao cho Study Agent
3. **Study Agent:** 
   - RAG tìm kiếm: "phương trình bậc 2"
   - Tìm thấy: "Toán học 10, Chương 2, trang 47-52"
   - Tạo prompt với context từ sách
4. **LLM trả lời:**
   ```
   "Dựa trên sách Toán học 10, Chương 2:
   
   Bước 1: Xác định a=1, b=5, c=6
   Bước 2: Tính Δ = b² - 4ac = 25 - 24 = 1
   Bước 3: Áp dụng công thức nghiệm:
   x₁ = (-b + √Δ)/2a = (-5 + 1)/2 = -2
   x₂ = (-b - √Δ)/2a = (-5 - 1)/2 = -3
   
   Nguồn: Toán học 10, Chương 2, trang 47-52
   Confidence: 95%"
   ```
5. **Study Agent:** Tạo thêm 3 bài tập tương tự để luyện tập

### **Scenario 2: Phân tích tiến độ học tập**

**Tình huống:** Cuối tuần, hệ thống phân tích tiến độ học tập

**Quy trình:**
1. **Progress Agent:** Lấy dữ liệu từ Moodle
   ```
   - Quiz Toán: 8.5/10 (tăng 0.5 so với tuần trước)
   - Quiz Lý: 7.0/10 (giảm 0.3 so với tuần trước)
   - Thời gian học: 5 giờ/ngày (hiệu quả 85%)
   ```

2. **Phân tích:**
   ```
   - Điểm mạnh: Toán học tốt, thời gian học đều đặn
   - Điểm yếu: Vật lý cần cải thiện
   - Vấn đề: Học sinh gặp khó khăn với chủ đề "Điện từ"
   ```

3. **RAG tìm tài liệu:**
   ```
   - Tìm kiếm: "điện từ học sinh lớp 10"
   - Tìm thấy: "Vật lý 10, Chương 3: Điện từ học"
   - Confidence: 90%
   ```

4. **Đề xuất:**
   ```
   "Báo cáo tuần:
   ✅ Điểm Toán: 8.5 (tăng 0.5) - Rất tốt!
   ⚠️ Điểm Lý: 7.0 (cần cải thiện)
   
   Đề xuất:
   - Tăng thời gian học Lý: 1 giờ/ngày
   - Tài liệu: Vật lý 10, Chương 3, trang 78-95
   - Mục tiêu: Đạt 8.0 điểm Lý trong 2 tuần tới"
   ```

### **Scenario 3: Động viên khi học sinh chán nản**

**Tình huống:** Học sinh cảm thấy chán nản và muốn bỏ cuộc

**Quy trình:**
1. **Học sinh:** "Tôi chán học quá, muốn bỏ cuộc"
2. **Student Coordinator:** Phân tích tâm trạng → Giao cho Motivation Agent
3. **Motivation Agent:**
   - Phân tích ngữ cảnh: Học sinh đã học 80% chương trình
   - RAG tìm câu chuyện: "học sinh vượt khó"
   - Tạo động lực phù hợp

4. **LLM trả lời:**
   ```
   "Tôi hiểu bạn đang gặp khó khăn. Nhưng hãy nhìn lại:
   
   🎯 Bạn đã học được 80% chương trình rồi!
   📈 Điểm Toán của bạn đã tăng từ 7.5 lên 8.5
   ⏰ Bạn đã dành 5 giờ/ngày để học - rất đáng khen!
   
   Câu chuyện: Có một học sinh khác cũng từng chán nản ở giai đoạn này,
   nhưng cố gắng thêm 2 tuần nữa và đã đạt điểm cao.
   
   Hãy thử học 30 phút nữa thôi, tôi sẽ tạo game học tập cho bạn!"
   ```

5. **Motivation Agent:** Tạo mini-game học tập để tăng hứng thú

---

## 🛠️ **IMPLEMENTATION CHI TIẾT**

### **1. Cấu trúc code**

#### **Backend (PHP + Python):**
```
local/aichatbot/
├── agents/
│   ├── study_agent.php          # Study Agent logic
│   ├── progress_agent.php       # Progress Agent logic
│   ├── motivation_agent.php     # Motivation Agent logic
│   └── coordinator.php          # Student Coordinator
├── rag/
│   ├── document_processor.php   # Xử lý tài liệu Moodle
│   ├── vector_database.php      # Vector database operations
│   └── rag_pipeline.php         # RAG pipeline
├── python/
│   ├── langchain_handler.py     # LangChain integration
│   ├── rag_system.py           # RAG system
│   └── mcp_server.py           # MCP server
└── templates/
    ├── agent_chat.mustache      # Chat interface
    └── progress_dashboard.mustache # Progress dashboard
```

#### **Frontend (React.js):**
```
src/
├── components/
│   ├── ChatInterface/           # Giao diện chat
│   ├── AgentSelector/           # Chọn AI Agent
│   ├── ProgressDashboard/       # Dashboard tiến độ
│   └── RAGSources/             # Hiển thị nguồn tham khảo
├── services/
│   ├── aiService.js            # Gọi AI APIs
│   └── moodleService.js        # Tích hợp Moodle
└── utils/
    ├── constants.js            # Constants
    └── helpers.js              # Helper functions
```

### **2. Database Schema**

```sql
-- Bảng embeddings cho RAG
CREATE TABLE mdl_local_aichatbot_embeddings (
    id BIGSERIAL PRIMARY KEY,
    document_id BIGINT NOT NULL,
    chunk_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    embedding TEXT NOT NULL, -- JSON array
    metadata TEXT, -- JSON object
    course_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng chat history
CREATE TABLE mdl_local_aichatbot_agent_chat (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    course_id BIGINT,
    agent_type VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    response TEXT,
    sources TEXT, -- JSON array
    confidence DECIMAL(3,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng user preferences
CREATE TABLE mdl_local_aichatbot_user_prefs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    preferred_agent VARCHAR(20) DEFAULT 'study',
    learning_style VARCHAR(20),
    rag_enabled BOOLEAN DEFAULT TRUE,
    preferences TEXT, -- JSON object
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **3. API Endpoints**

```yaml
# Study Agent APIs
POST /api/study/solve-problem
POST /api/study/explain-concept
POST /api/study/generate-exercises

# Progress Agent APIs
GET  /api/progress/overview
GET  /api/progress/analysis
POST /api/progress/set-goals

# Motivation Agent APIs
POST /api/motivation/boost
POST /api/motivation/celebrate
GET  /api/motivation/achievements

# RAG System APIs
POST /api/rag/search
POST /api/rag/query
GET  /api/rag/sources
```

---

## 📊 **METRICS VÀ ĐÁNH GIÁ**

### **1. Technical Metrics**
- **Response Time:** < 2 giây
- **Accuracy:** > 90%
- **Uptime:** > 99.9%
- **RAG Confidence:** > 80%

### **2. Educational Metrics**
- **Student Satisfaction:** > 4.5/5
- **Learning Improvement:** Tăng 20% điểm số
- **Engagement:** Tăng 30% thời gian học
- **Completion Rate:** > 85%

### **3. System Metrics**
- **Concurrent Users:** 100+ học sinh
- **Documents Processed:** 1000+ tài liệu
- **Queries per Day:** 500+ câu hỏi
- **Storage:** 10GB+ vector database

---

## 🚀 **KẾT LUẬN**

Đây là một đồ án sáng tạo và thực tế, kết hợp các công nghệ AI tiên tiến để tạo ra một hệ thống hỗ trợ học sinh thông minh. Với việc tích hợp vào Moodle, hệ thống sẽ tận dụng được cơ sở hạ tầng có sẵn và dễ dàng triển khai trong thực tế.

### **Điểm mạnh:**
- ✅ **Tính mới:** AI Agentic đầu tiên cho học sinh Việt Nam
- ✅ **Ứng dụng thực tế:** Tích hợp với Moodle hiện có
- ✅ **Công nghệ tiên tiến:** LangChain + MCP + RAG
- ✅ **Độ chính xác cao:** RAG System với nguồn tham khảo
- ✅ **Khả năng mở rộng:** Kiến trúc modular

### **Thách thức:**
- ⚠️ **Độ phức tạp kỹ thuật:** Tích hợp nhiều công nghệ AI
- ⚠️ **Thời gian phát triển:** 3 tháng cho prototype
- ⚠️ **Tài nguyên:** Cần GPU cho embeddings
- ⚠️ **Dữ liệu:** Cần tài liệu khóa học đa dạng

### **Tiềm năng:**
- 🚀 **Mở rộng:** Áp dụng cho nhiều môn học
- 🚀 **Thương mại hóa:** Bán cho các trường học
- 🚀 **Nghiên cứu:** Phát triển thành platform
- 🚀 **Quốc tế:** Mở rộng ra thị trường quốc tế

**Đây là một đồ án có tiềm năng lớn và có thể tạo ra tác động tích cực trong lĩnh vực giáo dục!**
