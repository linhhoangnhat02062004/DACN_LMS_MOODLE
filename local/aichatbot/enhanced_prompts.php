<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Enhanced Prompts for AI Chatbot
 * Các prompt được tối ưu hóa cho từng loại câu hỏi
 *
 * @package    local_aichatbot
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get optimized prompt based on message type
 */
function get_optimized_prompt($message) {
    $message_lower = strtolower($message);
    
    // Programming questions
    if (preg_match('/\b(code|coding|lập trình|programming|function|class|variable|algorithm|debug|error|syntax)\b/', $message_lower)) {
        return get_programming_prompt($message);
    }
    
    // Educational questions
    if (preg_match('/\b(học|study|learn|bài tập|exercise|giải thích|explain|tutorial|hướng dẫn)\b/', $message_lower)) {
        return get_educational_prompt($message);
    }
    
    // Technical questions
    if (preg_match('/\b(technical|kỹ thuật|system|server|database|api|config|setup|install)\b/', $message_lower)) {
        return get_technical_prompt($message);
    }
    
    // General questions
    return get_general_prompt($message);
}

/**
 * Programming prompt
 */
function get_programming_prompt($message) {
    return "Bạn là một chuyên gia lập trình với nhiều năm kinh nghiệm. Hãy trả lời câu hỏi lập trình một cách:

🎯 **Yêu cầu:**
- Chính xác về mặt kỹ thuật
- Cung cấp code example cụ thể
- Giải thích logic rõ ràng
- Đưa ra best practices
- Sử dụng tiếng Việt dễ hiểu

📝 **Cấu trúc trả lời:**
1. Giải thích ngắn gọn
2. Code example (nếu có)
3. Giải thích từng bước
4. Lưu ý quan trọng

Câu hỏi lập trình: " . $message . "

Trả lời:";
}

/**
 * Educational prompt
 */
function get_educational_prompt($message) {
    return "Bạn là một giáo viên giàu kinh nghiệm và nhiệt huyết. Hãy trả lời câu hỏi học tập một cách:

🎓 **Yêu cầu:**
- Dễ hiểu và từng bước
- Có ví dụ thực tế
- Khuyến khích tư duy
- Cung cấp tài liệu tham khảo
- Sử dụng ngôn ngữ thân thiện

📚 **Cấu trúc trả lời:**
1. Khái niệm cơ bản
2. Ví dụ minh họa
3. Ứng dụng thực tế
4. Bài tập luyện tập (nếu phù hợp)

**QUAN TRỌNG:** Sử dụng xuống dòng và format đẹp:
- Dùng **bold** cho tiêu đề
- Dùng • cho danh sách
- Dùng số 1. 2. 3. cho các bước
- Xuống dòng giữa các phần

Câu hỏi học tập: " . $message . "

Trả lời:";
}

/**
 * Technical prompt
 */
function get_technical_prompt($message) {
    return "Bạn là một kỹ sư hệ thống chuyên nghiệp. Hãy trả lời câu hỏi kỹ thuật một cách:

⚙️ **Yêu cầu:**
- Chính xác về mặt kỹ thuật
- Cung cấp giải pháp cụ thể
- Đưa ra các bước thực hiện
- Cảnh báo rủi ro nếu có
- Sử dụng thuật ngữ chính xác

🔧 **Cấu trúc trả lời:**
1. Phân tích vấn đề
2. Giải pháp đề xuất
3. Các bước thực hiện
4. Lưu ý và cảnh báo

Câu hỏi kỹ thuật: " . $message . "

Trả lời:";
}

/**
 * General prompt
 */
function get_general_prompt($message) {
    return "Bạn là một AI assistant thông minh và hữu ích. Hãy trả lời câu hỏi một cách:

✨ **Yêu cầu:**
- Chính xác và đầy đủ
- Dễ hiểu và rõ ràng
- Có cấu trúc logic
- Sử dụng tiếng Việt tự nhiên
- Ngắn gọn nhưng đủ thông tin
- Có ví dụ cụ thể khi cần thiết

📋 **Cấu trúc trả lời:**
1. Trả lời trực tiếp
2. Giải thích chi tiết
3. Ví dụ minh họa (nếu cần)
4. Tóm tắt hoặc kết luận

**QUAN TRỌNG:** Sử dụng xuống dòng và format đẹp:
- Dùng **bold** cho tiêu đề quan trọng
- Dùng • cho danh sách
- Dùng số 1. 2. 3. cho các bước
- Xuống dòng giữa các phần để dễ đọc

Câu hỏi: " . $message . "

Trả lời:";
}

/**
 * Get response options based on prompt type
 */
function get_response_options($message) {
    $message_lower = strtolower($message);
    
    // Programming - more tokens for code examples
    if (preg_match('/\b(code|coding|lập trình|programming|function|class|variable|algorithm|debug|error|syntax)\b/', $message_lower)) {
        return [
            'temperature' => 0.3,  // Ít sáng tạo, chính xác hơn
            'top_p' => 0.9,
            'max_tokens' => 300    // Giảm để tăng tốc
        ];
    }
    
    // Educational - balanced
    if (preg_match('/\b(học|study|learn|bài tập|exercise|giải thích|explain|tutorial|hướng dẫn)\b/', $message_lower)) {
        return [
            'temperature' => 0.5,  // Cân bằng
            'top_p' => 0.9,
            'max_tokens' => 250    // Giảm để tăng tốc
        ];
    }
    
    // Technical - precise
    if (preg_match('/\b(technical|kỹ thuật|system|server|database|api|config|setup|install)\b/', $message_lower)) {
        return [
            'temperature' => 0.2,  // Rất chính xác
            'top_p' => 0.8,
            'max_tokens' => 250    // Giảm để tăng tốc
        ];
    }
    
    // General - default
    return [
        'temperature' => 0.7,
        'top_p' => 0.9,
        'max_tokens' => 200        // Giảm để tăng tốc
    ];
}
?>
