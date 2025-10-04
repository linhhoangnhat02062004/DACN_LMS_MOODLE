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

## 2. PHÂN TÍCH PLUGIN AI CHATBOT MỚI ĐƯỢC THÊM VÀO

### 2.1 Thông tin plugin
- **Tên plugin:** `local_aichatbot`
- **Loại:** Local Plugin


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
- **API URL:** http://localhost:11434
- **Model:** deepseek-r1:8b 
- **Max Tokens:** Giới hạn độ dài phản hồi (1000)
- **Temperature:** Độ sáng tạo của AI (0.7)

#### B. Giao diện người dùng
- **Chat container** 
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

---

## 4. KIẾN TRÚC HỆ THỐNG

### 4.1 Tổng quan kiến trúc

Hệ thống AI Chatbot được xây dựng theo kiến trúc **3-tier** với các thành phần chính:

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                       │
├─────────────────────────────────────────────────────────────┤
│  • Giao diện chat (index.php)                              │
│  • Floating chatbot widget                                 │
│  • Responsive UI components                                │
│  • JavaScript/AJAX handlers                                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                     │
├─────────────────────────────────────────────────────────────┤
│  • Educational features (educational_features.php)         │
│  • Subject detection & question classification             │
│  • Prompt engineering system                               │
│  • AI response processing                                  │
│  • Rule-based fallback system                              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATA ACCESS LAYER                      │
├─────────────────────────────────────────────────────────────┤
│  • Moodle configuration (get_config)                       │
│  • AI API integration (Ollama/OpenAI)                      │
│  • Course context extraction                               │
│  • User session management                                 │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Kiến trúc chi tiết các thành phần

#### A. Frontend Architecture
```javascript
// Cấu trúc JavaScript
├── Chat Interface (index.php)
│   ├── Message Display System
│   ├── Input Handling
│   ├── Typing Indicators
│   └── Auto-scroll Management
│
├── AJAX Communication
│   ├── Send Message Handler
│   ├── Response Processing
│   ├── Error Handling
│   └── Loading States
│
└── UI Components
    ├── Responsive Design
    ├── Markdown Rendering
    ├── Mobile Optimization
    └── Accessibility Features
```

#### B. Backend Architecture
```php
// Cấu trúc PHP Backend
├── Core Handlers
│   ├── ajax.php (Basic chat)
│   ├── educational_ajax.php (Advanced features)
│   └── ollama_handler.php (AI integration)
│
├── Educational System
│   ├── Subject Detection
│   ├── Question Classification
│   ├── Prompt Engineering
│   └── Response Optimization
│
├── AI Integration
│   ├── Ollama API (Primary - localhost:11434)
│   ├── OpenAI API (Fallback)
│   ├── Model Management
│   └── Error Handling
│
└── Configuration
    ├── Settings Management
    ├── User Preferences
    ├── Course Context
    └── Security Settings
```

### 4.3 Luồng xử lý tin nhắn chi tiết

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Input    │───▶│  Frontend JS    │───▶│   AJAX Call     │
│   (Message)     │    │  (index.php)    │    │  (ajax.php)     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                                        │
                                                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  AI Response    │◀───│  AI API Call    │◀───│ Message Analysis│
│  Processing     │    │ (Ollama/OpenAI) │    │ & Prompt Build  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Response Format │    │ Error Handling  │    │ Rule-based      │
│ & Enhancement   │    │ & Fallback      │    │ Fallback        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │
         ▼
┌─────────────────┐    ┌─────────────────┐
│ JSON Response   │───▶│ Frontend Update │
│ to Frontend     │    │ & Display       │
└─────────────────┘    └─────────────────┘
```

### 4.4 Hệ thống AI Integration

#### A. Dual AI Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                    AI INTEGRATION LAYER                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────┐              ┌─────────────────┐      │
│  │   OLLAMA API    │              │   OPENAI API    │      │
│  │ (Primary)       │              │  (Fallback)     │      │
│  │                 │              │                 │      │
│  │ • Local Server  │              │ • Cloud Service │      │
│  │ • Port: 11434   │              │ • GPT Models    │      │
│  │ • Free Usage    │              │ • API Key       │      │
│  │ • Privacy Safe  │              │ • Internet Req  │      │
│  └─────────────────┘              └─────────────────┘      │
│           │                                │                │
│           └────────────┬───────────────────┘                │
│                        ▼                                    │
│              ┌─────────────────┐                            │
│              │  AI SELECTOR    │                            │
│              │  & MANAGER      │                            │
│              │                 │                            │
│              │ • Auto-failover │                            │
│              │ • Load Balance  │                            │
│              │ • Error Handle  │                            │
│              └─────────────────┘                            │
└─────────────────────────────────────────────────────────────┘
```



### 4.5 Educational Intelligence System

#### A. Subject Detection Engine
```php
// Intelligent Subject Detection
function detect_subject_from_course($course) {
    $subjects = [
        'toán' => ['toán', 'math', 'mathematics', 'algebra'],
        'lý' => ['lý', 'physics', 'vật lý', 'mechanics'],
        'hóa' => ['hóa', 'chemistry', 'hóa học', 'organic'],
        'sinh' => ['sinh', 'biology', 'sinh học', 'genetics'],
        'tin học' => ['tin', 'computer', 'programming', 'coding'],
        'tiếng anh' => ['english', 'tiếng anh', 'language', 'grammar']
    ];
    
    // Fuzzy matching algorithm
    // Context analysis
    // Confidence scoring
}
```



### 4.6 Prompt Engineering Architecture

#### A. Multi-layered Prompt System
```
┌─────────────────────────────────────────────────────────────┐
│                  PROMPT ENGINEERING SYSTEM                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌──────────────┐ │
│  │  BASE PROMPT    │  │ SUBJECT PROMPT  │  │ TYPE PROMPT  │ │
│  │                 │  │                 │  │              │ │
│  │ • Role Definition│  │ • Math Expert   │  │ • Concept    │ │
│  │ • Behavior Rules│  │ • Physics Expert│  │ • Step-by-step│ │
│  │ • Response Style│  │ • Chemistry Exp │  │ • Problem    │ │
│  │ • Language      │  │ • Biology Expert│  │ • Generation │ │
│  └─────────────────┘  └─────────────────┘  └──────────────┘ │
│           │                     │                   │       │
│           └─────────┬───────────┴─────────┬─────────┘       │
│                     ▼                     ▼                 │
│              ┌─────────────────┐  ┌─────────────────┐      │
│              │ CONTEXT PROMPT  │  │ FINAL PROMPT    │      │
│              │                 │  │                 │      │
│              │ • Course Info   │  │ • Combined      │      │
│              │ • User Info     │  │ • Optimized     │      │
│              │ • Session Data  │  │ • Ready for AI  │      │
│              └─────────────────┘  └─────────────────┘      │
└─────────────────────────────────────────────────────────────┘
```


### 4.7 Cơ chế fallback và error handling

#### A. Multi-level Fallback System
```
┌─────────────────────────────────────────────────────────────┐
│                    FALLBACK SYSTEM                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Level 1: Ollama API (Primary)                             │
│  ├── Success: Return AI Response                           │
│  └── Failure: → Level 2                                    │
│                                                             │
│  Level 2: OpenAI API (Secondary)                           │
│  ├── Success: Return AI Response                           │
│  └── Failure: → Level 3                                    │
│                                                             │
│  Level 3: Rule-based System (Fallback)                     │
│  ├── Pattern Matching                                      │
│  ├── Predefined Responses                                  │
│  └── Educational Templates                                 │
│                                                             │
│  Level 4: Error Message (Last Resort)                      │
│  └── User-friendly Error + Retry Option                    │
└─────────────────────────────────────────────────────────────┘
```



### 4.8 Scalability và Extensibility

#### A. Modular Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                  MODULAR ARCHITECTURE                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌──────────────┐ │
│  │   CORE MODULE   │  │ EDUCATIONAL     │  │ AI INTEGRATION│ │
│  │                 │  │ MODULE          │  │ MODULE        │ │
│  │ • Basic Chat    │  │ • Subject Detect│  │ • Ollama      │ │
│  │ • UI Components │  │ • Question Class│  │ • OpenAI      │ │
│  │ • AJAX Handler  │  │ • Prompt Eng    │  │ • Future APIs │ │
│  └─────────────────┘  └─────────────────┘  └──────────────┘ │
│                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌──────────────┐ │
│  │ ANALYTICS       │  │ SECURITY        │  │ EXTENSIONS   │ │
│  │ MODULE          │  │ MODULE          │  │ MODULE       │ │
│  │ • Usage Stats   │  │ • Auth & Auth   │  │              │ │
│  │ • Performance   │  │ • Data Privacy  │  │ • Image AI   │ │
│  │ • Learning Data │  │ • Rate Limiting │  │ • Multi-lang │ │
│  └─────────────────┘  └─────────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────────┘
```



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

### 5.2 Giai đoạn 3 - Tối ưu
1. **Hiệu suất và bảo mật:**
   - Caching system
   - Rate limiting

2. **Tính năng nâng cao:**
   - Image analysis
   - Multi-language support

---

**Sinh viên thực hiện:** Hoàng Nhật Linh, Huỳnh Nga  
**CBHD:** PGS. TS. Thoại Nam, TS. Nguyễn Quang Hùng






