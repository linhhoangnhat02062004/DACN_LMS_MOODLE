    # TỔNG QUAN HỆ THỐNG LMS HỖ TRỢ AI

    ## 1. Giới thiệu

### **Tên dự án**
**AI Chatbot Plugin for Moodle** - Plugin AI Chatbot tích hợp vào Moodle

### **Mục tiêu Giai đoạn Hiện tại**
Phát triển một **Plugin/Module bổ sung** cho hệ thống Moodle opensource có sẵn, tập trung vào **AI Chatbot (AI Tutor)** để:
- 🎓 **Sử dụng Moodle opensource** làm nền tảng LMS chính
- 💬 **Phát triển thêm AI Chatbot** tích hợp vào Moodle
- 📚 AI trả lời câu hỏi dựa trên nội dung khóa học trong Moodle (RAG - Retrieval Augmented Generation)
- 🔌 **Kết nối với Moodle** qua Web Services API
- 🏗️ Sử dụng **MCP Server** để chuẩn hóa tích hợp AI
- ⚡ Không phát triển lại LMS từ đầu

### **Kiến trúc hệ thống:**
```
┌────────────────────────────────────┐
│   MOODLE (Opensource - Có sẵn)    │
│   - Quản lý khóa học               │
│   - Quản lý users                  │
│   - Quản lý nội dung               │
│   - Quiz, Assignment...            │
└──────────────┬─────────────────────┘
               │ Web Services API
               │ (Lấy data)
               ▼
┌────────────────────────────────────┐
│   AI CHATBOT SERVICE (Phát triển)  │
│   - Backend API (Node.js)          │
│   - MCP Server + LangChain         │
│   - Vector DB (RAG)                │
│   - Chat Interface                 │
└────────────────────────────────────┘
```

### **Người dùng mục tiêu**
- 👨‍🎓 **Sinh viên**: Học trên Moodle, chat với AI tutor để giải đáp thắc mắc
- 👨‍🏫 **Giáo viên**: Quản lý khóa học trên Moodle như bình thường
- 👨‍💼 **Quản trị viên**: Cài đặt plugin AI Chatbot vào Moodle

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
    - **Markdown**: React Markdown (hiển thị câu trả lời AI)
    - **Real-time**: Socket.io-client (chat)

    #### **Backend**
    - **Runtime**: Node.js 18+
    - **Framework**: Express.js
    - **Database**: PostgreSQL 15+
    - **ORM**: Sequelize
    - **Authentication**: JWT (JSON Web Token)
    - **Real-time**: Socket.io (WebSocket cho chat)

    #### **AI Integration với MCP Server**
    - **MCP (Model Context Protocol)**: Chuẩn hóa tích hợp AI
    - **LangChain**: Framework orchestration AI workflows
    - **OpenAI API**: GPT-4, GPT-3.5 (chatbot)
    - **Google Gemini API**: Gemini Pro (alternative)
    - **Vector Database**: Pinecone / Chroma (lưu embeddings cho RAG)

    #### **External Integration**
    - **Moodle Web Services**: Đồng bộ courses, users, content

    ---

    ## 3. MCP Server Architecture

    ### **3.1. MCP Server là gì?**

    **MCP (Model Context Protocol)** là một giao thức chuẩn hóa cách ứng dụng tích hợp với AI models và external services.

    ### **3.2. Vai trò của MCP Server trong LMS**

    ```
    ┌─────────────────────────────────────────────────┐
    │            Frontend (React.js)                  │
    │         User Interface - Chat UI                │
    └───────────────────┬─────────────────────────────┘
                        │ HTTP/WebSocket
                        ▼
    ┌─────────────────────────────────────────────────┐
    │          Backend API (Node.js/Express)          │
    │         Business Logic Layer                    │
    └───────┬──────────────────┬──────────────────────┘
            │                  │
            │                  │
    ┌───────▼────────┐  ┌──────▼────────────────────┐
    │   PostgreSQL   │  │   MCP Server Layer        │
    │   Database     │  │   (Middleware)            │
    └────────────────┘  └──────┬────────────────────┘
                            │
            ┌──────────────────┼──────────────────┐
            │                  │                  │
    ┌───────▼───────┐  ┌───────▼───────┐  ┌──────▼────────┐
    │  AI Models    │  │  Vector DB    │  │  Moodle API   │
    │  MCP Server   │  │  MCP Server   │  │  MCP Server   │
    │  - GPT-4      │  │  - Pinecone   │  │  - Courses    │
    │  - Gemini     │  │  - Embeddings │  │  - Users      │
    └───────────────┘  └───────────────┘  └───────────────┘
    ```

    ### **3.3. MCP Servers trong hệ thống**

    #### **1. AI Model MCP Server**
    **Mục đích:** Chuẩn hóa gọi các AI models (GPT-4, Gemini)

    **Tools cung cấp:**
    - `generate_completion()` - Tạo câu trả lời chat
    - `generate_embedding()` - Tạo vector embeddings
    - `count_tokens()` - Đếm tokens

    **Config:**
    ```javascript
    // config/mcp/aiModelServer.js
    {
    serverType: 'ai_model',
    models: [
        {
        name: 'gpt-4',
        provider: 'openai',
        endpoint: 'https://api.openai.com/v1/chat/completions'
        },
        {
        name: 'gemini-pro',
        provider: 'google',
        endpoint: 'https://generativelanguage.googleapis.com/v1/models/gemini-pro'
        }
    ]
    }
    ```

    #### **2. Vector Database MCP Server**
    **Mục đích:** Quản lý embeddings cho RAG (tìm kiếm nội dung liên quan)

    **Tools cung cấp:**
    - `upsert_vectors()` - Lưu embeddings
    - `query_similar()` - Tìm kiếm tương tự
    - `delete_vectors()` - Xóa embeddings

    **Sử dụng:**
    ```javascript
    // Khi tạo/cập nhật TOPIC
    1. Lấy nội dung TOPIC.content
    2. Chia nhỏ thành chunks (mỗi chunk ~500 words)
    3. Generate embeddings cho mỗi chunk
    4. Lưu vào Vector DB qua MCP Server
    ```

    #### **3. Moodle MCP Server**
    **Mục đích:** Đồng bộ dữ liệu từ Moodle

    **Tools cung cấp:**
    - `get_courses()` - Lấy danh sách courses
    - `get_course_contents()` - Lấy nội dung course
    - `get_users()` - Lấy users
    - `get_enrolled_users()` - Lấy enrollments

    ---

    ## 4. Database Schema (10 bảng)

    ### **Core Tables (4)**
    1. **USER** - Người dùng (student/teacher/admin)
    2. **COURSE** - Khóa học
    3. **ENROLLMENT** - Đăng ký học (liên kết user-course)
    4. **TOPIC** - Chủ đề micro learning (5-15 phút)

    ### **Quiz Tables (4)** - Phase 2
    5. **QUESTION_BANK** - Ngân hàng câu hỏi
    6. **QUIZ** - Bài kiểm tra
    7. **QUIZ_QUESTION** - Liên kết quiz-question
    8. **STUDENT_ATTEMPT** - Bài làm của sinh viên

    ### **Chat Tables (2)** - ⭐ Phase 1 (Hiện tại)
    9. **CHAT_SESSION** - Phiên chat với AI
    10. **CHAT_MESSAGE** - Tin nhắn chat

    **Chi tiết xem:** `LMS_AI_ERD.md`

    ---

    ## 5. Tính năng Phase 1 - AI Chatbot (Hiện tại)

### **5.1. Moodle (Có sẵn)** 🎓
**Sử dụng Moodle opensource** để:
- ✅ Quản lý người dùng (students, teachers, admins)
- ✅ Tạo và quản lý khóa học
- ✅ Tạo nội dung (topics, sections)
- ✅ Quiz, Assignment, Forum
- ✅ Enrollment, Grading

**Không cần phát triển lại** - Moodle đã có đầy đủ tính năng LMS

### **5.2. Đồng bộ dữ liệu từ Moodle** 🔄
AI Chatbot Service lấy dữ liệu từ Moodle qua **Web Services API**:
- ✅ Lấy danh sách courses
- ✅ Lấy nội dung course (sections, modules)
- ✅ Lấy users và enrollments
- ✅ Tự động sync định kỳ hoặc real-time webhook

**Ví dụ dữ liệu từ Moodle:**
```
Course: "Lập trình JavaScript" (id: 123)
├─ Section 1: Giới thiệu
│  └─ Module: "Biến và kiểu dữ liệu" (text, video, files)
├─ Section 2: Functions
│  └─ Module: "Arrow Functions" (text, video)
├─ Section 3: Async Programming
│  └─ Module: "Promises & Async/Await" (text, code)
```

→ AI Chatbot sẽ embedding nội dung này vào Vector DB để trả lời câu hỏi

    ### **5.3. AI Tutor Chatbot** ⭐ (Trọng tâm Phase 1)

    #### **Tính năng:**
    - 💬 Chat real-time với AI
    - 🎯 AI trả lời dựa trên nội dung khóa học (RAG)
    - 📚 Context-aware (hiểu ngữ cảnh)
    - 🔄 Lưu lịch sử hội thoại
    - 🎓 Hỗ trợ học tập 24/7
    - ⚡ Stream responses (real-time typing effect)

    #### **Quy trình hoạt động với MCP Server:**

    ```
    ┌─────────────────────────────────────────────────┐
    │  Step 1: User gửi câu hỏi                       │
    │  "Arrow function khác gì function thông thường?"│
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 2: Backend nhận request                   │
    │  - Tạo/lấy CHAT_SESSION                         │
    │  - Lưu message (role='user')                    │
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 3: Generate embedding cho câu hỏi        │
    │  MCP Server: AI Model → generate_embedding()    │
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 4: Tìm nội dung liên quan (RAG)           │
    │  MCP Server: Vector DB → query_similar()        │
    │  → Trả về top 3-5 chunks liên quan nhất         │
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 5: Build context với LangChain           │
    │  - Chat history (10 messages gần nhất)          │
    │  - Retrieved content từ Vector DB               │
    │  - System prompt                                │
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 6: Gọi AI Model qua MCP Server            │
    │  MCP Server: AI Model → generate_completion()   │
    │  → Stream response về client                    │
    └───────────────────┬─────────────────────────────┘
                        │
                        ▼
    ┌─────────────────────────────────────────────────┐
    │  Step 7: Lưu AI response                        │
    │  - Lưu CHAT_MESSAGE (role='assistant')          │
    │  - Update session                               │
    └─────────────────────────────────────────────────┘
    ```

    #### **Code Example - Chat với MCP Server:**

    ```javascript
    // services/aiChatService.js
    const { MCPClient } = require('@modelcontextprotocol/sdk');
    const langchain = require('langchain');

    class AIChatService {
    constructor() {
        // Khởi tạo MCP Clients
        this.aiModelMCP = new MCPClient({
        serverUrl: process.env.AI_MODEL_MCP_SERVER
        });
        
        this.vectorDBMCP = new MCPClient({
        serverUrl: process.env.VECTOR_DB_MCP_SERVER
        });
    }

    async chat(sessionId, userMessage) {
        // 1. Lấy session
        const session = await ChatSession.findByPk(sessionId);
        
        // 2. Lưu user message
        await ChatMessage.create({
        session_id: sessionId,
        role: 'user',
        content: userMessage
        });

        // 3. Generate embedding cho câu hỏi
        const questionEmbedding = await this.aiModelMCP.callTool(
        'generate_embedding',
        { text: userMessage }
        );

        // 4. Tìm nội dung liên quan qua Vector DB MCP
        const relevantChunks = await this.vectorDBMCP.callTool(
        'query_similar',
        {
            embedding: questionEmbedding,
            filter: { course_id: session.course_id },
            top_k: 5
        }
        );

        // 5. Lấy chat history
        const chatHistory = await ChatMessage.findAll({
        where: { session_id: sessionId },
        order: [['created_at', 'DESC']],
        limit: 10
        });

        // 6. Build context với LangChain
        const context = this.buildContext(relevantChunks, chatHistory);

        // 7. Gọi AI Model qua MCP Server
        const aiResponse = await this.aiModelMCP.callTool(
        'generate_completion',
        {
            model: 'gpt-4',
            messages: [
            { role: 'system', content: this.getSystemPrompt() },
            ...context,
            { role: 'user', content: userMessage }
            ],
            stream: true // Stream response
        }
        );

        // 8. Lưu AI response
        await ChatMessage.create({
        session_id: sessionId,
        role: 'assistant',
        content: aiResponse.content
        });

        return aiResponse;
    }

    buildContext(relevantChunks, chatHistory) {
        // Kết hợp retrieved content và chat history
        let context = "Dựa trên nội dung sau:\n\n";
        
        relevantChunks.forEach(chunk => {
        context += `${chunk.content}\n\n`;
        });
        
        context += "\nLịch sử hội thoại:\n";
        chatHistory.reverse().forEach(msg => {
        context += `${msg.role}: ${msg.content}\n`;
        });
        
        return context;
    }

    getSystemPrompt() {
        return `Bạn là một AI Tutor thông minh, nhiệm vụ của bạn là:
    - Trả lời câu hỏi dựa trên nội dung khóa học được cung cấp
    - Giải thích rõ ràng, dễ hiểu
    - Đưa ra ví dụ minh họa khi cần
    - Khuyến khích sinh viên suy nghĩ
    - Luôn lịch sự và hữu ích`;
    }
    }

    module.exports = new AIChatService();
    ```

    #### **Ví dụ hội thoại:**

    **User:** "Arrow function khác gì function thông thường?"

    **AI Assistant (streaming...):** 
    ```
    Arrow function khác function thông thường ở 3 điểm chính:

    1. **Cú pháp ngắn gọn hơn:**
    // Function thông thường
    function add(a, b) {
        return a + b;
    }
    
    // Arrow function
    const add = (a, b) => a + b;

    2. **Không có binding this riêng:**
    Arrow function không có `this` của riêng nó, 
    nó sẽ kế thừa `this` từ scope cha.

    3. **Không có arguments object:**
    Arrow function không có đối tượng `arguments`.

    Bạn muốn tìm hiểu thêm về điểm nào không?
    ```

    **User:** "Cho ví dụ về binding this?"

    **AI Assistant:** "Dựa trên nội dung bài học về Functions..."

### **5.4. Cách triển khai Plugin vào Moodle** 🔌

#### **Option 1: Moodle Block Plugin (Khuyến nghị)**
- Tạo Moodle Block Plugin "AI Chatbot"
- Block hiển thị chat widget ở sidebar
- Click vào block → Mở chat interface
- Gọi API của AI Chatbot Service

**Cấu trúc:**
```
moodle/blocks/ai_chatbot/
├── version.php           # Plugin version
├── block_ai_chatbot.php  # Block class
├── lang/
│   └── en/
│       └── block_ai_chatbot.php
├── styles.css
└── amd/src/
    └── chatbot.js        # JavaScript gọi API
```

#### **Option 2: Standalone Web App + Iframe**
- Deploy AI Chatbot Service riêng
- Nhúng vào Moodle qua iframe
- Single Sign-On (SSO) với Moodle

#### **Option 3: Browser Extension**
- Tạo Chrome/Firefox extension
- Extension inject chat button vào Moodle
- Hoạt động trên bất kỳ trang Moodle nào

**Chi tiết xem:** `MOODLE_INTEGRATION.md`

    ---

    ## 6. User Flow Phase 1

    ### **6.1. Sinh viên (Student)**

#### **Học trên Moodle và Chat với AI:**
```
1. Đăng nhập **Moodle** (như bình thường)
↓
2. Vào khóa học "Lập trình JavaScript"
↓
3. Đọc Section 1: "Biến và kiểu dữ liệu"
   - Đọc tài liệu
   - Xem video
↓
4. Có thắc mắc → Click **"AI Chatbot"** block (sidebar)
↓
5. Gửi câu hỏi: "const khác let như thế nào?"
↓
6. AI trả lời real-time dựa trên:
   - Nội dung section đang học
   - Tài liệu trong course
   - Video transcripts
↓
7. Tiếp tục hỏi nếu chưa hiểu
↓
8. Quay lại học Section 2 trên Moodle
```

    ### **6.2. Giáo viên (Teacher)**

#### **Tạo nội dung trên Moodle:**
```
1. Đăng nhập **Moodle** với role Teacher
↓
2. Tạo khóa học mới trên Moodle (như bình thường)
   - Tên: "Lập trình JavaScript"
   - Mô tả, category, enrollment...
↓
3. Tạo Sections và thêm nội dung:
   - Section 1: "Biến và kiểu dữ liệu"
     + Add Page: Nội dung text
     + Add File: Slides PDF
     + Add Video: Link YouTube
   - Section 2: "Functions"
     + Add Page: Giải thích functions
     + Add Quiz: Bài tập
↓
4. **AI Chatbot Service tự động:**
   - Sync nội dung từ Moodle qua API
   - Embedding vào Vector DB
   - Sẵn sàng trả lời câu hỏi sinh viên
↓
5. Giáo viên không cần làm gì thêm!
```

    ### **6.3. Quản trị viên (Admin)**

```
1. Cài đặt **Moodle** opensource
↓
2. Cài đặt **AI Chatbot Plugin**:
   Option A: Moodle Block Plugin
   Option B: Standalone service + embed
↓
3. Cấu hình AI Chatbot Service:
   - Moodle URL và token
   - OpenAI/Gemini API keys
   - Vector DB credentials
↓
4. Enable plugin trong Moodle
↓
5. AI Chatbot tự động sync dữ liệu từ Moodle
↓
6. Monitor usage và costs
```

    ---

    ## 7. API Endpoints Phase 1

    ### **Authentication**
    ```
    POST   /api/auth/register        # Đăng ký
    POST   /api/auth/login           # Đăng nhập
    GET    /api/auth/me              # Thông tin user
    POST   /api/auth/logout          # Đăng xuất
    ```

    ### **Courses & Topics**
    ```
    GET    /api/courses              # Danh sách khóa học
    GET    /api/courses/:id          # Chi tiết khóa học
    POST   /api/courses              # Tạo khóa học (teacher)
    GET    /api/courses/:id/topics   # Danh sách topics
    GET    /api/topics/:id           # Chi tiết topic
    POST   /api/topics               # Tạo topic (teacher)
    ```

    ### **AI Chat** ⭐
    ```
    POST   /api/chat/session         # Tạo session mới
    Body: {
    "course_id": 1,
    "topic_id": 3  // optional
    }

    POST   /api/chat/message         # Gửi message
    Body: {
    "session_id": 1,
    "message": "Arrow function là gì?"
    }

    GET    /api/chat/sessions/:id    # Lấy lịch sử chat

    WebSocket: ws://localhost:3000/chat  # Real-time streaming
    ```

    ### **Moodle Sync**
    ```
    POST   /api/sync/users           # Sync users (admin)
    POST   /api/sync/courses         # Sync courses (admin)
    POST   /api/sync/all             # Sync toàn bộ (admin)
    ```

    ---

    ## 8. MCP Server Configuration

    ### **8.1. AI Model MCP Server**

    **File: config/mcp/aiModelServer.config.js**
    ```javascript
    module.exports = {
    serverName: 'ai-model-server',
    serverType: 'ai_model',
    endpoint: process.env.AI_MODEL_MCP_ENDPOINT,
    
    tools: [
        {
        name: 'generate_completion',
        description: 'Generate chat completion',
        inputSchema: {
            model: 'string',
            messages: 'array',
            temperature: 'number',
            stream: 'boolean'
        }
        },
        {
        name: 'generate_embedding',
        description: 'Generate text embedding',
        inputSchema: {
            text: 'string',
            model: 'string'  // 'text-embedding-ada-002'
        }
        }
    ],
    
    models: [
        {
        id: 'gpt-4',
        provider: 'openai',
        apiKey: process.env.OPENAI_API_KEY
        },
        {
        id: 'gemini-pro',
        provider: 'google',
        apiKey: process.env.GEMINI_API_KEY
        }
    ]
    };
    ```

    ### **8.2. Vector DB MCP Server**

    **File: config/mcp/vectorDBServer.config.js**
    ```javascript
    module.exports = {
    serverName: 'vector-db-server',
    serverType: 'vector_database',
    endpoint: process.env.VECTOR_DB_MCP_ENDPOINT,
    
    tools: [
        {
        name: 'upsert_vectors',
        description: 'Insert/update vectors',
        inputSchema: {
            vectors: 'array',
            namespace: 'string'
        }
        },
        {
        name: 'query_similar',
        description: 'Query similar vectors',
        inputSchema: {
            embedding: 'array',
            top_k: 'number',
            filter: 'object'
        }
        }
    ],
    
    provider: 'pinecone',  // or 'chroma'
    config: {
        apiKey: process.env.PINECONE_API_KEY,
        environment: process.env.PINECONE_ENVIRONMENT,
        indexName: 'lms-topics'
    }
    };
    ```

    ### **8.3. Moodle MCP Server**

    **File: config/mcp/moodleServer.config.js**
    ```javascript
    module.exports = {
    serverName: 'moodle-server',
    serverType: 'moodle',
    endpoint: process.env.MOODLE_MCP_ENDPOINT,
    
    tools: [
        {
        name: 'get_courses',
        description: 'Get all courses from Moodle'
        },
        {
        name: 'get_course_contents',
        description: 'Get course content by ID',
        inputSchema: {
            courseId: 'number'
        }
        },
        {
        name: 'get_users',
        description: 'Get users from Moodle'
        }
    ],
    
    config: {
        url: process.env.MOODLE_URL,
        token: process.env.MOODLE_TOKEN
    }
    };
    ```

    ---

    ## 9. Deployment

    ### **9.1. Environment Variables**

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

    # MCP Servers
    AI_MODEL_MCP_ENDPOINT=http://localhost:8001
    VECTOR_DB_MCP_ENDPOINT=http://localhost:8002
    MOODLE_MCP_ENDPOINT=http://localhost:8003

    # AI APIs (used by MCP Servers)
    OPENAI_API_KEY=sk-...
    GEMINI_API_KEY=...

    # Vector Database
    PINECONE_API_KEY=...
    PINECONE_ENVIRONMENT=us-east-1
    PINECONE_INDEX=lms-topics

    # Moodle
    MOODLE_URL=https://your-moodle.com
    MOODLE_TOKEN=...
    ```

    ### **9.2. Dependencies**

    ```json
    {
    "dependencies": {
        "express": "^4.18.2",
        "sequelize": "^6.35.0",
        "pg": "^8.11.3",
        "socket.io": "^4.6.1",
        "jsonwebtoken": "^9.0.2",
        "bcryptjs": "^2.4.3",
        
        "@modelcontextprotocol/sdk": "^0.1.0",
        "langchain": "^0.1.30",
        "@langchain/openai": "^0.0.28",
        "@langchain/google-genai": "^0.0.11",
        "@pinecone-database/pinecone": "^2.0.1",
        
        "axios": "^1.6.7",
        "dotenv": "^16.3.1",
        "cors": "^2.8.5"
    }
    }
    ```

    ---

    ## 10. Roadmap

    ### **✅ Phase 1 - AI Chatbot (Hiện tại - 2 tháng)**
    - [x] Setup project structure
    - [x] Database schema (10 tables)
    - [x] Authentication & Authorization
    - [x] CRUD Courses & Topics
    - [x] Moodle Integration
    - [ ] **MCP Server Setup** (đang làm)
    - [ ] AI Model MCP Server
    - [ ] Vector DB MCP Server
    - [ ] Moodle MCP Server
    - [ ] **AI Chatbot với RAG** (trọng tâm)
    - [ ] Embedding topics vào Vector DB
    - [ ] Real-time chat với WebSocket
    - [ ] Context-aware responses
    - [ ] Stream AI responses
    - [ ] Frontend Chat UI
    - [ ] Testing & Optimization

    ### **📋 Phase 2 - Quiz & Questions (2-3 tháng)**
    - [ ] AI sinh câu hỏi tự động
    - [ ] Quiz management
    - [ ] Auto-grading
    - [ ] Results & Analytics

    ### **🚀 Phase 3 - Advanced Features (2-3 tháng)**
    - [ ] Video integration
    - [ ] Rich text editor
    - [ ] Advanced analytics
    - [ ] Mobile app
    - [ ] Gamification

    ---

    ## 11. Tài liệu

    📚 **Tài liệu kỹ thuật:**
    - `LMS_AI_ERD.md` - Database schema chi tiết
    - `MOODLE_INTEGRATION.md` - Hướng dẫn tích hợp Moodle
    - `MCP_SERVER_GUIDE.md` - Hướng dẫn setup MCP Servers

    ---

    ## Tổng kết

### **Focus Phase 1:**
🎯 **Plugin AI Chatbot cho Moodle Opensource**

### **Điểm mạnh:**
1. ✅ **Tận dụng Moodle Opensource** - Không phát triển lại LMS từ đầu
2. ✅ **Plugin Architecture** - Dễ cài đặt, dễ triển khai
3. ✅ **MCP Server** - Chuẩn hóa tích hợp AI
4. ✅ **RAG (Retrieval Augmented Generation)** - Chatbot trả lời dựa trên nội dung trong Moodle
5. ✅ **Real-time Chat** - WebSocket streaming
6. ✅ **Auto Sync** - Tự động lấy nội dung từ Moodle
7. ✅ **Scalable** - Hoạt động với bất kỳ Moodle site nào

### **Kiến trúc:**
```
MOODLE (Có sẵn) + AI CHATBOT PLUGIN (Phát triển)
```

### **Tech Stack:**
- **LMS Platform**: Moodle Opensource ✅
- **AI Chatbot Backend**: Node.js + Express + PostgreSQL
- **AI Framework**: MCP Server + LangChain + OpenAI/Gemini
- **Vector DB**: Pinecone/Chroma (RAG)
- **Plugin Interface**: Moodle Block / Iframe / Extension
- **Integration**: Moodle Web Services API

### **Lợi ích:**
- ⚡ Nhanh - Không cần build lại LMS
- 💰 Tiết kiệm - Dùng Moodle free
- 🎯 Tập trung - Chỉ phát triển AI Chatbot
- 🔌 Dễ deploy - Plugin vào Moodle có sẵn
- 🌍 Mở rộng - Dùng cho bất kỳ Moodle nào

Hệ thống tập trung vào việc **phát triển AI Chatbot plugin** tích hợp vào Moodle, không phát triển lại LMS! 🚀💬

