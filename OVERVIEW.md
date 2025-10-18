# TỔNG QUAN HỆ THỐNG LMS HỖ TRỢ AI

## 1. Giới thiệu

### **Tên dự án**
**Learning Management System (LMS) with AI Support** - Hệ thống Quản lý Học tập hỗ trợ AI

### **Mục tiêu**
Xây dựng một nền tảng học tập trực tuyến hiện đại, tích hợp AI để:
- Tự động sinh câu hỏi từ nội dung bài học
- Hỗ trợ học tập với AI Tutor chatbot
- Áp dụng phương pháp Micro Learning (bài học ngắn 5-15 phút)
- Tích hợp với Moodle để đồng bộ dữ liệu

### **Người dùng mục tiêu**
- 👨‍🎓 **Sinh viên**: Học online, làm bài tập, chat với AI tutor
- 👨‍🏫 **Giáo viên**: Tạo khóa học, quản lý nội dung, sinh câu hỏi bằng AI
- 👨‍💼 **Quản trị viên**: Quản lý hệ thống, đồng bộ Moodle

---

## 2. Kiến trúc Hệ thống

### **Tech Stack**

#### **Frontend**
- **Framework**: React.js 18+
- **Routing**: React Router v6
- **State Management**: Redux Toolkit hoặc Zustand
- **HTTP Client**: Axios
- **UI/Styling**: TailwindCSS
- **Build Tool**: Vite
- **Icons**: Lucide React
- **Markdown**: React Markdown (hiển thị nội dung AI)

#### **Backend**
- **Runtime**: Node.js 18+
- **Framework**: Express.js
- **Database**: PostgreSQL 15+
- **ORM**: Sequelize
- **Authentication**: JWT (JSON Web Token)
- **Real-time**: Socket.io (chat)
- **Cron Jobs**: node-cron (đồng bộ Moodle)

#### **AI Integration**
- **OpenAI API**: GPT-4, GPT-3.5 (sinh câu hỏi, chatbot)
- **Google Gemini API**: Gemini Pro (alternative AI model)
- **LangChain**: Framework orchestration AI workflows
- **Moodle Web Services**: Tích hợp Moodle

#### **DevOps**
- **Version Control**: Git
- **Container**: Docker (optional)
- **Deployment**: VPS / Cloud (AWS, Azure, GCP)

---

## 3. Cấu trúc Dự án

### **Cấu trúc Thư mục**

```
lms-ai-platform/
├── frontend/                    # React.js Frontend
│   ├── public/
│   ├── src/
│   │   ├── components/         # UI Components
│   │   │   ├── common/        # Button, Input, Card...
│   │   │   ├── layout/        # Navbar, Sidebar, Footer
│   │   │   ├── course/        # CourseList, CourseDetail
│   │   │   ├── quiz/          # QuizTaker, QuestionGenerator
│   │   │   └── chat/          # ChatInterface, MessageList
│   │   ├── pages/             # Page Components
│   │   │   ├── Home.jsx
│   │   │   ├── Login.jsx
│   │   │   ├── Dashboard.jsx
│   │   │   ├── Courses.jsx
│   │   │   └── AITutor.jsx
│   │   ├── hooks/             # Custom Hooks
│   │   ├── services/          # API Services
│   │   ├── store/             # Redux Store
│   │   ├── utils/             # Utilities
│   │   ├── App.jsx
│   │   └── main.jsx
│   ├── package.json
│   └── vite.config.js
│
├── backend/                     # Node.js Backend
│   ├── src/
│   │   ├── config/            # Configuration
│   │   │   ├── database.js
│   │   │   └── moodle.config.js
│   │   ├── models/            # Database Models (Sequelize)
│   │   │   ├── User.js
│   │   │   ├── Course.js
│   │   │   ├── Topic.js
│   │   │   ├── Quiz.js
│   │   │   └── ...
│   │   ├── controllers/       # Request Handlers
│   │   │   ├── authController.js
│   │   │   ├── courseController.js
│   │   │   ├── quizController.js
│   │   │   └── chatController.js
│   │   ├── services/          # Business Logic
│   │   │   ├── aiService.js           # AI Integration
│   │   │   ├── moodleService.js       # Moodle API
│   │   │   ├── syncService.js         # Sync Moodle
│   │   │   └── questionGeneratorService.js
│   │   ├── routes/            # API Routes
│   │   │   ├── auth.routes.js
│   │   │   ├── courses.routes.js
│   │   │   ├── quizzes.routes.js
│   │   │   ├── chat.routes.js
│   │   │   └── sync.routes.js
│   │   ├── middleware/        # Middleware
│   │   │   ├── auth.middleware.js
│   │   │   ├── validation.middleware.js
│   │   │   └── errorHandler.middleware.js
│   │   ├── jobs/              # Cron Jobs
│   │   │   └── syncCronJob.js
│   │   ├── utils/             # Utilities
│   │   └── app.js             # Entry Point
│   ├── package.json
│   └── .env
│
├── database/                    # Database Scripts
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
│
├── docs/                        # Documentation
│   ├── LMS_AI_ERD.md
│   ├── MOODLE_INTEGRATION.md
│   └── API_DOCUMENTATION.md
│
└── README.md
```

---

## 4. Database Schema (10 bảng)

### **Core Tables (4)**
1. **USER** - Người dùng (student/teacher/admin)
2. **COURSE** - Khóa học
3. **ENROLLMENT** - Đăng ký học (liên kết user-course)
4. **TOPIC** - Chủ đề micro learning (5-15 phút)

### **Quiz Tables (4)**
5. **QUESTION_BANK** - Ngân hàng câu hỏi (AI + manual)
6. **QUIZ** - Bài kiểm tra
7. **QUIZ_QUESTION** - Liên kết quiz-question
8. **STUDENT_ATTEMPT** - Bài làm của sinh viên

### **Chat Tables (2)**
9. **CHAT_SESSION** - Phiên chat với AI
10. **CHAT_MESSAGE** - Tin nhắn chat

**Chi tiết xem:** `LMS_AI_ERD.md`

---

## 5. Tính năng Chính

### **5.1. Quản lý Người dùng**
- ✅ Đăng ký/Đăng nhập (JWT Authentication)
- ✅ Phân quyền: Student, Teacher, Admin
- ✅ Profile management
- ✅ Đồng bộ users từ Moodle

### **5.2. Quản lý Khóa học (Micro Learning)**
- ✅ Tạo/Chỉnh sửa khóa học
- ✅ Chia khóa học thành Topics (micro learning units)
- ✅ Mỗi topic = 1 bài học ngắn (5-15 phút)
- ✅ Nội dung hỗ trợ text, markdown, video
- ✅ Đồng bộ courses từ Moodle

**Ví dụ cấu trúc:**
```
Khóa học: "Lập trình JavaScript"
  ├─ Topic 1: Biến và kiểu dữ liệu (10 phút)
  ├─ Topic 2: Toán tử (8 phút)
  ├─ Topic 3: Functions (12 phút)
  ├─ Topic 4: Arrays (15 phút)
  └─ Topic 5: Async/Await (15 phút)
```

### **5.3. AI Sinh Câu hỏi Tự động**

#### **Quy trình:**
```
1. Giáo viên chọn Topic
   ↓
2. Nhấn "Sinh câu hỏi bằng AI"
   ↓
3. Nhập số lượng câu hỏi, độ khó
   ↓
4. Backend gọi OpenAI/Gemini API
   ↓
5. AI đọc nội dung Topic.content
   ↓
6. AI sinh câu hỏi (JSON format)
   ↓
7. Lưu vào QUESTION_BANK (is_ai_generated=true)
   ↓
8. Giáo viên review và chỉnh sửa
```

#### **Prompt Template:**
```
Dựa trên nội dung bài học sau về JavaScript:

{topic_content}

Hãy tạo {num_questions} câu hỏi trắc nghiệm với:
- Độ khó: {difficulty}
- Mỗi câu có 4 đáp án A, B, C, D
- Chỉ 1 đáp án đúng
- Có giải thích

Format JSON:
{
  "questions": [
    {
      "question_text": "...",
      "answer_options": {"A": "...", "B": "...", "C": "...", "D": "..."},
      "correct_answer": "B",
      "explanation": "..."
    }
  ]
}
```

### **5.4. Quiz và Kiểm tra**
- ✅ Tạo quiz từ QUESTION_BANK
- ✅ Quiz micro learning (sau mỗi topic)
- ✅ Hỗ trợ nhiều loại câu hỏi:
  - Multiple choice (trắc nghiệm)
  - True/False (đúng/sai)
  - Essay (tự luận)
- ✅ Tự động chấm điểm câu trắc nghiệm
- ✅ Giáo viên chấm câu tự luận
- ✅ Lưu lịch sử làm bài

### **5.5. AI Tutor Chatbot**

#### **Tính năng:**
- 💬 Chat real-time với AI
- 🎯 AI trả lời dựa trên nội dung khóa học
- 📚 Context-aware (hiểu ngữ cảnh)
- 🔄 Lưu lịch sử hội thoại
- 🎓 Hỗ trợ học tập 24/7

#### **Quy trình:**
```
1. Sinh viên mở chat trong khóa học
   ↓
2. Chọn chat về toàn khóa học hoặc topic cụ thể
   ↓
3. Gửi câu hỏi: "Arrow function khác gì function thông thường?"
   ↓
4. Backend:
   - Lấy nội dung TOPIC từ DB
   - Build context cho AI
   - Gọi OpenAI/Gemini API
   ↓
5. AI trả lời với context từ bài học
   ↓
6. Stream response về frontend (real-time)
   ↓
7. Lưu conversation vào CHAT_MESSAGE
```

#### **Ví dụ hội thoại:**

**User:** "Arrow function khác gì function thông thường?"

**AI Assistant:** 
```
Arrow function khác function thông thường ở 3 điểm chính:

1. Cú pháp ngắn gọn hơn
2. Không có binding this riêng
3. Không có arguments object

Ví dụ:
// Function thông thường
function add(a, b) { return a + b; }

// Arrow function
const add = (a, b) => a + b;

Bạn muốn tìm hiểu thêm về điểm nào không?
```

### **5.6. Tích hợp Moodle**
- ✅ Đồng bộ Users từ Moodle
- ✅ Đồng bộ Courses từ Moodle
- ✅ Đồng bộ Course Contents (Topics)
- ✅ Đồng bộ Enrollments
- ✅ Tự động sync định kỳ (Cron Job)
- ✅ Manual sync qua API

**Chi tiết xem:** `MOODLE_INTEGRATION.md`

### **5.7. Dashboard & Analytics**
- 📊 Tiến độ học tập
- 📈 Điểm số các quiz
- ⏱️ Thời gian học
- 🎯 Topics đã hoàn thành
- 📝 Lịch sử chat với AI

---

## 6. User Flow (Luồng Người dùng)

### **6.1. Sinh viên (Student)**

#### **Đăng nhập và Học tập:**
```
1. Đăng nhập hệ thống
   ↓
2. Xem danh sách khóa học đã đăng ký
   ↓
3. Chọn khóa học
   ↓
4. Xem danh sách Topics (micro learning)
   ↓
5. Học Topic 1 (10 phút)
   ↓
6. Làm Quiz sau Topic 1
   ↓
7. Xem kết quả và giải thích
   ↓
8. Tiếp tục Topic 2
```

#### **Chat với AI Tutor:**
```
1. Trong khóa học, click "Chat với AI Tutor"
   ↓
2. Chọn chat về topic cụ thể hoặc toàn bộ khóa học
   ↓
3. Gửi câu hỏi
   ↓
4. AI trả lời real-time
   ↓
5. Tiếp tục hỏi nếu chưa hiểu
```

### **6.2. Giáo viên (Teacher)**

#### **Tạo khóa học:**
```
1. Đăng nhập với role Teacher
   ↓
2. Click "Tạo khóa học mới"
   ↓
3. Nhập thông tin: tên, mô tả, thời gian
   ↓
4. Tạo Topics (micro learning):
   - Topic 1: "Biến và kiểu dữ liệu" (10 phút)
   - Topic 2: "Functions" (12 phút)
   - ...
   ↓
5. Nhập nội dung cho mỗi topic
```

#### **Sinh câu hỏi bằng AI:**
```
1. Vào Topic đã tạo
   ↓
2. Click "Sinh câu hỏi bằng AI"
   ↓
3. Chọn:
   - Số lượng câu hỏi: 10
   - Độ khó: Medium
   - AI Model: GPT-4
   ↓
4. AI sinh câu hỏi từ nội dung Topic
   ↓
5. Review và chỉnh sửa câu hỏi
   ↓
6. Lưu vào Question Bank
```

#### **Tạo Quiz:**
```
1. Click "Tạo Quiz"
   ↓
2. Chọn Topic hoặc toàn bộ khóa học
   ↓
3. Chọn câu hỏi từ Question Bank
   ↓
4. Cấu hình:
   - Thời gian làm bài
   - Ngày mở/đóng
   - Điểm số
   ↓
5. Publish Quiz
```

### **6.3. Quản trị viên (Admin)**

```
1. Đăng nhập với role Admin
   ↓
2. Quản lý Users
   ↓
3. Đồng bộ Moodle:
   - Sync Users
   - Sync Courses
   - Sync Enrollments
   ↓
4. Cấu hình AI Models
   ↓
5. Xem Analytics toàn hệ thống
```

---

## 7. API Endpoints

### **Authentication**
```
POST   /api/auth/register        # Đăng ký
POST   /api/auth/login           # Đăng nhập
GET    /api/auth/me              # Thông tin user hiện tại
POST   /api/auth/logout          # Đăng xuất
```

### **Courses**
```
GET    /api/courses              # Danh sách khóa học
GET    /api/courses/:id          # Chi tiết khóa học
POST   /api/courses              # Tạo khóa học (teacher)
PUT    /api/courses/:id          # Cập nhật khóa học
DELETE /api/courses/:id          # Xóa khóa học
```

### **Topics**
```
GET    /api/courses/:id/topics   # Danh sách topics
GET    /api/topics/:id           # Chi tiết topic
POST   /api/topics               # Tạo topic (teacher)
PUT    /api/topics/:id           # Cập nhật topic
DELETE /api/topics/:id           # Xóa topic
```

### **AI - Generate Questions**
```
POST   /api/ai/generate-questions
Body: {
  "topic_id": 1,
  "num_questions": 10,
  "difficulty": "medium",
  "model": "gpt-4"
}
Response: [array of questions]
```

### **Questions**
```
GET    /api/questions            # Danh sách câu hỏi
GET    /api/questions/:id        # Chi tiết câu hỏi
POST   /api/questions            # Tạo câu hỏi thủ công
PUT    /api/questions/:id        # Cập nhật câu hỏi
DELETE /api/questions/:id        # Xóa câu hỏi
```

### **Quizzes**
```
GET    /api/quizzes              # Danh sách quiz
GET    /api/quizzes/:id          # Chi tiết quiz
POST   /api/quizzes              # Tạo quiz
POST   /api/quizzes/:id/attempt  # Bắt đầu làm bài
POST   /api/quizzes/:id/submit   # Nộp bài
GET    /api/quizzes/:id/results  # Xem kết quả
```

### **AI Chat**
```
POST   /api/chat/session         # Tạo session mới
POST   /api/chat/message         # Gửi message
GET    /api/chat/sessions/:id    # Lấy lịch sử chat
WebSocket: /chat                 # Real-time chat
```

### **Moodle Sync**
```
POST   /api/sync/users           # Sync users (admin)
POST   /api/sync/courses         # Sync courses (admin)
POST   /api/sync/all             # Sync toàn bộ (admin)
```

---

## 8. Công nghệ AI

### **8.1. AI Models Supported**
- **GPT-4** (OpenAI) - Chất lượng cao nhất
- **GPT-3.5-Turbo** (OpenAI) - Nhanh, rẻ hơn
- **Gemini Pro** (Google) - Alternative

### **8.2. AI Use Cases**

#### **1. Question Generation**
```javascript
// Input
{
  topic_content: "JavaScript Functions là...",
  num_questions: 10,
  difficulty: "medium"
}

// Output
{
  questions: [
    {
      question_text: "Arrow function khác gì với function declaration?",
      answer_options: { A: "...", B: "...", C: "...", D: "..." },
      correct_answer: "B",
      explanation: "..."
    },
    ...
  ]
}
```

#### **2. AI Tutor Chatbot**
```javascript
// Input
{
  message: "Arrow function là gì?",
  context: {
    course_name: "JavaScript Basics",
    topic_content: "...",
    chat_history: [...]
  }
}

// Output
{
  response: "Arrow function là một cách viết ngắn gọn hơn...",
  references: ["Topic 3: Functions"]
}
```

### **8.3. Cost Optimization**
- Cache responses thường gặp
- Use GPT-3.5 for simple tasks
- Use GPT-4 for complex generation
- Limit context length
- Rate limiting

---

## 9. Deployment

### **9.1. Development Environment**

```bash
# Clone repository
git clone https://github.com/your-repo/lms-ai-platform.git

# Setup Backend
cd backend
npm install
cp .env.example .env
# Edit .env with your configs
npm run dev

# Setup Frontend
cd frontend
npm install
npm run dev
```

### **9.2. Environment Variables**

**Backend (.env):**
```env
# Server
PORT=3000
NODE_ENV=development

# Database
DATABASE_URL=postgresql://user:password@localhost:5432/lms_db

# JWT
JWT_SECRET=your-secret-key
JWT_EXPIRES_IN=7d

# AI APIs
OPENAI_API_KEY=sk-...
GEMINI_API_KEY=...

# Moodle
MOODLE_URL=https://your-moodle.com
MOODLE_TOKEN=...
```

**Frontend (.env):**
```env
VITE_API_URL=http://localhost:3000/api
VITE_WS_URL=ws://localhost:3000
```

### **9.3. Production Deployment**

#### **Option 1: VPS (Ubuntu Server)**
```bash
# Install dependencies
sudo apt update
sudo apt install nodejs npm postgresql nginx

# Setup database
sudo -u postgres createdb lms_db

# Deploy backend
cd backend
npm install --production
npm run migrate
npm start

# Deploy frontend
cd frontend
npm install
npm run build
# Serve with nginx
```

#### **Option 2: Docker**
```yaml
# docker-compose.yml
version: '3.8'
services:
  postgres:
    image: postgres:15
    environment:
      POSTGRES_DB: lms_db
      POSTGRES_PASSWORD: password
    volumes:
      - pgdata:/var/lib/postgresql/data

  backend:
    build: ./backend
    ports:
      - "3000:3000"
    environment:
      DATABASE_URL: postgresql://postgres:password@postgres:5432/lms_db
    depends_on:
      - postgres

  frontend:
    build: ./frontend
    ports:
      - "80:80"
```

---

## 10. Security

### **10.1. Authentication & Authorization**
- ✅ JWT tokens với expiry
- ✅ Password hashing (bcrypt)
- ✅ Role-based access control (RBAC)
- ✅ Protected routes/APIs

### **10.2. Data Security**
- ✅ SQL injection prevention (ORM)
- ✅ XSS protection
- ✅ CORS configuration
- ✅ Rate limiting
- ✅ Input validation (zod)
- ✅ Encrypted API keys

### **10.3. AI Security**
- ✅ Prompt injection prevention
- ✅ Content filtering
- ✅ Rate limiting AI calls
- ✅ Cost monitoring

---

## 11. Testing

### **Unit Tests**
```bash
# Backend
cd backend
npm test

# Frontend
cd frontend
npm test
```

### **Test Coverage**
- Controllers
- Services (AI, Moodle, Sync)
- API endpoints
- React components

---

## 12. Roadmap

### **Phase 1 - MVP (3 months)** ✅
- [x] Basic CRUD for Users, Courses, Topics
- [x] Authentication & Authorization
- [x] AI Question Generation
- [x] Basic Quiz functionality
- [x] AI Tutor Chatbot
- [x] Moodle Integration

### **Phase 2 - Enhancement (2 months)**
- [ ] Video integration
- [ ] Rich text editor for topics
- [ ] Advanced analytics
- [ ] Mobile responsive
- [ ] Email notifications

### **Phase 3 - Advanced AI (2 months)**
- [ ] Personalized learning paths
- [ ] AI-powered recommendations
- [ ] Auto-grading essays
- [ ] Voice AI tutor
- [ ] Adaptive difficulty

### **Phase 4 - Scale (ongoing)**
- [ ] Multi-language support
- [ ] Mobile apps (React Native)
- [ ] Offline mode
- [ ] Advanced reporting
- [ ] Gamification

---

## 13. Team & Roles

### **Development Team**
- **Backend Developer**: Node.js, Express, PostgreSQL, AI Integration
- **Frontend Developer**: React.js, UI/UX
- **AI Engineer**: LangChain, Prompt Engineering
- **DevOps**: Deployment, Monitoring

### **Skills Required**
- Node.js & Express
- React.js & Modern Frontend
- PostgreSQL & Sequelize
- REST API design
- AI/ML basics
- Moodle familiarity

---

## 14. Documentation

📚 **Tài liệu kỹ thuật:**
- `LMS_AI_ERD.md` - Database schema
- `MOODLE_INTEGRATION.md` - Tích hợp Moodle
- `API_DOCUMENTATION.md` - API reference
- `DEPLOYMENT.md` - Hướng dẫn deploy

---

## 15. Support & Contact

### **Issues & Bugs**
- GitHub Issues: [link]
- Email: support@lms-ai.com

### **Community**
- Discord: [link]
- Forum: [link]

---

## Tổng kết

### **Điểm mạnh của hệ thống:**
1. ✅ **Micro Learning** - Bài học ngắn, dễ tiêu hóa
2. ✅ **AI-Powered** - Tự động sinh câu hỏi, chatbot thông minh
3. ✅ **Moodle Integration** - Tận dụng hệ thống có sẵn
4. ✅ **Modern Tech Stack** - React + Node.js + PostgreSQL
5. ✅ **Scalable** - Kiến trúc có thể mở rộng
6. ✅ **Real-time** - Chat với AI real-time
7. ✅ **Cost-effective** - Sử dụng AI hiệu quả

### **Use Cases:**
- 🎓 Trường học, đại học
- 💼 Đào tạo doanh nghiệp
- 📚 Học online cá nhân
- 🏢 Training centers

Hệ thống sẵn sàng cho việc nghiên cứu và phát triển thành sản phẩm thực tế! 🚀

