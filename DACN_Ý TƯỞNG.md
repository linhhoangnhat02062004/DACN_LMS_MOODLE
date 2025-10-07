# KẾ HOẠCH TRIỂN KHAI AGENTIC AI TRONG MOODLE SỬ DỤNG LANGCHAIN

## Tổng quan dự án
Triển khai hệ thống AI Agentic trong LMS Moodle sử dụng framework LangChain để tạo ra "gia sư ảo" thông minh, có khả năng:
- Theo dõi tiến độ học tập cá nhân
- Điều chỉnh bài giảng phù hợp với từng học viên
- Tạo tình huống mô phỏng sinh động
- Hỗ trợ học tập 24/7

---

## 1. KIẾN TRÚC TỔNG THỂ

### 1.1 Sơ đồ kiến trúc LangChain + Moodle
```
┌─────────────────────────────────────────────────────────────┐
│                    MOODLE LMS                               │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Study     │  │  Progress   │  │ Motivation  │         │
│  │   Agent     │  │   Agent     │  │   Agent     │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
│           │               │               │                │
│           └───────────────┼───────────────┘                │
│                           │                                │
│  ┌─────────────────────────────────────────────────────────┐│
│  │              STUDENT COORDINATOR                        ││
│  │              (LangChain Agent)                          ││
│  └─────────────────────────────────────────────────────────┘│
│                           │                                │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                RAG SYSTEM                               ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     ││
│  │  │ Document    │  │ Vector      │  │ Embedding   │     ││
│  │  │ Processor   │  │ Database    │  │ Generator   │     ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘     ││
│  └─────────────────────────────────────────────────────────┘│
│                           │                                │
│  ┌─────────────────────────────────────────────────────────┐│
│  │                MOODLE INTEGRATION                       ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     ││
│  │  │ Course      │  │ User        │  │ Grade       │     ││
│  │  │ Content     │  │ Progress    │  │ System      │     ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘     ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│                PYTHON LANGCHAIN LAYER                       │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │ LangChain   │  │ OpenAI/     │  │ Vector      │         │
│  │ Framework   │  │ Claude      │  │ Store       │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Các thành phần chính

#### **1.2.1 LangChain Components**
- **Agents**: Các AI agent chuyên biệt
- **Tools**: Công cụ tương tác với Moodle
- **Memory**: Lưu trữ context và lịch sử
- **Chains**: Kết nối các component
- **Vector Stores**: Lưu trữ embeddings

#### **1.2.2 Moodle Integration**
- **Plugin Architecture**: Tích hợp vào local/aichatbot
- **Database Integration**: Sử dụng Moodle DB
- **API Integration**: Tương tác với Moodle APIs
- **User Management**: Tích hợp với hệ thống user

---

## 2. THIẾT KẾ CHI TIẾT CÁC AI AGENTS

### 2.1 Study Agent (Gia sư học tập)

#### **2.1.1 Chức năng chính:**
```python
# local/aichatbot/python/agents/study_agent.py
from langchain.agents import Tool, AgentExecutor, create_react_agent
from langchain.prompts import PromptTemplate
from langchain.memory import ConversationBufferWindowMemory
from langchain.tools import BaseTool

class StudyAgent:
    def __init__(self, llm, rag_system, moodle_tools):
        self.llm = llm
        self.rag_system = rag_system
        self.moodle_tools = moodle_tools
        self.memory = ConversationBufferWindowMemory(
            k=10,  # Lưu 10 cuộc hội thoại gần nhất
            return_messages=True
        )
        
        # Tạo tools cho Study Agent
        self.tools = [
            Tool(
                name="search_course_content",
                description="Tìm kiếm nội dung khóa học liên quan đến câu hỏi",
                func=self.search_course_content
            ),
            Tool(
                name="get_user_progress",
                description="Lấy tiến độ học tập của học viên",
                func=self.get_user_progress
            ),
            Tool(
                name="generate_practice_questions",
                description="Tạo câu hỏi luyện tập dựa trên chủ đề",
                func=self.generate_practice_questions
            ),
            Tool(
                name="explain_concept",
                description="Giải thích khái niệm với ví dụ cụ thể",
                func=self.explain_concept
            )
        ]
        
        # Tạo prompt template
        self.prompt = PromptTemplate(
            template="""
            Bạn là một gia sư AI thông minh trong hệ thống Moodle. 
            Nhiệm vụ của bạn là giúp học viên học tập hiệu quả.
            
            Thông tin học viên:
            - Tên: {student_name}
            - Khóa học: {course_name}
            - Tiến độ: {progress_percentage}%
            - Điểm mạnh: {strengths}
            - Điểm yếu: {weaknesses}
            
            Lịch sử hội thoại:
            {chat_history}
            
            Câu hỏi hiện tại: {input}
            
            Hãy trả lời một cách:
            1. Thân thiện và khuyến khích
            2. Cung cấp ví dụ cụ thể
            3. Đề xuất tài liệu học tập
            4. Tạo câu hỏi luyện tập nếu cần
            
            {agent_scratchpad}
            """,
            input_variables=["input", "chat_history", "student_name", 
                           "course_name", "progress_percentage", 
                           "strengths", "weaknesses", "agent_scratchpad"]
        )
        
        # Tạo agent
        self.agent = create_react_agent(
            llm=self.llm,
            tools=self.tools,
            prompt=self.prompt
        )
        
        self.agent_executor = AgentExecutor(
            agent=self.agent,
            tools=self.tools,
            memory=self.memory,
            verbose=True,
            max_iterations=5
        )
    
    def process_question(self, question, user_id, course_id):
        """Xử lý câu hỏi của học viên"""
        # Lấy thông tin học viên
        student_info = self.moodle_tools.get_student_info(user_id, course_id)
        
        # Tìm kiếm nội dung liên quan
        relevant_content = self.rag_system.search(question, course_id)
        
        # Xử lý với agent
        response = self.agent_executor.invoke({
            "input": question,
            "student_name": student_info["name"],
            "course_name": student_info["course_name"],
            "progress_percentage": student_info["progress"],
            "strengths": student_info["strengths"],
            "weaknesses": student_info["weaknesses"]
        })
        
        return {
            "answer": response["output"],
            "sources": relevant_content["sources"],
            "confidence": relevant_content["confidence"],
            "suggestions": self.generate_learning_suggestions(student_info)
        }
    
    def search_course_content(self, query):
        """Tool: Tìm kiếm nội dung khóa học"""
        return self.rag_system.search(query)
    
    def get_user_progress(self, user_id):
        """Tool: Lấy tiến độ học viên"""
        return self.moodle_tools.get_user_progress(user_id)
    
    def generate_practice_questions(self, topic):
        """Tool: Tạo câu hỏi luyện tập"""
        return self.rag_system.generate_questions(topic)
    
    def explain_concept(self, concept):
        """Tool: Giải thích khái niệm"""
        return self.rag_system.explain_concept(concept)
```

### 2.2 Progress Agent (Theo dõi tiến độ)

#### **2.2.1 Chức năng chính:**
```python
# local/aichatbot/python/agents/progress_agent.py
from langchain.agents import Tool, AgentExecutor, create_react_agent
from langchain.prompts import PromptTemplate
from langchain.memory import ConversationSummaryBufferMemory

class ProgressAgent:
    def __init__(self, llm, moodle_tools, analytics_engine):
        self.llm = llm
        self.moodle_tools = moodle_tools
        self.analytics_engine = analytics_engine
        self.memory = ConversationSummaryBufferMemory(
            llm=llm,
            max_token_limit=1000,
            return_messages=True
        )
        
        self.tools = [
            Tool(
                name="analyze_learning_patterns",
                description="Phân tích mẫu học tập của học viên",
                func=self.analyze_learning_patterns
            ),
            Tool(
                name="predict_performance",
                description="Dự đoán hiệu suất học tập",
                func=self.predict_performance
            ),
            Tool(
                name="recommend_learning_path",
                description="Đề xuất lộ trình học tập",
                func=self.recommend_learning_path
            ),
            Tool(
                name="identify_learning_gaps",
                description="Xác định khoảng trống kiến thức",
                func=self.identify_learning_gaps
            )
        ]
        
        self.prompt = PromptTemplate(
            template="""
            Bạn là một chuyên gia phân tích tiến độ học tập AI.
            Nhiệm vụ: Phân tích và đưa ra khuyến nghị cải thiện học tập.
            
            Dữ liệu học viên:
            - ID: {user_id}
            - Khóa học: {course_id}
            - Thời gian học: {study_time}
            - Điểm số: {scores}
            - Hoạt động: {activities}
            
            Hãy phân tích và đưa ra:
            1. Đánh giá tiến độ hiện tại
            2. Điểm mạnh và điểm yếu
            3. Khuyến nghị cải thiện
            4. Lộ trình học tập tối ưu
            
            {agent_scratchpad}
            """,
            input_variables=["user_id", "course_id", "study_time", 
                           "scores", "activities", "agent_scratchpad"]
        )
        
        self.agent = create_react_agent(
            llm=self.llm,
            tools=self.tools,
            prompt=self.prompt
        )
        
        self.agent_executor = AgentExecutor(
            agent=self.agent,
            tools=self.tools,
            memory=self.memory,
            verbose=True
        )
    
    def analyze_student_progress(self, user_id, course_id):
        """Phân tích tiến độ học viên"""
        # Lấy dữ liệu từ Moodle
        student_data = self.moodle_tools.get_comprehensive_data(user_id, course_id)
        
        # Phân tích với agent
        analysis = self.agent_executor.invoke({
            "user_id": user_id,
            "course_id": course_id,
            "study_time": student_data["study_time"],
            "scores": student_data["scores"],
            "activities": student_data["activities"]
        })
        
        return {
            "analysis": analysis["output"],
            "recommendations": self.generate_recommendations(student_data),
            "learning_path": self.create_learning_path(student_data),
            "alerts": self.check_learning_alerts(student_data)
        }
    
    def analyze_learning_patterns(self, user_id):
        """Tool: Phân tích mẫu học tập"""
        return self.analytics_engine.analyze_patterns(user_id)
    
    def predict_performance(self, user_id, course_id):
        """Tool: Dự đoán hiệu suất"""
        return self.analytics_engine.predict_performance(user_id, course_id)
    
    def recommend_learning_path(self, user_data):
        """Tool: Đề xuất lộ trình"""
        return self.analytics_engine.recommend_path(user_data)
    
    def identify_learning_gaps(self, user_id, course_id):
        """Tool: Xác định khoảng trống"""
        return self.analytics_engine.identify_gaps(user_id, course_id)
```

### 2.3 Motivation Agent (Động viên học tập)

#### **2.3.1 Chức năng chính:**
```python
# local/aichatbot/python/agents/motivation_agent.py
from langchain.agents import Tool, AgentExecutor, create_react_agent
from langchain.prompts import PromptTemplate
from langchain.memory import ConversationBufferMemory

class MotivationAgent:
    def __init__(self, llm, moodle_tools, gamification_engine):
        self.llm = llm
        self.moodle_tools = moodle_tools
        self.gamification_engine = gamification_engine
        self.memory = ConversationBufferMemory(
            k=15,
            return_messages=True
        )
        
        self.tools = [
            Tool(
                name="check_achievements",
                description="Kiểm tra thành tích và huy hiệu",
                func=self.check_achievements
            ),
            Tool(
                name="create_challenge",
                description="Tạo thử thách học tập",
                func=self.create_challenge
            ),
            Tool(
                name="send_encouragement",
                description="Gửi lời động viên cá nhân hóa",
                func=self.send_encouragement
            ),
            Tool(
                name="track_streak",
                description="Theo dõi chuỗi học tập",
                func=self.track_streak
            )
        ]
        
        self.prompt = PromptTemplate(
            template="""
            Bạn là một chuyên gia tâm lý học tập AI, chuyên động viên và khuyến khích học viên.
            
            Thông tin học viên:
            - Tên: {student_name}
            - Tâm trạng: {mood}
            - Tiến độ: {progress}
            - Thành tích: {achievements}
            - Chuỗi học: {streak} ngày
            
            Mục tiêu: Động viên và khuyến khích học viên tiếp tục học tập.
            
            Hãy:
            1. Nhận diện tâm trạng hiện tại
            2. Đưa ra lời động viên phù hợp
            3. Tạo thử thách thú vị
            4. Đề xuất hoạt động gamification
            
            {agent_scratchpad}
            """,
            input_variables=["student_name", "mood", "progress", 
                           "achievements", "streak", "agent_scratchpad"]
        )
        
        self.agent = create_react_agent(
            llm=self.llm,
            tools=self.tools,
            prompt=self.prompt
        )
        
        self.agent_executor = AgentExecutor(
            agent=self.agent,
            tools=self.tools,
            memory=self.memory,
            verbose=True
        )
    
    def motivate_student(self, user_id, context="general"):
        """Động viên học viên"""
        student_info = self.moodle_tools.get_motivation_data(user_id)
        
        response = self.agent_executor.invoke({
            "student_name": student_info["name"],
            "mood": student_info["mood"],
            "progress": student_info["progress"],
            "achievements": student_info["achievements"],
            "streak": student_info["streak"]
        })
        
        return {
            "motivation_message": response["output"],
            "challenges": self.create_personalized_challenges(student_info),
            "achievements": self.check_new_achievements(student_info),
            "gamification": self.suggest_gamification_activities(student_info)
        }
    
    def check_achievements(self, user_id):
        """Tool: Kiểm tra thành tích"""
        return self.gamification_engine.check_achievements(user_id)
    
    def create_challenge(self, user_id, difficulty="medium"):
        """Tool: Tạo thử thách"""
        return self.gamification_engine.create_challenge(user_id, difficulty)
    
    def send_encouragement(self, user_id, context):
        """Tool: Gửi động viên"""
        return self.gamification_engine.send_encouragement(user_id, context)
    
    def track_streak(self, user_id):
        """Tool: Theo dõi chuỗi"""
        return self.gamification_engine.track_streak(user_id)
```

---

## 3. RAG SYSTEM VỚI LANGCHAIN

### 3.1 Document Processing Pipeline

```python
# local/aichatbot/python/rag/document_processor.py
from langchain.document_loaders import DirectoryLoader, PyPDFLoader
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain.embeddings import OpenAIEmbeddings
from langchain.vectorstores import Chroma
from langchain.chains import RetrievalQA
from langchain.llms import OpenAI

class MoodleRAGSystem:
    def __init__(self, config):
        self.config = config
        self.embeddings = OpenAIEmbeddings(openai_api_key=config["openai_key"])
        self.text_splitter = RecursiveCharacterTextSplitter(
            chunk_size=1000,
            chunk_overlap=200
        )
        self.vectorstore = None
        
    def process_moodle_documents(self, course_id):
        """Xử lý tài liệu Moodle"""
        # Lấy tài liệu từ Moodle
        documents = self.get_moodle_documents(course_id)
        
        # Chia nhỏ tài liệu
        chunks = self.text_splitter.split_documents(documents)
        
        # Tạo embeddings và lưu vào vector store
        self.vectorstore = Chroma.from_documents(
            documents=chunks,
            embedding=self.embeddings,
            persist_directory=f"./vector_db/course_{course_id}"
        )
        
        return len(chunks)
    
    def get_moodle_documents(self, course_id):
        """Lấy tài liệu từ Moodle database"""
        # Kết nối với Moodle database
        moodle_docs = self.query_moodle_files(course_id)
        
        documents = []
        for doc in moodle_docs:
            if doc["mimetype"] == "application/pdf":
                # Xử lý PDF
                loader = PyPDFLoader(doc["filepath"])
                pdf_docs = loader.load()
                for pdf_doc in pdf_docs:
                    pdf_doc.metadata.update({
                        "course_id": course_id,
                        "filename": doc["filename"],
                        "source": "moodle"
                    })
                documents.extend(pdf_docs)
            elif doc["mimetype"] == "text/plain":
                # Xử lý text files
                with open(doc["filepath"], 'r', encoding='utf-8') as f:
                    content = f.read()
                    documents.append({
                        "page_content": content,
                        "metadata": {
                            "course_id": course_id,
                            "filename": doc["filename"],
                            "source": "moodle"
                        }
                    })
        
        return documents
    
    def query_moodle_files(self, course_id):
        """Query files từ Moodle database"""
        # Sử dụng PHP bridge để lấy dữ liệu
        import subprocess
        import json
        
        result = subprocess.run([
            "php", 
            "../moodle_bridge.php", 
            "get_course_files", 
            str(course_id)
        ], capture_output=True, text=True)
        
        return json.loads(result.stdout)
    
    def search_relevant_content(self, query, course_id, top_k=5):
        """Tìm kiếm nội dung liên quan"""
        if not self.vectorstore:
            self.load_vectorstore(course_id)
        
        # Tìm kiếm tương tự
        docs = self.vectorstore.similarity_search(
            query, 
            k=top_k,
            filter={"course_id": course_id}
        )
        
        return {
            "content": [doc.page_content for doc in docs],
            "sources": [doc.metadata for doc in docs],
            "confidence": self.calculate_confidence(docs, query)
        }
    
    def create_qa_chain(self, course_id):
        """Tạo QA chain cho khóa học"""
        if not self.vectorstore:
            self.load_vectorstore(course_id)
        
        retriever = self.vectorstore.as_retriever(
            search_kwargs={"k": 3, "filter": {"course_id": course_id}}
        )
        
        qa_chain = RetrievalQA.from_chain_type(
            llm=OpenAI(temperature=0.7),
            chain_type="stuff",
            retriever=retriever,
            return_source_documents=True
        )
        
        return qa_chain
```

### 3.2 Vector Database Management

```python
# local/aichatbot/python/rag/vector_manager.py
from langchain.vectorstores import Chroma
from langchain.embeddings import OpenAIEmbeddings
import os
import json

class VectorDatabaseManager:
    def __init__(self, config):
        self.config = config
        self.base_path = config["vector_db_path"]
        self.embeddings = OpenAIEmbeddings(openai_api_key=config["openai_key"])
        
    def create_course_vectorstore(self, course_id, documents):
        """Tạo vector store cho khóa học"""
        persist_directory = os.path.join(self.base_path, f"course_{course_id}")
        
        vectorstore = Chroma.from_documents(
            documents=documents,
            embedding=self.embeddings,
            persist_directory=persist_directory
        )
        
        # Lưu metadata
        self.save_course_metadata(course_id, {
            "document_count": len(documents),
            "created_at": datetime.now().isoformat(),
            "last_updated": datetime.now().isoformat()
        })
        
        return vectorstore
    
    def load_course_vectorstore(self, course_id):
        """Load vector store của khóa học"""
        persist_directory = os.path.join(self.base_path, f"course_{course_id}")
        
        if not os.path.exists(persist_directory):
            return None
        
        vectorstore = Chroma(
            persist_directory=persist_directory,
            embedding_function=self.embeddings
        )
        
        return vectorstore
    
    def update_course_vectorstore(self, course_id, new_documents):
        """Cập nhật vector store"""
        vectorstore = self.load_course_vectorstore(course_id)
        
        if vectorstore:
            # Thêm documents mới
            vectorstore.add_documents(new_documents)
        else:
            # Tạo mới
            vectorstore = self.create_course_vectorstore(course_id, new_documents)
        
        # Cập nhật metadata
        self.update_course_metadata(course_id, {
            "last_updated": datetime.now().isoformat()
        })
        
        return vectorstore
    
    def search_across_courses(self, query, user_courses, top_k=10):
        """Tìm kiếm across nhiều khóa học"""
        all_results = []
        
        for course_id in user_courses:
            vectorstore = self.load_course_vectorstore(course_id)
            if vectorstore:
                results = vectorstore.similarity_search_with_score(query, k=top_k)
                for doc, score in results:
                    all_results.append({
                        "content": doc.page_content,
                        "metadata": doc.metadata,
                        "score": score,
                        "course_id": course_id
                    })
        
        # Sắp xếp theo score
        all_results.sort(key=lambda x: x["score"])
        
        return all_results[:top_k]
    
    def save_course_metadata(self, course_id, metadata):
        """Lưu metadata khóa học"""
        metadata_path = os.path.join(self.base_path, f"course_{course_id}_metadata.json")
        with open(metadata_path, 'w', encoding='utf-8') as f:
            json.dump(metadata, f, ensure_ascii=False, indent=2)
    
    def get_course_metadata(self, course_id):
        """Lấy metadata khóa học"""
        metadata_path = os.path.join(self.base_path, f"course_{course_id}_metadata.json")
        if os.path.exists(metadata_path):
            with open(metadata_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return None
```

---

## 4. MCP SERVER INTEGRATION

### 4.1 MCP Server Architecture

MCP (Model Context Protocol) Server sẽ đóng vai trò trung gian giữa AI Agents và các Moodle services, cho phép agents tương tác trực tiếp với hệ thống Moodle.

#### **4.1.1 MCP Server Structure**
```python
# local/aichatbot/python/mcp/mcp_server.py
import asyncio
import json
from typing import Dict, List, Any, Optional
from mcp.server import Server
from mcp.server.models import InitializationOptions
from mcp.server.stdio import stdio_server
from mcp.types import (
    Resource, Tool, TextContent, ImageContent, 
    EmbeddedResource, LoggingLevel
)

class MoodleMCPServer:
    def __init__(self, moodle_tools, rag_system):
        self.moodle_tools = moodle_tools
        self.rag_system = rag_system
        self.server = Server("moodle-ai-server")
        self.setup_handlers()
    
    def setup_handlers(self):
        """Setup MCP handlers"""
        
        @self.server.list_tools()
        async def handle_list_tools() -> List[Tool]:
            """List available tools"""
            return [
                Tool(
                    name="get_course_content",
                    description="Lấy nội dung khóa học từ Moodle",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "course_id": {"type": "integer", "description": "ID khóa học"},
                            "content_type": {"type": "string", "description": "Loại nội dung (files, quizzes, assignments)"}
                        },
                        "required": ["course_id"]
                    }
                ),
                Tool(
                    name="get_user_progress",
                    description="Lấy tiến độ học tập của học viên",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {"type": "integer", "description": "ID học viên"},
                            "course_id": {"type": "integer", "description": "ID khóa học"}
                        },
                        "required": ["user_id"]
                    }
                ),
                Tool(
                    name="search_course_documents",
                    description="Tìm kiếm tài liệu trong khóa học",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "query": {"type": "string", "description": "Từ khóa tìm kiếm"},
                            "course_id": {"type": "integer", "description": "ID khóa học"},
                            "limit": {"type": "integer", "description": "Số lượng kết quả", "default": 5}
                        },
                        "required": ["query"]
                    }
                ),
                Tool(
                    name="create_learning_activity",
                    description="Tạo hoạt động học tập mới",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {"type": "integer", "description": "ID học viên"},
                            "activity_type": {"type": "string", "description": "Loại hoạt động"},
                            "content": {"type": "string", "description": "Nội dung hoạt động"},
                            "course_id": {"type": "integer", "description": "ID khóa học"}
                        },
                        "required": ["user_id", "activity_type", "content"]
                    }
                ),
                Tool(
                    name="send_notification",
                    description="Gửi thông báo cho học viên",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {"type": "integer", "description": "ID học viên"},
                            "message": {"type": "string", "description": "Nội dung thông báo"},
                            "type": {"type": "string", "description": "Loại thông báo", "default": "info"}
                        },
                        "required": ["user_id", "message"]
                    }
                ),
                Tool(
                    name="analyze_quiz_results",
                    description="Phân tích kết quả quiz của học viên",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {"type": "integer", "description": "ID học viên"},
                            "quiz_id": {"type": "integer", "description": "ID quiz"},
                            "course_id": {"type": "integer", "description": "ID khóa học"}
                        },
                        "required": ["user_id", "quiz_id"]
                    }
                ),
                Tool(
                    name="generate_practice_questions",
                    description="Tạo câu hỏi luyện tập dựa trên chủ đề",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "topic": {"type": "string", "description": "Chủ đề"},
                            "difficulty": {"type": "string", "description": "Độ khó", "default": "medium"},
                            "count": {"type": "integer", "description": "Số lượng câu hỏi", "default": 5}
                        },
                        "required": ["topic"]
                    }
                ),
                Tool(
                    name="get_learning_recommendations",
                    description="Đưa ra khuyến nghị học tập cho học viên",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {"type": "integer", "description": "ID học viên"},
                            "course_id": {"type": "integer", "description": "ID khóa học"},
                            "analysis_type": {"type": "string", "description": "Loại phân tích", "default": "comprehensive"}
                        },
                        "required": ["user_id"]
                    }
                )
            ]
        
        @self.server.call_tool()
        async def handle_call_tool(name: str, arguments: Dict[str, Any]) -> List[TextContent]:
            """Handle tool calls"""
            try:
                if name == "get_course_content":
                    result = await self.get_course_content(
                        arguments["course_id"],
                        arguments.get("content_type", "all")
                    )
                elif name == "get_user_progress":
                    result = await self.get_user_progress(
                        arguments["user_id"],
                        arguments.get("course_id")
                    )
                elif name == "search_course_documents":
                    result = await self.search_course_documents(
                        arguments["query"],
                        arguments.get("course_id"),
                        arguments.get("limit", 5)
                    )
                elif name == "create_learning_activity":
                    result = await self.create_learning_activity(
                        arguments["user_id"],
                        arguments["activity_type"],
                        arguments["content"],
                        arguments.get("course_id")
                    )
                elif name == "send_notification":
                    result = await self.send_notification(
                        arguments["user_id"],
                        arguments["message"],
                        arguments.get("type", "info")
                    )
                elif name == "analyze_quiz_results":
                    result = await self.analyze_quiz_results(
                        arguments["user_id"],
                        arguments["quiz_id"],
                        arguments.get("course_id")
                    )
                elif name == "generate_practice_questions":
                    result = await self.generate_practice_questions(
                        arguments["topic"],
                        arguments.get("difficulty", "medium"),
                        arguments.get("count", 5)
                    )
                elif name == "get_learning_recommendations":
                    result = await self.get_learning_recommendations(
                        arguments["user_id"],
                        arguments.get("course_id"),
                        arguments.get("analysis_type", "comprehensive")
                    )
                else:
                    result = {"error": f"Unknown tool: {name}"}
                
                return [TextContent(type="text", text=json.dumps(result, ensure_ascii=False, indent=2))]
                
            except Exception as e:
                return [TextContent(type="text", text=json.dumps({"error": str(e)}, ensure_ascii=False))]
    
    async def get_course_content(self, course_id: int, content_type: str = "all") -> Dict[str, Any]:
        """Tool: Lấy nội dung khóa học"""
        try:
            content = self.moodle_tools.get_course_content(course_id)
            
            if content_type == "files":
                content = [item for item in content if item.get("type") == "file"]
            elif content_type == "quizzes":
                content = [item for item in content if item.get("type") == "quiz"]
            elif content_type == "assignments":
                content = [item for item in content if item.get("type") == "assignment"]
            
            return {
                "success": True,
                "course_id": course_id,
                "content_type": content_type,
                "items": content,
                "count": len(content)
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def get_user_progress(self, user_id: int, course_id: Optional[int] = None) -> Dict[str, Any]:
        """Tool: Lấy tiến độ học viên"""
        try:
            progress = self.moodle_tools.get_user_progress(user_id, course_id)
            return {
                "success": True,
                "user_id": user_id,
                "course_id": course_id,
                "progress": progress
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def search_course_documents(self, query: str, course_id: Optional[int] = None, limit: int = 5) -> Dict[str, Any]:
        """Tool: Tìm kiếm tài liệu"""
        try:
            results = self.rag_system.search_relevant_content(query, course_id, limit)
            return {
                "success": True,
                "query": query,
                "course_id": course_id,
                "results": results,
                "count": len(results.get("content", []))
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def create_learning_activity(self, user_id: int, activity_type: str, content: str, course_id: Optional[int] = None) -> Dict[str, Any]:
        """Tool: Tạo hoạt động học tập"""
        try:
            activity_data = {
                "type": activity_type,
                "content": content,
                "course_id": course_id,
                "created_at": datetime.now().isoformat()
            }
            
            success = self.moodle_tools.create_learning_activity(user_id, activity_data)
            return {
                "success": success,
                "user_id": user_id,
                "activity_type": activity_type,
                "activity_id": activity_data.get("id") if success else None
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def send_notification(self, user_id: int, message: str, type: str = "info") -> Dict[str, Any]:
        """Tool: Gửi thông báo"""
        try:
            success = self.moodle_tools.send_notification(user_id, message, type)
            return {
                "success": success,
                "user_id": user_id,
                "message": message,
                "type": type
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def analyze_quiz_results(self, user_id: int, quiz_id: int, course_id: Optional[int] = None) -> Dict[str, Any]:
        """Tool: Phân tích kết quả quiz"""
        try:
            quiz_results = self.moodle_tools.get_quiz_results(user_id, course_id)
            quiz_data = [result for result in quiz_results if result.get("quiz_id") == quiz_id]
            
            if not quiz_data:
                return {"success": False, "error": "Quiz not found"}
            
            # Phân tích kết quả
            analysis = {
                "total_questions": quiz_data[0].get("total_questions", 0),
                "correct_answers": quiz_data[0].get("correct_answers", 0),
                "score_percentage": quiz_data[0].get("score_percentage", 0),
                "time_taken": quiz_data[0].get("time_taken", 0),
                "weak_areas": self.identify_weak_areas(quiz_data[0]),
                "recommendations": self.generate_quiz_recommendations(quiz_data[0])
            }
            
            return {
                "success": True,
                "user_id": user_id,
                "quiz_id": quiz_id,
                "analysis": analysis
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def generate_practice_questions(self, topic: str, difficulty: str = "medium", count: int = 5) -> Dict[str, Any]:
        """Tool: Tạo câu hỏi luyện tập"""
        try:
            questions = self.rag_system.generate_questions(topic, difficulty, count)
            return {
                "success": True,
                "topic": topic,
                "difficulty": difficulty,
                "questions": questions,
                "count": len(questions)
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    async def get_learning_recommendations(self, user_id: int, course_id: Optional[int] = None, analysis_type: str = "comprehensive") -> Dict[str, Any]:
        """Tool: Đưa ra khuyến nghị học tập"""
        try:
            # Lấy dữ liệu học viên
            user_data = self.moodle_tools.get_comprehensive_data(user_id, course_id)
            
            # Phân tích và đưa ra khuyến nghị
            recommendations = {
                "learning_path": self.suggest_learning_path(user_data),
                "weak_areas": self.identify_weak_areas(user_data),
                "study_schedule": self.create_study_schedule(user_data),
                "resources": self.recommend_resources(user_data),
                "goals": self.set_learning_goals(user_data)
            }
            
            return {
                "success": True,
                "user_id": user_id,
                "course_id": course_id,
                "analysis_type": analysis_type,
                "recommendations": recommendations
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def identify_weak_areas(self, data: Dict[str, Any]) -> List[str]:
        """Xác định điểm yếu"""
        weak_areas = []
        
        if data.get("score_percentage", 0) < 70:
            weak_areas.append("Kiến thức cơ bản")
        
        if data.get("time_taken", 0) > data.get("average_time", 0) * 1.5:
            weak_areas.append("Tốc độ xử lý")
        
        # Thêm logic phân tích khác
        return weak_areas
    
    def generate_quiz_recommendations(self, quiz_data: Dict[str, Any]) -> List[str]:
        """Tạo khuyến nghị từ kết quả quiz"""
        recommendations = []
        
        if quiz_data.get("score_percentage", 0) < 60:
            recommendations.append("Cần ôn tập lại kiến thức cơ bản")
        
        if quiz_data.get("time_taken", 0) > quiz_data.get("time_limit", 0) * 0.8:
            recommendations.append("Cần cải thiện tốc độ làm bài")
        
        return recommendations
    
    def suggest_learning_path(self, user_data: Dict[str, Any]) -> Dict[str, Any]:
        """Đề xuất lộ trình học tập"""
        return {
            "current_level": user_data.get("current_level", "beginner"),
            "next_topics": ["Topic A", "Topic B", "Topic C"],
            "estimated_time": "2-3 tuần",
            "prerequisites": ["Basic knowledge"]
        }
    
    def create_study_schedule(self, user_data: Dict[str, Any]) -> Dict[str, Any]:
        """Tạo lịch học"""
        return {
            "daily_study_time": "1-2 giờ",
            "weekly_schedule": ["Thứ 2, 4, 6"],
            "break_intervals": "25 phút học, 5 phút nghỉ"
        }
    
    def recommend_resources(self, user_data: Dict[str, Any]) -> List[Dict[str, Any]]:
        """Đề xuất tài liệu"""
        return [
            {"type": "video", "title": "Video giảng dạy", "url": "..."},
            {"type": "document", "title": "Tài liệu tham khảo", "url": "..."},
            {"type": "quiz", "title": "Bài tập luyện tập", "url": "..."}
        ]
    
    def set_learning_goals(self, user_data: Dict[str, Any]) -> List[str]:
        """Đặt mục tiêu học tập"""
        return [
            "Hoàn thành 80% nội dung khóa học",
            "Đạt điểm trung bình trên 7.0",
            "Tham gia tích cực các hoạt động"
        ]

async def main():
    """Main function to run MCP server"""
    # Initialize dependencies
    moodle_tools = MoodleTools(config)
    rag_system = MoodleRAGSystem(config)
    
    # Create MCP server
    server = MoodleMCPServer(moodle_tools, rag_system)
    
    # Run server
    async with stdio_server() as (read_stream, write_stream):
        await server.server.run(
            read_stream,
            write_stream,
            InitializationOptions(
                server_name="moodle-ai-server",
                server_version="1.0.0",
                capabilities=server.server.get_capabilities(
                    notification_options=None,
                    experimental_capabilities=None
                )
            )
        )

if __name__ == "__main__":
    asyncio.run(main())
```

### 4.2 MCP Client Integration

#### **4.2.1 LangChain MCP Client**
```python
# local/aichatbot/python/mcp/mcp_client.py
from langchain.agents import Tool
from langchain.tools import BaseTool
from mcp.client import Client
from mcp.client.stdio import stdio_client
import asyncio
import json

class MoodleMCPClient:
    def __init__(self, config):
        self.config = config
        self.client = None
        self.tools = []
    
    async def connect(self):
        """Kết nối đến MCP server"""
        try:
            self.client = await stdio_client(
                command="python",
                args=[self.config["mcp_server_path"]]
            )
            
            # Lấy danh sách tools
            tools_response = await self.client.list_tools()
            self.tools = tools_response.tools
            
            return True
        except Exception as e:
            print(f"Error connecting to MCP server: {e}")
            return False
    
    async def call_tool(self, tool_name: str, arguments: Dict[str, Any]) -> Dict[str, Any]:
        """Gọi tool trên MCP server"""
        try:
            if not self.client:
                await self.connect()
            
            response = await self.client.call_tool(tool_name, arguments)
            
            if response.content:
                result = json.loads(response.content[0].text)
                return result
            else:
                return {"error": "No response from MCP server"}
                
        except Exception as e:
            return {"error": str(e)}
    
    def get_langchain_tools(self) -> List[Tool]:
        """Chuyển đổi MCP tools thành LangChain tools"""
        langchain_tools = []
        
        for mcp_tool in self.tools:
            def create_tool_func(tool_name):
                async def tool_func(**kwargs):
                    result = await self.call_tool(tool_name, kwargs)
                    return json.dumps(result, ensure_ascii=False)
                return tool_func
            
            langchain_tool = Tool(
                name=mcp_tool.name,
                description=mcp_tool.description,
                func=create_tool_func(mcp_tool.name)
            )
            
            langchain_tools.append(langchain_tool)
        
        return langchain_tools

# Integration với LangChain Agents
class MoodleMCPTool(BaseTool):
    """Base tool class cho Moodle MCP integration"""
    
    name: str
    description: str
    mcp_client: MoodleMCPClient
    
    def __init__(self, name: str, description: str, mcp_client: MoodleMCPClient):
        super().__init__()
        self.name = name
        self.description = description
        self.mcp_client = mcp_client
    
    async def _arun(self, **kwargs) -> str:
        """Async run method"""
        result = await self.mcp_client.call_tool(self.name, kwargs)
        return json.dumps(result, ensure_ascii=False)
    
    def _run(self, **kwargs) -> str:
        """Sync run method"""
        loop = asyncio.new_event_loop()
        asyncio.set_event_loop(loop)
        try:
            result = loop.run_until_complete(self._arun(**kwargs))
            return result
        finally:
            loop.close()
```

### 4.3 MCP Server Configuration

#### **4.3.1 MCP Server Config**
```python
# local/aichatbot/python/config/mcp_config.py
import os
from typing import Dict, Any

class MCPConfig:
    def __init__(self):
        self.config = {
            "mcp_server": {
                "name": "moodle-ai-server",
                "version": "1.0.0",
                "description": "Moodle AI Agentic MCP Server",
                "capabilities": {
                    "tools": True,
                    "resources": True,
                    "prompts": True,
                    "logging": True
                }
            },
            "moodle": {
                "db_host": os.getenv("MOODLE_DB_HOST", "localhost"),
                "db_name": os.getenv("MOODLE_DB_NAME", "moodle"),
                "db_user": os.getenv("MOODLE_DB_USER", "moodle_user"),
                "db_pass": os.getenv("MOODLE_DB_PASS", "moodle_password"),
                "base_url": os.getenv("MOODLE_BASE_URL", "http://localhost/moodle")
            },
            "ai": {
                "openai_api_key": os.getenv("OPENAI_API_KEY"),
                "model": "gpt-4",
                "temperature": 0.7,
                "max_tokens": 2000
            },
            "rag": {
                "vector_db_path": os.getenv("VECTOR_DB_PATH", "./vector_db"),
                "embedding_model": "sentence-transformers/all-MiniLM-L6-v2",
                "chunk_size": 1000,
                "chunk_overlap": 200
            },
            "logging": {
                "level": "INFO",
                "file": "./logs/mcp_server.log",
                "max_size": "10MB",
                "backup_count": 5
            }
        }
    
    def get(self, key: str, default: Any = None) -> Any:
        """Get config value"""
        keys = key.split(".")
        value = self.config
        
        for k in keys:
            if isinstance(value, dict) and k in value:
                value = value[k]
            else:
                return default
        
        return value
    
    def get_moodle_config(self) -> Dict[str, Any]:
        """Get Moodle configuration"""
        return self.config["moodle"]
    
    def get_ai_config(self) -> Dict[str, Any]:
        """Get AI configuration"""
        return self.config["ai"]
    
    def get_rag_config(self) -> Dict[str, Any]:
        """Get RAG configuration"""
        return self.config["rag"]
```

### 4.4 MCP Server Deployment

#### **4.4.1 Systemd Service**
```ini
# /etc/systemd/system/moodle-mcp-server.service
[Unit]
Description=Moodle AI MCP Server
After=network.target

[Service]
Type=simple
User=moodle
Group=moodle
WorkingDirectory=/var/www/moodle/local/aichatbot/python
ExecStart=/usr/bin/python3 mcp/mcp_server.py
Restart=always
RestartSec=10
Environment=PYTHONPATH=/var/www/moodle/local/aichatbot/python
Environment=OPENAI_API_KEY=your_api_key_here

[Install]
WantedBy=multi-user.target
```

#### **4.4.2 Docker Configuration**
```dockerfile
# local/aichatbot/docker/Dockerfile.mcp
FROM python:3.9-slim

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    g++ \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy MCP server code
COPY python/ ./python/
COPY config/ ./config/

# Set environment variables
ENV PYTHONPATH=/app/python
ENV MCP_SERVER_PORT=8000

# Expose port
EXPOSE 8000

# Run MCP server
CMD ["python", "python/mcp/mcp_server.py"]
```

#### **4.4.3 Docker Compose**
```yaml
# local/aichatbot/docker/docker-compose.yml
version: '3.8'

services:
  moodle-mcp-server:
    build:
      context: .
      dockerfile: Dockerfile.mcp
    container_name: moodle-mcp-server
    ports:
      - "8000:8000"
    environment:
      - OPENAI_API_KEY=${OPENAI_API_KEY}
      - MOODLE_DB_HOST=${MOODLE_DB_HOST}
      - MOODLE_DB_NAME=${MOODLE_DB_NAME}
      - MOODLE_DB_USER=${MOODLE_DB_USER}
      - MOODLE_DB_PASS=${MOODLE_DB_PASS}
      - VECTOR_DB_PATH=/app/vector_db
    volumes:
      - ./vector_db:/app/vector_db
      - ./logs:/app/logs
    restart: unless-stopped
    depends_on:
      - moodle-db
      - redis

  moodle-db:
    image: mysql:8.0
    container_name: moodle-db
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${MOODLE_DB_NAME}
      - MYSQL_USER=${MOODLE_DB_USER}
      - MYSQL_PASSWORD=${MOODLE_DB_PASS}
    volumes:
      - moodle_db_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    container_name: moodle-redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

volumes:
  moodle_db_data:
  redis_data:
```

---

## 5. TÍCH HỢP VỚI MOODLE

### 4.1 PHP Bridge cho LangChain

```php
<?php
// local/aichatbot/python_bridge.php
require_once("../../config.php");

class LangChainBridge {
    private $python_path;
    private $script_path;
    
    public function __construct() {
        $this->python_path = get_config('local_aichatbot', 'python_path') ?: 'python';
        $this->script_path = __DIR__ . '/python/';
    }
    
    public function call_study_agent($message, $user_id, $course_id) {
        $script = $this->script_path . 'agents/study_agent.py';
        $input = json_encode([
            'action' => 'process_question',
            'message' => $message,
            'user_id' => $user_id,
            'course_id' => $course_id
        ]);
        
        $command = "{$this->python_path} {$script} " . escapeshellarg($input);
        $output = shell_exec($command);
        
        return json_decode($output, true);
    }
    
    public function call_progress_agent($user_id, $course_id) {
        $script = $this->script_path . 'agents/progress_agent.py';
        $input = json_encode([
            'action' => 'analyze_progress',
            'user_id' => $user_id,
            'course_id' => $course_id
        ]);
        
        $command = "{$this->python_path} {$script} " . escapeshellarg($input);
        $output = shell_exec($command);
        
        return json_decode($output, true);
    }
    
    public function call_motivation_agent($user_id, $context = 'general') {
        $script = $this->script_path . 'agents/motivation_agent.py';
        $input = json_encode([
            'action' => 'motivate_student',
            'user_id' => $user_id,
            'context' => $context
        ]);
        
        $command = "{$this->python_path} {$script} " . escapeshellarg($input);
        $output = shell_exec($command);
        
        return json_decode($output, true);
    }
    
    public function process_rag_query($query, $course_id) {
        $script = $this->script_path . 'rag/rag_system.py';
        $input = json_encode([
            'action' => 'search_content',
            'query' => $query,
            'course_id' => $course_id
        ]);
        
        $command = "{$this->python_path} {$script} " . escapeshellarg($input);
        $output = shell_exec($command);
        
        return json_decode($output, true);
    }
    
    public function get_course_files($course_id) {
        global $DB;
        
        $sql = "SELECT f.*, ctx.instanceid as courseid 
                FROM {files} f 
                JOIN {context} ctx ON f.contextid = ctx.id 
                WHERE ctx.contextlevel = ? AND ctx.instanceid = ? 
                AND f.filename != '.' AND f.filesize > 0";
        
        $files = $DB->get_records_sql($sql, [CONTEXT_COURSE, $course_id]);
        
        $result = [];
        foreach ($files as $file) {
            $file_path = $this->get_file_path($file);
            if (file_exists($file_path)) {
                $result[] = [
                    'id' => $file->id,
                    'filename' => $file->filename,
                    'filepath' => $file_path,
                    'mimetype' => $file->mimetype,
                    'filesize' => $file->filesize
                ];
            }
        }
        
        return $result;
    }
    
    private function get_file_path($file) {
        global $CFG;
        
        $file_path = $CFG->dataroot . '/filedir/' . 
                    substr($file->contenthash, 0, 2) . '/' . 
                    substr($file->contenthash, 2, 2) . '/' . 
                    $file->contenthash;
        
        return $file_path;
    }
}

// API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $bridge = new LangChainBridge();
    
    switch ($data['action']) {
        case 'get_course_files':
            $result = $bridge->get_course_files($data['course_id']);
            break;
        case 'call_study_agent':
            $result = $bridge->call_study_agent(
                $data['message'], 
                $data['user_id'], 
                $data['course_id']
            );
            break;
        case 'call_progress_agent':
            $result = $bridge->call_progress_agent(
                $data['user_id'], 
                $data['course_id']
            );
            break;
        case 'call_motivation_agent':
            $result = $bridge->call_motivation_agent(
                $data['user_id'], 
                $data['context']
            );
            break;
        default:
            $result = ['error' => 'Invalid action'];
    }
    
    echo json_encode($result);
}
?>
```

### 4.2 Moodle Tools cho LangChain

```python
# local/aichatbot/python/tools/moodle_tools.py
import subprocess
import json
from typing import Dict, List, Any

class MoodleTools:
    def __init__(self, config):
        self.config = config
        self.bridge_script = config["bridge_script_path"]
    
    def get_student_info(self, user_id: int, course_id: int) -> Dict[str, Any]:
        """Lấy thông tin học viên"""
        result = self._call_php_bridge("get_student_info", {
            "user_id": user_id,
            "course_id": course_id
        })
        return result
    
    def get_user_progress(self, user_id: int, course_id: int = None) -> Dict[str, Any]:
        """Lấy tiến độ học viên"""
        result = self._call_php_bridge("get_user_progress", {
            "user_id": user_id,
            "course_id": course_id
        })
        return result
    
    def get_course_content(self, course_id: int) -> List[Dict[str, Any]]:
        """Lấy nội dung khóa học"""
        result = self._call_php_bridge("get_course_content", {
            "course_id": course_id
        })
        return result
    
    def get_quiz_results(self, user_id: int, course_id: int) -> List[Dict[str, Any]]:
        """Lấy kết quả quiz"""
        result = self._call_php_bridge("get_quiz_results", {
            "user_id": user_id,
            "course_id": course_id
        })
        return result
    
    def get_assignment_submissions(self, user_id: int, course_id: int) -> List[Dict[str, Any]]:
        """Lấy bài nộp assignment"""
        result = self._call_php_bridge("get_assignment_submissions", {
            "user_id": user_id,
            "course_id": course_id
        })
        return result
    
    def get_forum_participation(self, user_id: int, course_id: int) -> Dict[str, Any]:
        """Lấy tham gia forum"""
        result = self._call_php_bridge("get_forum_participation", {
            "user_id": user_id,
            "course_id": course_id
        })
        return result
    
    def create_learning_activity(self, user_id: int, activity_data: Dict[str, Any]) -> bool:
        """Tạo hoạt động học tập"""
        result = self._call_php_bridge("create_learning_activity", {
            "user_id": user_id,
            "activity_data": activity_data
        })
        return result.get("success", False)
    
    def send_notification(self, user_id: int, message: str, type: str = "info") -> bool:
        """Gửi thông báo"""
        result = self._call_php_bridge("send_notification", {
            "user_id": user_id,
            "message": message,
            "type": type
        })
        return result.get("success", False)
    
    def _call_php_bridge(self, action: str, data: Dict[str, Any]) -> Dict[str, Any]:
        """Gọi PHP bridge"""
        input_data = {
            "action": action,
            **data
        }
        
        try:
            result = subprocess.run([
                "php",
                self.bridge_script,
                json.dumps(input_data)
            ], capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                return json.loads(result.stdout)
            else:
                return {"error": result.stderr}
                
        except subprocess.TimeoutExpired:
            return {"error": "Request timeout"}
        except Exception as e:
            return {"error": str(e)}
```

---

## 5. ROADMAP TRIỂN KHAI

### 5.1 Giai đoạn 1: Chuẩn bị (Tuần 1-2)

#### **Tuần 1:**
- [ ] Cài đặt Python environment
- [ ] Cài đặt LangChain và dependencies
- [ ] Thiết lập OpenAI API key
- [ ] Tạo cấu trúc thư mục

#### **Tuần 2:**
- [ ] Tạo PHP bridge cơ bản
- [ ] Thiết lập vector database
- [ ] Tạo database schema mới
- [ ] Testing kết nối Python-PHP

### 5.2 Giai đoạn 2: RAG System (Tuần 3-4)

#### **Tuần 3:**
- [ ] Implement Document Processor
- [ ] Tạo Vector Database Manager
- [ ] Xử lý tài liệu Moodle
- [ ] Testing RAG pipeline

#### **Tuần 4:**
- [ ] Tối ưu hóa embeddings
- [ ] Implement similarity search
- [ ] Tạo QA chains
- [ ] Testing với dữ liệu thực

### 5.3 Giai đoạn 3: AI Agents (Tuần 5-7)

#### **Tuần 5:**
- [ ] Implement Study Agent
- [ ] Tạo tools cho Study Agent
- [ ] Testing Study Agent
- [ ] Tích hợp với RAG

#### **Tuần 6:**
- [ ] Implement Progress Agent
- [ ] Tạo analytics engine
- [ ] Testing Progress Agent
- [ ] Tích hợp với Moodle data

#### **Tuần 7:**
- [ ] Implement Motivation Agent
- [ ] Tạo gamification engine
- [ ] Testing Motivation Agent
- [ ] Tích hợp với user preferences

### 5.4 Giai đoạn 4: Integration (Tuần 8-9)

#### **Tuần 8:**
- [ ] Tích hợp tất cả agents
- [ ] Tạo Student Coordinator
- [ ] Testing end-to-end
- [ ] Tối ưu hóa performance

#### **Tuần 9:**
- [ ] Cập nhật giao diện Moodle
- [ ] Tạo agent selector
- [ ] Implement real-time chat
- [ ] Testing user experience

### 5.5 Giai đoạn 5: Testing & Deployment (Tuần 10-12)

#### **Tuần 10:**
- [ ] Unit testing
- [ ] Integration testing
- [ ] Performance testing
- [ ] Security testing

#### **Tuần 11:**
- [ ] User acceptance testing
- [ ] Bug fixes
- [ ] Documentation
- [ ] Training materials

#### **Tuần 12:**
- [ ] Production deployment
- [ ] Monitoring setup
- [ ] User training
- [ ] Go-live support

---

## 6. CẤU HÌNH VÀ DEPLOYMENT

### 6.1 Requirements

#### **Python Dependencies:**
```txt
# requirements.txt
langchain==0.1.0
openai==1.3.0
chromadb==0.4.18
sentence-transformers==2.2.2
numpy==1.24.3
pandas==2.0.3
scikit-learn==1.3.0
python-dotenv==1.0.0
```

#### **PHP Extensions:**
- PHP 8.0+
- JSON extension
- cURL extension
- MySQL/PostgreSQL extension

#### **System Requirements:**
- RAM: 8GB+ (16GB recommended)
- Storage: 50GB+ for vector database
- CPU: 4 cores+ (8 cores recommended)

### 6.2 Configuration

#### **Environment Variables:**
```bash
# .env
OPENAI_API_KEY=your_openai_api_key
MOODLE_DB_HOST=localhost
MOODLE_DB_NAME=moodle
MOODLE_DB_USER=moodle_user
MOODLE_DB_PASS=moodle_password
VECTOR_DB_PATH=/path/to/vector/database
PYTHON_PATH=/usr/bin/python3
```

#### **Moodle Settings:**
```php
// config.php additions
$CFG->local_aichatbot_enabled = true;
$CFG->local_aichatbot_openai_key = 'your_key';
$CFG->local_aichatbot_vector_db_path = '/path/to/vector/db';
$CFG->local_aichatbot_python_path = '/usr/bin/python3';
```

### 6.3 Monitoring và Maintenance

#### **Logging:**
```python
# local/aichatbot/python/utils/logger.py
import logging
import os
from datetime import datetime

class MoodleAILogger:
    def __init__(self, log_dir="./logs"):
        self.log_dir = log_dir
        os.makedirs(log_dir, exist_ok=True)
        
        # Setup logging
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
            handlers=[
                logging.FileHandler(f"{log_dir}/moodle_ai_{datetime.now().strftime('%Y%m%d')}.log"),
                logging.StreamHandler()
            ]
        )
        
        self.logger = logging.getLogger('MoodleAI')
    
    def log_agent_interaction(self, agent_type, user_id, action, result):
        """Log agent interactions"""
        self.logger.info(f"Agent: {agent_type}, User: {user_id}, Action: {action}, Result: {result}")
    
    def log_rag_query(self, query, course_id, results_count, response_time):
        """Log RAG queries"""
        self.logger.info(f"RAG Query: {query[:100]}..., Course: {course_id}, Results: {results_count}, Time: {response_time}ms")
    
    def log_error(self, error_type, error_message, context=None):
        """Log errors"""
        self.logger.error(f"Error: {error_type}, Message: {error_message}, Context: {context}")
```

#### **Performance Monitoring:**
```python
# local/aichatbot/python/utils/monitor.py
import time
import psutil
import json
from datetime import datetime

class PerformanceMonitor:
    def __init__(self):
        self.metrics = {}
    
    def start_timer(self, operation_name):
        """Bắt đầu đo thời gian"""
        self.metrics[operation_name] = {
            'start_time': time.time(),
            'start_memory': psutil.Process().memory_info().rss
        }
    
    def end_timer(self, operation_name):
        """Kết thúc đo thời gian"""
        if operation_name in self.metrics:
            end_time = time.time()
            end_memory = psutil.Process().memory_info().rss
            
            self.metrics[operation_name].update({
                'end_time': end_time,
                'end_memory': end_memory,
                'duration': end_time - self.metrics[operation_name]['start_time'],
                'memory_used': end_memory - self.metrics[operation_name]['start_memory']
            })
            
            return self.metrics[operation_name]
        return None
    
    def get_system_stats(self):
        """Lấy thống kê hệ thống"""
        return {
            'cpu_percent': psutil.cpu_percent(),
            'memory_percent': psutil.virtual_memory().percent,
            'disk_usage': psutil.disk_usage('/').percent,
            'timestamp': datetime.now().isoformat()
        }
    
    def save_metrics(self, filepath):
        """Lưu metrics"""
        with open(filepath, 'w') as f:
            json.dump(self.metrics, f, indent=2)
```

---

## 7. KẾT LUẬN

Kế hoạch triển khai AI Agentic trong Moodle sử dụng LangChain sẽ tạo ra một hệ thống "gia sư ảo" thông minh với các đặc điểm:

### **7.1 Lợi ích chính:**
- **Học tập cá nhân hóa**: AI điều chỉnh nội dung theo từng học viên
- **Hỗ trợ 24/7**: Học viên có thể học bất cứ lúc nào
- **Theo dõi tiến độ thông minh**: Phân tích và dự đoán hiệu suất
- **Động viên học tập**: Gamification và khuyến khích tích cực
- **Tích hợp sâu**: Tận dụng toàn bộ dữ liệu Moodle

### **7.2 Tính khả thi:**
- ✅ **Kỹ thuật**: LangChain cung cấp framework hoàn chỉnh
- ✅ **Tích hợp**: Moodle plugin architecture linh hoạt
- ✅ **Dữ liệu**: Tận dụng dữ liệu có sẵn trong Moodle
- ✅ **Mở rộng**: Dễ dàng thêm agents và tính năng mới

### **7.3 ROI dự kiến:**
- **Tăng engagement**: 40-60% học viên tích cực hơn
- **Cải thiện kết quả**: 20-30% điểm số cao hơn
- **Giảm dropout**: 25-35% tỷ lệ bỏ học thấp hơn
- **Tiết kiệm thời gian**: 50-70% thời gian hỗ trợ giảng viên

**Kế hoạch này sẽ biến Moodle thành một nền tảng học tập thông minh, nơi AI không chỉ hỗ trợ mà còn thúc đẩy việc học tập hiệu quả cho mọi học viên.**
