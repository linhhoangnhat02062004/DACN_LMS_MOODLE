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
 * AJAX handler for AI Chatbot.
 *
 * @package    local_aichatbot
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();

// Set JSON header
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$action = required_param('action', PARAM_TEXT);

if ($action === 'send_message') {
    $message = required_param('message', PARAM_TEXT);
    
    try {
        // Check if using Ollama
        $api_url = get_config('local_aichatbot', 'apiurl');
        if (strpos($api_url, 'ollama') !== false || strpos($api_url, '11434') !== false) {
            // Use Ollama
            require_once('ollama_handler.php');
            $response = get_ollama_response($message);
        } else {
            // Use OpenAI/Azure AI
            $response = get_ai_response($message);
        }
        
        echo json_encode([
            'success' => true,
            'response' => $response
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

/**
 * Get AI response from API
 */
function get_ai_response($message) {
    global $CFG;
    
    // Get settings
    $apikey = get_config('local_aichatbot', 'apikey');
    $apiurl = get_config('local_aichatbot', 'apiurl');
    $model = get_config('local_aichatbot', 'model');
    $max_tokens = get_config('local_aichatbot', 'max_tokens');
    $temperature = get_config('local_aichatbot', 'temperature');
    
    // Check if API key is configured
    if (empty($apikey)) {
        throw new Exception('API Key chưa được cấu hình. Vui lòng liên hệ admin.');
    }
    
    // Prepare request data
    $data = [
        'model' => $model ?: 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Bạn là một AI assistant thân thiện và hữu ích. Trả lời bằng tiếng Việt một cách tự nhiên và dễ hiểu.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'max_tokens' => (int)($max_tokens ?: 1000),
        'temperature' => (float)($temperature ?: 0.7)
    ];
    
    // Prepare headers
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apikey
    ];
    
    // Make API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiurl ?: 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Check for cURL errors
    if ($curl_error) {
        throw new Exception('Lỗi kết nối: ' . $curl_error);
    }
    
    // Check HTTP status code
    if ($http_code !== 200) {
        throw new Exception('API trả về lỗi HTTP ' . $http_code);
    }
    
    // Parse response
    $response_data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Lỗi phân tích JSON response');
    }
    
    // Check for API errors
    if (isset($response_data['error'])) {
        throw new Exception('API Error: ' . $response_data['error']['message']);
    }
    
    // Extract message content
    if (isset($response_data['choices'][0]['message']['content'])) {
        return trim($response_data['choices'][0]['message']['content']);
    } else {
        throw new Exception('Không nhận được response từ AI');
    }
}
?>
