<?php
/**
 * Class xử lý API cho OpenRouter
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class API_Handler {
    /**
     * API Endpoint của OpenRouter
     * 
     * @var string
     */
    private $api_endpoint = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * API Key từ cài đặt plugin
     * 
     * @var string
     */
    private $api_key;

    /**
     * Model mặc định
     * 
     * @var string
     */
    private $default_model;

    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = get_option( 'ntq_ai_connector_api_key', '' );
        $this->default_model = get_option( 'ntq_ai_connector_default_model', 'deepseek/deepseek-chat-v3-0324:free' );
    }

    /**
     * Kiểm tra xem API có sẵn sàng hay không
     * 
     * @return bool True nếu API key đã được cấu hình
     */
    public function is_api_ready() {
        return ! empty( $this->api_key );
    }

    /**
     * Gửi yêu cầu đến API để tóm tắt nội dung
     * 
     * @param string $title Tiêu đề bài viết
     * @param string $content Nội dung bài viết
     * @param string $model Model AI sẽ sử dụng
     * @param int $post_id ID của bài viết
     * @return array|WP_Error Kết quả từ API hoặc lỗi
     */
    public function summarize_content( $title, $content, $model = '', $post_id = 0 ) {
        // Kiểm tra API key
        if ( ! $this->is_api_ready() ) {
            return new WP_Error( 'api_error', __( 'API key chưa được cấu hình', 'ntq-ai-connector' ) );
        }

        // Kiểm tra giới hạn số lượng request/ngày
        if ( ! $this->check_daily_limit() ) {
            return new WP_Error( 'limit_error', __( 'Đã đạt giới hạn số request trong ngày', 'ntq-ai-connector' ) );
        }

        $model = empty( $model ) ? $this->default_model : $model;
        
        // Loại bỏ HTML tags và chuẩn hóa nội dung
        $clean_content = wp_strip_all_tags( $content );

        // Giới hạn độ dài nội dung để tránh vượt quá giới hạn token
        if ( strlen( $clean_content ) > 100000 ) {
            $clean_content = substr( $clean_content, 0, 100000 ) . '...';
        }

        // Tạo prompt cho tóm tắt
        $prompt = $this->get_summarize_prompt( $title, $clean_content );        // Lấy thông số từ cài đặt
        $temperature = floatval(get_option('ntq_ai_connector_temperature', 0.3));
        $max_tokens = intval(get_option('ntq_ai_connector_max_tokens', 2000));
        
        // Đảm bảo các thông số trong phạm vi hợp lệ
        $temperature = max(0, min(1, $temperature)); // Giới hạn từ 0 đến 1
        $max_tokens = max(100, min(10000, $max_tokens)); // Giới hạn từ 100 đến 10000
        
        // Chuẩn bị dữ liệu gửi đến API
        $data = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'Bạn là trợ lý AI chuyên nghiệp giúp tóm tắt nội dung bài viết. Hãy tạo bản tóm tắt ngắn gọn, súc tích và đầy đủ các ý chính.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
        );

        // Ghi nhận thời gian bắt đầu request
        $start_time = microtime( true );

        // Gửi yêu cầu đến API
        $response = $this->make_api_request( $data );

        // Ghi nhận thời gian kết thúc request
        $end_time = microtime( true );
        $response_time = round( ( $end_time - $start_time ) * 1000 ); // Convert to milliseconds

        // Xử lý kết quả
        if ( is_wp_error( $response ) ) {
            return $response;
        }        // Lưu log với kết quả tóm tắt
        if ( $post_id > 0 ) {
            $summary_text = isset($response['summary']) ? $response['summary'] : '';
            $this->log_request( $post_id, $model, $start_time, $response_time, $summary_text );
        }

        // Cập nhật số lượng request trong ngày
        $this->increment_daily_requests();

        return $response;
    }

    /**
     * Kiểm tra giới hạn số lượng request/ngày
     * 
     * @return bool True nếu chưa đạt giới hạn
     */
    private function check_daily_limit() {
        $daily_limit = intval( get_option( 'ntq_ai_connector_daily_limit', 50 ) );
        $today_requests = intval( get_option( 'ntq_ai_connector_today_requests', 0 ) );
        $last_reset_date = get_option( 'ntq_ai_connector_last_reset_date', '' );
        
        // Kiểm tra ngày cuối cùng reset counter
        $today = date( 'Y-m-d' );
        if ( $last_reset_date !== $today ) {
            update_option( 'ntq_ai_connector_today_requests', 0 );
            update_option( 'ntq_ai_connector_last_reset_date', $today );
            return true;
        }
        
        return ( $today_requests < $daily_limit );
    }

    /**
     * Tăng số lượng request trong ngày
     */
    private function increment_daily_requests() {
        $today_requests = intval( get_option( 'ntq_ai_connector_today_requests', 0 ) );
        update_option( 'ntq_ai_connector_today_requests', $today_requests + 1 );
    }    /**
     * Tạo prompt cho tóm tắt
     * 
     * @param string $title Tiêu đề bài viết
     * @param string $content Nội dung bài viết
     * @return string Prompt để gửi đến API
     */    private function get_summarize_prompt( $title, $content ) {
        $default_prompt = "Hãy tóm tắt bài viết sau một cách ngắn gọn và đầy đủ ý chính:
        
Tiêu đề: {title}

Nội dung:
{content}

Yêu cầu:
1. Tóm tắt phải bằng tiếng Việt
2. Tóm tắt phải ngắn gọn, rõ ràng, nhưng vẫn giữ được các ý chính
3. Giữ nguyên các thông tin quan trọng như số liệu, dữ liệu
4. Tổ chức nội dung thành các đoạn ngắn để dễ đọc
5. Sử dụng cú pháp Markdown để định dạng văn bản:
   - Sử dụng **từ khóa** hoặc __từ khóa__ cho văn bản in đậm
   - Sử dụng *văn bản* hoặc _văn bản_ cho văn bản in nghiêng
   - Sử dụng # cho tiêu đề lớn, ## cho tiêu đề nhỏ hơn
   - Sử dụng - hoặc * cho danh sách
   - Sử dụng > cho trích dẫn
6. Không thêm thông tin không có trong bài viết gốc";
        
        $custom_prompt = get_option( 'ntq_ai_connector_custom_prompt', $default_prompt );
        
        // Thay thế placeholders với nội dung thực tế
        $prompt = str_replace( '{title}', $title, $custom_prompt );
        $prompt = str_replace( '{content}', $content, $prompt );
        
        return $prompt;
    }

    /**
     * Gửi yêu cầu đến API
     * 
     * @param array $data Dữ liệu gửi đến API
     * @return array|WP_Error Kết quả từ API hoặc lỗi
     */
    private function make_api_request( $data ) {
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'HTTP-Referer' => home_url(),
        );

        $args = array(
            'headers' => $headers,
            'body'    => json_encode( $data ),
            'timeout' => 60,
            'method'  => 'POST',
        );

        // Gửi yêu cầu đến API
        $response = wp_remote_post( $this->api_endpoint, $args );

        // Kiểm tra lỗi
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        // Lấy body từ response
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );

        // Kiểm tra status code
        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code !== 200 ) {
            $error_message = isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'Lỗi không xác định từ API', 'ntq-ai-connector' );
            return new WP_Error( 'api_error', $error_message );
        }

        // Kiểm tra kết quả
        if ( ! isset( $result['choices'][0]['message']['content'] ) ) {
            return new WP_Error( 'api_error', __( 'Không nhận được dữ liệu từ API', 'ntq-ai-connector' ) );
        }

        return array(
            'summary' => $result['choices'][0]['message']['content'],
            'model' => $result['model'],
            'usage' => isset( $result['usage'] ) ? $result['usage'] : array(),
        );
    }    /**
     * Ghi log request vào database
     * 
     * @param int $post_id ID của bài viết
     * @param string $model Model AI sử dụng
     * @param int $request_time Thời gian bắt đầu request
     * @param int $response_time Thời gian phản hồi (ms)
     * @param string $summary Kết quả tóm tắt
     */
    private function log_request( $post_id, $model, $request_time, $response_time, $summary = '' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ntq_ai_logs';
        
        $user_id = get_current_user_id();
        
        $wpdb->insert(
            $table_name,
            array(
                'post_id'       => $post_id,
                'model_used'    => $model,
                'request_time'  => gmdate( 'Y-m-d H:i:s', $request_time ),
                'response_time' => $response_time,
                'user_id'       => $user_id,
                'summary_text'  => $summary,
                'created_at'    => current_time( 'mysql', true ),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
            )
        );
    }
}
