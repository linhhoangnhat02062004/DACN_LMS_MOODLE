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
 * AI Chatbot Plugin
 *
 * @package    local_aichatbot
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();

$PAGE->set_url('/local/aichatbot/index.php');
$PAGE->set_title(get_string('chatwithai', 'local_aichatbot'));
$PAGE->set_heading(get_string('chatwithai', 'local_aichatbot'));

echo $OUTPUT->header();

echo '<style>
.chatbot-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chatbot-header {
    background: #007cba;
    color: white;
    padding: 20px;
    text-align: center;
    font-weight: bold;
}

.chatbot-messages {
    height: 400px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.message {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
}

.message.user {
    justify-content: flex-end;
}

.message.ai {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 18px;
    word-wrap: break-word;
    line-height: 1.6;
    white-space: pre-wrap;
}

.message.user .message-content {
    background: #007cba;
    color: white;
    border-bottom-right-radius: 5px;
}

.message.ai .message-content {
    background: white;
    color: #333;
    border: 1px solid #e1e5e9;
    border-bottom-left-radius: 5px;
}

.chatbot-input {
    display: flex;
    padding: 15px;
    background: white;
    border-top: 1px solid #ddd;
}

.chatbot-input input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
}

.chatbot-input button {
    margin-left: 10px;
    padding: 12px 20px;
    background: #007cba;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.chatbot-input button:hover {
    background: #005a87;
}

.chatbot-input button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.typing-indicator {
    display: none;
    padding: 10px;
    color: #666;
    font-style: italic;
}

.typing-indicator.show {
    display: block;
}
</style>';

echo '<div class="chatbot-container">
    <div class="chatbot-header">
        🤖 ' . get_string('chatwithai', 'local_aichatbot') . '
    </div>
    
    <div class="chatbot-messages" id="chatMessages">
        <div class="message ai">
            <div class="message-content">
                👋 Xin chào! Tôi là AI assistant. Bạn có thể hỏi tôi bất cứ điều gì!
            </div>
        </div>
    </div>
    
    <div class="typing-indicator" id="typingIndicator">
        ' . get_string('typing', 'local_aichatbot') . '
    </div>
    
    <div class="chatbot-input">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn của bạn..." maxlength="1000">
        <button id="sendButton" onclick="sendMessage()">Gửi</button>
    </div>
</div>';

echo '<script>
let isTyping = false;

function addMessage(content, isUser = false) {
    const messagesContainer = document.getElementById("chatMessages");
    const messageDiv = document.createElement("div");
    messageDiv.className = "message " + (isUser ? "user" : "ai");
    
    const contentDiv = document.createElement("div");
    contentDiv.className = "message-content";
    
    if (isUser) {
        contentDiv.textContent = content;
    } else {
        // Simple formatting for AI messages
        let formattedContent = content;
        
        // Convert newlines to <br>
        formattedContent = formattedContent.replace(/\\n/g, "<br>");
        
        // Bold text **text**
        formattedContent = formattedContent.replace(/\\*\\*(.*?)\\*\\*/g, "<strong>$1</strong>");
        
        // Italic text *text*
        formattedContent = formattedContent.replace(/(?<!\\*)\\*([^*]+)\\*(?!\\*)/g, "<em>$1</em>");
        
        // Inline code `code`
        formattedContent = formattedContent.replace(/`([^`]+)`/g, "<code style=\\"background: #f1f1f1; padding: 2px 4px; border-radius: 3px; font-family: monospace;\\">$1</code>");
        
        // Numbered lists 1. 2. 3.
        formattedContent = formattedContent.replace(/(\\d+\\.\\s)/g, "<strong>$1</strong>");
        
        // Bullet points
        formattedContent = formattedContent.replace(/^[-•]\\s/gm, "• ");
        
        contentDiv.innerHTML = formattedContent;
    }
    
    messageDiv.appendChild(contentDiv);
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showTyping() {
    document.getElementById("typingIndicator").classList.add("show");
}

function hideTyping() {
    document.getElementById("typingIndicator").classList.remove("show");
}

function sendMessage() {
    const input = document.getElementById("messageInput");
    const button = document.getElementById("sendButton");
    const message = input.value.trim();
    
    if (!message || isTyping) return;
    
    // Add user message
    addMessage(message, true);
    input.value = "";
    
    // Disable input
    input.disabled = true;
    button.disabled = true;
    isTyping = true;
    showTyping();
    
    // Send to server
    fetch("ajax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=send_message&message=" + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        hideTyping();
        
        if (data.success) {
            addMessage(data.response);
        } else {
            addMessage("Lỗi: " + data.error);
        }
    })
    .catch(error => {
        hideTyping();
        addMessage("Lỗi kết nối: " + error);
    })
    .finally(() => {
        // Re-enable input
        input.disabled = false;
        button.disabled = false;
        isTyping = false;
        input.focus();
    });
}

// Enter key to send
document.getElementById("messageInput").addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
        sendMessage();
    }
});

// Focus input on load
document.getElementById("messageInput").focus();
</script>';

echo $OUTPUT->footer();
?>
