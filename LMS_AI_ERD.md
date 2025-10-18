# ERD - Hệ thống Quản lý Học tập (LMS) hỗ trợ AI

```mermaid
erDiagram
    %% Core Entities
    USER {
        int user_id PK
        string email UK
        string password_hash
        string full_name
        enum role "student, teacher, admin"
        datetime created_at
        string moodle_user_id "tích hợp Moodle"
    }

    COURSE {
        int course_id PK
        string course_code UK
        string course_name
        text description
        int teacher_id FK
        datetime start_date
        datetime end_date
        string moodle_course_id "tích hợp Moodle"
        datetime created_at
    }

    ENROLLMENT {
        int enrollment_id PK
        int student_id FK
        int course_id FK
        datetime enrollment_date
    }

    LESSON {
        int lesson_id PK
        int course_id FK
        string lesson_name
        text content
        int order_number
        datetime created_at
    }

    %% AI Components
    AI_MODEL {
        int model_id PK
        string model_name "GPT-4, Gemini, Grok"
        string provider "OpenAI, Google, xAI"
        string api_key
        boolean is_active
    }

    LANGCHAIN_PROMPT {
        int prompt_id PK
        string prompt_name
        enum prompt_type "question_generation, tutoring"
        text prompt_template
        datetime created_at
    }

    %% Question & Quiz
    QUESTION_BANK {
        int question_id PK
        int course_id FK
        int created_by FK
        enum question_type "multiple_choice, true_false, essay"
        text question_text
        json answer_options
        text correct_answer
        enum difficulty "easy, medium, hard"
        boolean is_ai_generated
        datetime created_at
    }

    QUIZ {
        int quiz_id PK
        int course_id FK
        int lesson_id FK
        string quiz_name
        int duration_minutes
        datetime start_time
        datetime end_time
        datetime created_at
    }

    QUIZ_QUESTION {
        int quiz_question_id PK
        int quiz_id FK
        int question_id FK
        int order_number
        int points
    }

    STUDENT_ATTEMPT {
        int attempt_id PK
        int quiz_id FK
        int student_id FK
        datetime start_time
        datetime submit_time
        float score
        json answers "lưu tất cả câu trả lời"
    }

    %% AI Chat
    CHAT_SESSION {
        int session_id PK
        int student_id FK
        int course_id FK
        int model_id FK
        datetime created_at
    }

    CHAT_MESSAGE {
        int message_id PK
        int session_id FK
        enum role "user, assistant"
        text content
        datetime created_at
    }

    %% Relationships - Core
    USER ||--o{ COURSE : "teaches"
    USER ||--o{ ENROLLMENT : "enrolls"
    COURSE ||--o{ ENROLLMENT : "has"
    COURSE ||--o{ LESSON : "contains"
    
    %% Relationships - Quiz
    COURSE ||--o{ QUESTION_BANK : "has"
    COURSE ||--o{ QUIZ : "has"
    LESSON ||--o{ QUIZ : "includes"
    USER ||--o{ QUESTION_BANK : "creates"
    
    QUIZ ||--o{ QUIZ_QUESTION : "contains"
    QUESTION_BANK ||--o{ QUIZ_QUESTION : "used_in"
    QUIZ ||--o{ STUDENT_ATTEMPT : "has"
    USER ||--o{ STUDENT_ATTEMPT : "attempts"
    
    %% Relationships - AI Chat
    USER ||--o{ CHAT_SESSION : "initiates"
    COURSE ||--o{ CHAT_SESSION : "relates_to"
    AI_MODEL ||--o{ CHAT_SESSION : "powers"
    CHAT_SESSION ||--o{ CHAT_MESSAGE : "contains"
```

---

## Tổng kết

### **Phân loại bảng:**
- **Core (4 bảng):** USER, COURSE, ENROLLMENT, LESSON
- **AI (2 bảng):** AI_MODEL, LANGCHAIN_PROMPT
- **Quiz (4 bảng):** QUESTION_BANK, QUIZ, QUIZ_QUESTION, STUDENT_ATTEMPT
- **Chat (2 bảng):** CHAT_SESSION, CHAT_MESSAGE

### **Tính năng chính:**
1. ✅ Quản lý người dùng và khóa học
2. ✅ Tích hợp Moodle
3. ✅ AI sinh câu hỏi tự động
4. ✅ Quiz và đánh giá
5. ✅ AI Tutor chatbot
6. ✅ Hỗ trợ nhiều AI models
