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
 * Ollama API Handler for AI Chatbot
 * Handler cho Ollama API (miễn phí)
 *
 * @package    local_aichatbot
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get AI response from Ollama API
 */
function get_ollama_response($message) {
    global $CFG;
    
    // Include enhanced prompts
    require_once(__DIR__ . '/enhanced_prompts.php');
    
    // Get settings
    $model = get_config('local_aichatbot', 'model') ?: 'llama2';
    $ollama_url = get_config('local_aichatbot', 'apiurl') ?: 'http://localhost:11434';
    
    // Get optimized prompt and options based on message type
    $optimized_prompt = get_optimized_prompt($message);
    $response_options = get_response_options($message);
    
    // Prepare request data for Ollama
    $data = [
        'model' => $model,
        'prompt' => $optimized_prompt,
        'stream' => false,
        'options' => $response_options
    ];
    
    // Make API request to Ollama
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ollama_url . '/api/generate');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Tăng timeout lên 2 phút
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout kết nối
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TCP_NODELAY, true); // Tối ưu TCP
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Check for cURL errors
    if ($curl_error) {
        throw new Exception('Lỗi kết nối Ollama: ' . $curl_error);
    }
    
    // Check HTTP status code
    if ($http_code !== 200) {
        throw new Exception('Ollama trả về lỗi HTTP ' . $http_code);
    }
    
    // Parse response
    $response_data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Lỗi phân tích JSON response từ Ollama');
    }
    
    // Extract response text
    if (isset($response_data['response'])) {
        return trim($response_data['response']);
    } else {
        throw new Exception('Không nhận được response từ Ollama');
    }
}

/**
 * Check if Ollama is running
 */
function check_ollama_status() {
    $ollama_url = get_config('local_aichatbot', 'apiurl') ?: 'http://localhost:11434';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ollama_url . '/api/tags');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code === 200;
}
?>
