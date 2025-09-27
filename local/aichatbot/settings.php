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
 * Settings for local_aichatbot plugin.
 *
 * @package    local_aichatbot
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_aichatbot', get_string('settings', 'local_aichatbot'));
    
    if ($ADMIN->fulltree) {
        // API Configuration
        $settings->add(new admin_setting_heading('local_aichatbot_api', 
            get_string('apikey', 'local_aichatbot'), 
            'Cấu hình API cho AI Chatbot'));
        
        $settings->add(new admin_setting_configtext('local_aichatbot/apikey',
            get_string('apikey', 'local_aichatbot'),
            get_string('apikey_desc', 'local_aichatbot'),
            '', PARAM_TEXT));
        
        $settings->add(new admin_setting_configtext('local_aichatbot/apiurl',
            get_string('apiurl', 'local_aichatbot'),
            get_string('apiurl_desc', 'local_aichatbot'),
            'https://api.openai.com/v1/chat/completions', PARAM_URL));
        
        $settings->add(new admin_setting_configtext('local_aichatbot/model',
            get_string('model', 'local_aichatbot'),
            get_string('model_desc', 'local_aichatbot'),
            'gpt-3.5-turbo', PARAM_TEXT));
        
        $settings->add(new admin_setting_configtext('local_aichatbot/max_tokens',
            get_string('max_tokens', 'local_aichatbot'),
            get_string('max_tokens_desc', 'local_aichatbot'),
            '1000', PARAM_INT));
        
        $settings->add(new admin_setting_configtext('local_aichatbot/temperature',
            get_string('temperature', 'local_aichatbot'),
            get_string('temperature_desc', 'local_aichatbot'),
            '0.7', PARAM_FLOAT));
    }
    
    $ADMIN->add('localplugins', $settings);
}
