<?php
/**
 * Class xử lý phần public/frontend của plugin
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NTQ_Public {
    /**
     * Constructor
     */
    public function __construct() {
        // Đăng ký scripts và styles cho frontend
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // Thêm widget vào footer
        add_action( 'wp_footer', array( $this, 'add_widget' ) );
        
        // AJAX handler để tóm tắt bài viết
        add_action( 'wp_ajax_ntq_ai_summarize_post', array( $this, 'ajax_summarize_post' ) );
        add_action( 'wp_ajax_nopriv_ntq_ai_summarize_post', array( $this, 'ajax_summarize_post' ) );
    }

    /**
     * Đăng ký scripts và styles cho frontend
     */
    public function enqueue_assets() {
        // Chỉ load khi đang xem bài viết hoặc trang
        if ( ! is_singular() ) {
            return;
        }
        
        $load_local = get_option( 'ntq_ai_connector_load_js_local', 'no' ) === 'yes';

        // Animate.css
        wp_enqueue_style(
            'animate-css',
            'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
            array(),
            '4.1.1'
        );
        
        // SweetAlert2
        $sweetalert_src = $load_local ? NTQ_AI_CONNECTOR_PLUGIN_URL . 'assets/js/sweetalert2.all.min.js' : 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js';
        wp_enqueue_script( 'sweetalert2', $sweetalert_src, array(), '11.0.18', true );
        
        // Marked.js - thư viện xử lý Markdown
        $marked_src = $load_local ? NTQ_AI_CONNECTOR_PLUGIN_URL . 'assets/js/marked.min.js' : 'https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js';
        wp_enqueue_script( 'marked-js', $marked_src, array(), '4.3.0', true );
        
        // Heroicons (sprite)
        wp_enqueue_style(
            'ntq-ai-heroicons',
            NTQ_AI_CONNECTOR_PLUGIN_URL . 'assets/css/heroicons.css',
            array(),
            NTQ_AI_CONNECTOR_VERSION
        );
        
        // Plugin CSS
        wp_enqueue_style(
            'ntq-ai-public-style',
            NTQ_AI_CONNECTOR_PLUGIN_URL . 'public/css/public.css',
            array( 'animate-css' ),
            NTQ_AI_CONNECTOR_VERSION
        );
        
        // Plugin JS
        wp_enqueue_script(
            'ntq-ai-public-script',
            NTQ_AI_CONNECTOR_PLUGIN_URL . 'public/js/public.js',
            array( 'jquery', 'sweetalert2' ),
            NTQ_AI_CONNECTOR_VERSION,
            true
        );
        
        // Thêm custom CSS để áp dụng gradient từ cài đặt
        $gradient_start = get_option( 'ntq_ai_connector_gradient_start', '#7C3AED' );
        $gradient_end = get_option( 'ntq_ai_connector_gradient_end', '#2563EB' );
        
        $custom_css = "
            .ntq-ai-widget-button {
                background: linear-gradient(135deg, {$gradient_start}, {$gradient_end});
            }
            .ntq-ai-modal-header {
                background: linear-gradient(135deg, {$gradient_start}, {$gradient_end});
            }
        ";
        
        wp_add_inline_style( 'ntq-ai-public-style', $custom_css );
        
        // Localize script
        wp_localize_script(
            'ntq-ai-public-script',
            'ntqAiConnector',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'ntq_ai_connector_nonce' ),
                'postId' => get_the_ID(),
                'loadingText' => __( 'Đang tóm tắt...', 'ntq-ai-connector' ),
                'summaryTitle' => __( 'Tóm tắt bài viết', 'ntq-ai-connector' ),
                'errorTitle' => __( 'Lỗi', 'ntq-ai-connector' ),
                'closeText' => __( 'Đóng', 'ntq-ai-connector' ),
                'animations' => get_option( 'ntq_ai_connector_enable_animations', 'yes' ) === 'yes',
                'position' => get_option( 'ntq_ai_connector_widget_position', 'bottom-right' ),
            )
        );
    }
      /**
     * Thêm widget vào footer
     */
    public function add_widget() {
        // Chỉ hiện trên bài viết và trang
        if ( ! is_singular() ) {
            return;
        }
        
        // Kiểm tra API key
        $api_key = get_option( 'ntq_ai_connector_api_key', '' );
        if ( empty( $api_key ) ) {
            return;
        }
        
        // Kiểm tra nếu widget đã bị tắt
        $enable_widget = get_option( 'ntq_ai_connector_enable_widget', 'yes' );
        if ( $enable_widget === 'no' ) {
            return;
        }
        
        include NTQ_AI_CONNECTOR_PLUGIN_DIR . 'public/views/widget.php';
    }
    
    /**
     * AJAX handler để tóm tắt bài viết
     */
    public function ajax_summarize_post() {
        // Kiểm tra nonce
        check_ajax_referer( 'ntq_ai_connector_nonce', 'nonce' );
        
        // Lấy post ID
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        
        if ( $post_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'ID bài viết không hợp lệ.', 'ntq-ai-connector' ) ) );
        }
        
        // Lấy thông tin bài viết
        $post = get_post( $post_id );
        
        if ( empty( $post ) ) {
            wp_send_json_error( array( 'message' => __( 'Không tìm thấy bài viết.', 'ntq-ai-connector' ) ) );
        }
        
        // Lấy nội dung bài viết
        $title = get_the_title( $post_id );
        $content = apply_filters( 'the_content', $post->post_content );
        
        // Model AI (nếu có)
        $model = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : '';
        
        // Gọi API để tóm tắt
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/api/class-api-handler.php';
        $api_handler = new API_Handler();
        $result = $api_handler->summarize_content( $title, $content, $model, $post_id );
        
        // Kiểm tra lỗi
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }
        
        // Trả về kết quả
        wp_send_json_success( array(
            'summary' => $result['summary'],
            'model' => $result['model'],
        ) );
    }
}
