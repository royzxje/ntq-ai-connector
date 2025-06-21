<?php
/**
 * Class quản lý phần admin của plugin
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin {
    /**
     * Constructor
     */
    public function __construct() {
        // Thêm menu trong admin
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Đăng ký settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Thêm link Settings vào danh sách plugin
        add_filter( 'plugin_action_links_' . NTQ_AI_CONNECTOR_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
        
        // Enqueue admin scripts và styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // AJAX handler để kiểm tra API key
        add_action( 'wp_ajax_ntq_ai_test_api_key', array( $this, 'ajax_test_api_key' ) );
    }

    /**
     * Thêm menu trong admin
     */
    public function add_admin_menu() {
        // Parent menu
        add_menu_page(
            __( 'NTQ AI Connector', 'ntq-ai-connector' ),
            __( 'NTQ AI Connector', 'ntq-ai-connector' ),
            'manage_options',
            'ntq-ai-connector',
            array( $this, 'render_dashboard_page' ),
            'dashicons-chart-area',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'ntq-ai-connector',
            __( 'Dashboard', 'ntq-ai-connector' ),
            __( 'Dashboard', 'ntq-ai-connector' ),
            'manage_options',
            'ntq-ai-connector',
            array( $this, 'render_dashboard_page' )
        );

        // Settings submenu
        add_submenu_page(
            'ntq-ai-connector',
            __( 'Cài đặt', 'ntq-ai-connector' ),
            __( 'Cài đặt', 'ntq-ai-connector' ),
            'manage_options',
            'ntq-ai-connector-settings',
            array( $this, 'render_settings_page' )
        );
        
        // Logs submenu
        add_submenu_page(
            'ntq-ai-connector',
            __( 'Lịch sử', 'ntq-ai-connector' ),
            __( 'Lịch sử', 'ntq-ai-connector' ),
            'manage_options',
            'ntq-ai-connector-logs',
            array( $this, 'render_logs_page' )
        );
    }

    /**
     * Đăng ký các cài đặt
     */    public function register_settings() {
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_api_key' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_default_model' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_widget_position' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_widget_header_text' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_widget_footer_text' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_summarize_button_text' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_custom_prompt' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_gradient_start' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_gradient_end' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_enable_animations' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_daily_limit' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_max_tokens' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_temperature' );
        register_setting( 'ntq_ai_connector_settings', 'ntq_ai_connector_enable_widget' );
        
        // API Settings section
        add_settings_section(
            'ntq_ai_connector_api_settings',
            __( 'Cài đặt API', 'ntq-ai-connector' ),
            array( $this, 'api_settings_section_callback' ),
            'ntq_ai_connector_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_api_key',
            __( 'API Key', 'ntq-ai-connector' ),
            array( $this, 'api_key_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_api_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_default_model',
            __( 'Model mặc định', 'ntq-ai-connector' ),
            array( $this, 'default_model_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_api_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_daily_limit',
            __( 'Giới hạn request/ngày', 'ntq-ai-connector' ),
            array( $this, 'daily_limit_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_api_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_max_tokens',
            __( 'Token tối đa (Max Tokens)', 'ntq-ai-connector' ),
            array( $this, 'max_tokens_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_api_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_temperature',
            __( 'Nhiệt độ (Temperature)', 'ntq-ai-connector' ),
            array( $this, 'temperature_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_api_settings'
        );
        
        // Widget Settings section
        add_settings_section(
            'ntq_ai_connector_widget_settings',
            __( 'Cài đặt Widget', 'ntq-ai-connector' ),
            array( $this, 'widget_settings_section_callback' ),
            'ntq_ai_connector_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_widget_position',
            __( 'Vị trí Widget', 'ntq-ai-connector' ),
            array( $this, 'widget_position_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_widget_header_text',
            __( 'Tiêu đề widget', 'ntq-ai-connector' ),
            array( $this, 'widget_header_text_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_widget_footer_text',
            __( 'Footer widget', 'ntq-ai-connector' ),
            array( $this, 'widget_footer_text_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_summarize_button_text',
            __( 'Text nút tóm tắt', 'ntq-ai-connector' ),
            array( $this, 'summarize_button_text_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_gradient_start',
            __( 'Màu gradient (bắt đầu)', 'ntq-ai-connector' ),
            array( $this, 'gradient_start_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_gradient_end',
            __( 'Màu gradient (kết thúc)', 'ntq-ai-connector' ),
            array( $this, 'gradient_end_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_enable_animations',
            __( 'Bật hiệu ứng animation', 'ntq-ai-connector' ),
            array( $this, 'enable_animations_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_enable_widget',
            __( 'Hiển thị Widget', 'ntq-ai-connector' ),
            array( $this, 'enable_widget_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_widget_settings'
        );
        
        // Prompt Settings section
        add_settings_section(
            'ntq_ai_connector_prompt_settings',
            __( 'Tùy chỉnh Prompt', 'ntq-ai-connector' ),
            array( $this, 'prompt_settings_section_callback' ),
            'ntq_ai_connector_settings'
        );
        
        add_settings_field(
            'ntq_ai_connector_custom_prompt',
            __( 'Prompt tùy chỉnh', 'ntq-ai-connector' ),
            array( $this, 'custom_prompt_callback' ),
            'ntq_ai_connector_settings',
            'ntq_ai_connector_prompt_settings'
        );
    }

    /**
     * Callback cho section API Settings
     */
    public function api_settings_section_callback() {
        echo '<p>' . __( 'Cấu hình kết nối với OpenRouter API', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho section Widget Settings
     */
    public function widget_settings_section_callback() {
        echo '<p>' . __( 'Cấu hình giao diện và vị trí widget', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho section Prompt Settings
     */
    public function prompt_settings_section_callback() {
        echo '<p>' . __( 'Tùy chỉnh prompt gửi đến AI để tối ưu kết quả tóm tắt', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho field API Key
     */
    public function api_key_callback() {
        $api_key = get_option( 'ntq_ai_connector_api_key', '' );
        echo '<input type="password" id="ntq_ai_connector_api_key" name="ntq_ai_connector_api_key" value="' . esc_attr( $api_key ) . '" class="regular-text" />';
        echo '<button type="button" id="ntq_ai_connector_test_api_key" class="button button-secondary">' . __( 'Kiểm tra API Key', 'ntq-ai-connector' ) . '</button>';
        echo '<p class="description">' . __( 'Nhập API Key từ OpenRouter. <a href="https://openrouter.ai/keys" target="_blank">Lấy API Key</a>', 'ntq-ai-connector' ) . '</p>';
        echo '<div id="ntq_ai_connector_api_key_status"></div>';
    }
    
    /**
     * Callback cho field Default Model
     */
    public function default_model_callback() {
        $default_model = get_option( 'ntq_ai_connector_default_model', 'deepseek/deepseek-chat-v3-0324:free' );
        
        $models = array(
            'deepseek/deepseek-chat-v3-0324:free' => 'DeepSeek Chat v3 (Tóm tắt nhanh)',
            'deepseek/deepseek-r1-0528:free' => 'DeepSeek R1 (Phân tích sâu)',
        );
        
        echo '<select id="ntq_ai_connector_default_model" name="ntq_ai_connector_default_model">';
        foreach ( $models as $model_id => $model_name ) {
            echo '<option value="' . esc_attr( $model_id ) . '" ' . selected( $default_model, $model_id, false ) . '>' . esc_html( $model_name ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __( 'Model mặc định sử dụng khi tóm tắt nội dung', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho field Daily Limit
     */
    public function daily_limit_callback() {
        $daily_limit = get_option( 'ntq_ai_connector_daily_limit', 50 );
        echo '<input type="number" id="ntq_ai_connector_daily_limit" name="ntq_ai_connector_daily_limit" value="' . esc_attr( $daily_limit ) . '" min="1" max="1000" step="1" />';
        echo '<p class="description">' . __( 'Giới hạn số lượng request mỗi ngày', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho field Widget Position
     */
    public function widget_position_callback() {
        $position = get_option( 'ntq_ai_connector_widget_position', 'bottom-right' );
        
        $positions = array(
            'bottom-right' => __( 'Góc phải dưới', 'ntq-ai-connector' ),
            'bottom-left' => __( 'Góc trái dưới', 'ntq-ai-connector' ),
            'top-right' => __( 'Góc phải trên', 'ntq-ai-connector' ),
            'top-left' => __( 'Góc trái trên', 'ntq-ai-connector' ),
        );
        
        echo '<select id="ntq_ai_connector_widget_position" name="ntq_ai_connector_widget_position">';
        foreach ( $positions as $position_id => $position_name ) {
            echo '<option value="' . esc_attr( $position_id ) . '" ' . selected( $position, $position_id, false ) . '>' . esc_html( $position_name ) . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Callback cho field Gradient Start
     */
    public function gradient_start_callback() {
        $gradient_start = get_option( 'ntq_ai_connector_gradient_start', '#7C3AED' );
        echo '<input type="color" id="ntq_ai_connector_gradient_start" name="ntq_ai_connector_gradient_start" value="' . esc_attr( $gradient_start ) . '" />';
    }
    
    /**
     * Callback cho field Gradient End
     */
    public function gradient_end_callback() {
        $gradient_end = get_option( 'ntq_ai_connector_gradient_end', '#2563EB' );
        echo '<input type="color" id="ntq_ai_connector_gradient_end" name="ntq_ai_connector_gradient_end" value="' . esc_attr( $gradient_end ) . '" />';
        
        // Hiển thị preview gradient
        echo '<div id="gradient_preview" style="margin-top: 10px; height: 30px; border-radius: 5px;"></div>';
    }
    
    /**
     * Callback cho field Enable Animations
     */
    public function enable_animations_callback() {
        $enable_animations = get_option( 'ntq_ai_connector_enable_animations', 'yes' );
        echo '<label><input type="checkbox" id="ntq_ai_connector_enable_animations" name="ntq_ai_connector_enable_animations" value="yes" ' . checked( $enable_animations, 'yes', false ) . ' /> ' . __( 'Bật hiệu ứng animation', 'ntq-ai-connector' ) . '</label>';
    }

    /**
     * Callback cho field Custom Prompt
     */
    public function custom_prompt_callback() {
        $default_prompt = "Hãy tóm tắt bài viết sau một cách ngắn gọn và đầy đủ ý chính:
        
Tiêu đề: {title}

Nội dung:
{content}

Yêu cầu:
1. Tóm tắt phải bằng tiếng Việt
2. Tóm tắt phải ngắn gọn, rõ ràng, nhưng vẫn giữ được các ý chính
3. Giữ nguyên các thông tin quan trọng như số liệu, dữ liệu
4. Tổ chức nội dung thành các đoạn ngắn để dễ đọc
5. Không thêm thông tin không có trong bài viết gốc";
        
        $custom_prompt = get_option( 'ntq_ai_connector_custom_prompt', $default_prompt );
        
        echo '<textarea id="ntq_ai_connector_custom_prompt" name="ntq_ai_connector_custom_prompt" rows="12" cols="50" class="large-text code">' . esc_textarea( $custom_prompt ) . '</textarea>';
        echo '<p class="description">' . __( 'Tùy chỉnh prompt gửi đến AI. Sử dụng {title} và {content} để đại diện cho tiêu đề và nội dung bài viết.', 'ntq-ai-connector' ) . '</p>';
    }
    
    /**
     * Callback cho field Widget Header Text
     */
    public function widget_header_text_callback() {
        $header_text = get_option( 'ntq_ai_connector_widget_header_text', 'NTQ AI' );
        echo '<input type="text" id="ntq_ai_connector_widget_header_text" name="ntq_ai_connector_widget_header_text" value="' . esc_attr( $header_text ) . '" class="regular-text" />';
    }
    
    /**
     * Callback cho field Widget Footer Text
     */
    public function widget_footer_text_callback() {
        $footer_text = get_option( 'ntq_ai_connector_widget_footer_text', 'Powered by NTQ AI Connector' );
        echo '<input type="text" id="ntq_ai_connector_widget_footer_text" name="ntq_ai_connector_widget_footer_text" value="' . esc_attr( $footer_text ) . '" class="regular-text" />';
    }
    
    /**
     * Callback cho field Summarize Button Text
     */
    public function summarize_button_text_callback() {
        $button_text = get_option( 'ntq_ai_connector_summarize_button_text', 'Tóm tắt bài viết hiện tại' );
        echo '<input type="text" id="ntq_ai_connector_summarize_button_text" name="ntq_ai_connector_summarize_button_text" value="' . esc_attr( $button_text ) . '" class="regular-text" />';
    }

    /**
     * Thêm link Settings vào danh sách plugin
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=ntq-ai-connector-settings' ) . '">' . __( 'Settings', 'ntq-ai-connector' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }    /**
     * Render the dashboard page
     */
    public function render_dashboard_page() {
        // Lấy khoảng thời gian từ tham số URL
        $period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : 'week';
        
        // Chỉ cho phép các giá trị hợp lệ
        if ( ! in_array( $period, array( 'day', 'week', 'month', 'year' ) ) ) {
            $period = 'week'; // Mặc định là tuần
        }
        
        // Lấy dữ liệu thống kê từ database
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/class-database.php';
        $database = new Database();
        $stats = $database->get_stats( $period );
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $chart_labels = array();
        $chart_data = array();
        
        // Nếu có daily_data, chuẩn bị dữ liệu cho biểu đồ
        if (!empty($stats['daily_data'])) {
            foreach ($stats['daily_data'] as $data) {
                // Định dạng ngày để hiển thị
                $date = new DateTime($data->date);
                $chart_labels[] = $date->format('d/m');
                $chart_data[] = (int)$data->count;
            }
        } else {
            // Nếu không có dữ liệu, tạo biểu đồ trống với 7 ngày gần nhất
            $end_date = new DateTime();
            $start_date = new DateTime('-6 days');
            
            while ($start_date <= $end_date) {
                $chart_labels[] = $start_date->format('d/m');
                $chart_data[] = 0;
                $start_date->modify('+1 day');
            }
        }
        
        // JSON encode cho dữ liệu biểu đồ
        $chart_labels_json = json_encode($chart_labels);
        $chart_data_json = json_encode($chart_data);
        
        // Include dashboard view
        include_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        // Include settings view
        include_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'admin/views/settings.php';
    }    /**
     * Render the logs page
     */
    public function render_logs_page() {
        // Lấy dữ liệu logs từ database
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/class-database.php';
        $database = new Database();
        
        // Phân trang
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $per_page = 20; // Số logs trên một trang
        $offset = ( $current_page - 1 ) * $per_page;
        
        // Lấy danh sách logs
        $logs = $database->get_logs( $per_page, $offset );
        
        // Lấy tổng số logs cho phân trang
        $total_logs = $database->count_logs();
        $total_pages = ceil( $total_logs / $per_page );
        
        // Include logs view và truyền dữ liệu
        include_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'admin/views/logs.php';
    }

    /**
     * Enqueue admin scripts và styles
     */
    public function enqueue_admin_assets( $hook ) {
        // Chỉ load trong các trang của plugin
        if ( strpos( $hook, 'ntq-ai-connector' ) === false ) {
            return;
        }
        
        // CSS
        wp_enqueue_style( 
            'ntq-ai-connector-admin-style', 
            NTQ_AI_CONNECTOR_PLUGIN_URL . 'admin/css/admin.css', 
            array(), 
            NTQ_AI_CONNECTOR_VERSION 
        );
          // SweetAlert2
        wp_enqueue_script( 
            'sweetalert2', 
            'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js', 
            array(), 
            '11.0.18', 
            true 
        );
        
        // Marked.js - thư viện xử lý Markdown (chỉ load trên trang logs)
        if ( 'ntq-ai-connector_page_ntq-ai-connector-logs' === $hook ) {
            wp_enqueue_script(
                'marked-js',
                'https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js',
                array(),
                '4.3.0',
                true
            );
        }
          // Chart.js (chỉ load trên trang dashboard)
        if ( $hook === 'toplevel_page_ntq-ai-connector' ) {
            wp_enqueue_script( 
                'chartjs', 
                'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js', 
                array('jquery'), 
                '3.7.1', 
                true 
            );
        }
          // Admin script
        $deps = array('jquery', 'sweetalert2');
        if ($hook === 'toplevel_page_ntq-ai-connector') {
            $deps[] = 'chartjs';
        }
        
        wp_enqueue_script( 
            'ntq-ai-connector-admin-script', 
            NTQ_AI_CONNECTOR_PLUGIN_URL . 'admin/js/admin.js', 
            $deps, 
            NTQ_AI_CONNECTOR_VERSION, 
            true 
        );
        
        // Localize script
        wp_localize_script( 
            'ntq-ai-connector-admin-script', 
            'ntqAiConnector', 
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'ntq_ai_connector_nonce' ),
                'testing' => __( 'Đang kiểm tra...', 'ntq-ai-connector' ),
                'testSuccess' => __( 'API Key hợp lệ!', 'ntq-ai-connector' ),
                'testFailed' => __( 'API Key không hợp lệ hoặc có lỗi xảy ra.', 'ntq-ai-connector' ),
            )
        );
    }

    /**
     * AJAX handler để kiểm tra API key
     */
    public function ajax_test_api_key() {
        // Kiểm tra nonce
        check_ajax_referer( 'ntq_ai_connector_nonce', 'nonce' );
        
        // Kiểm tra quyền
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Bạn không có quyền thực hiện hành động này.', 'ntq-ai-connector' ) ) );
        }
        
        // Lấy API key từ request
        $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
        
        if ( empty( $api_key ) ) {
            wp_send_json_error( array( 'message' => __( 'API key không được để trống.', 'ntq-ai-connector' ) ) );
        }
        
        // Endpoint để kiểm tra API key (OpenRouter models endpoint)
        $api_endpoint = 'https://openrouter.ai/api/v1/models';
        
        // Gửi request
        $response = wp_remote_get( 
            $api_endpoint,
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'HTTP-Referer' => home_url(),
                )
            ) 
        );
        
        // Kiểm tra lỗi
        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }
        
        // Kiểm tra status code
        $status_code = wp_remote_retrieve_response_code( $response );
        
        if ( $status_code === 200 ) {
            wp_send_json_success( array( 'message' => __( 'API Key hợp lệ!', 'ntq-ai-connector' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'API Key không hợp lệ hoặc có lỗi xảy ra.', 'ntq-ai-connector' ) ) );
        }
    }
    
    /**
     * Callback cho field Max Tokens
     */
    public function max_tokens_callback() {
        $max_tokens = get_option( 'ntq_ai_connector_max_tokens', 2000 );
        ?>
        <input type="number" name="ntq_ai_connector_max_tokens" value="<?php echo intval( $max_tokens ); ?>" min="100" max="10000" step="100" />
        <p class="description">
            <?php _e( 'Số token tối đa cho mỗi lần tóm tắt. Giá trị lớn hơn cho phép tạo nội dung dài hơn nhưng sẽ tốn nhiều tokens hơn (giá trị từ 100 đến 10000).', 'ntq-ai-connector' ); ?>
        </p>
        <?php
    }
    
    /**
     * Callback cho field Temperature
     */
    public function temperature_callback() {
        $temperature = get_option( 'ntq_ai_connector_temperature', 0.3 );
        ?>
        <input type="number" name="ntq_ai_connector_temperature" value="<?php echo floatval( $temperature ); ?>" min="0" max="1" step="0.1" />
        <p class="description">
            <?php _e( 'Nhiệt độ ảnh hưởng đến tính ngẫu nhiên và sáng tạo của kết quả. Giá trị thấp (gần 0) sẽ cho kết quả nhất quán, giá trị cao (gần 1) sẽ cho kết quả sáng tạo hơn.', 'ntq-ai-connector' ); ?>
        </p>
        <?php
    }
    
    /**
     * Callback cho field Widget Enable
     */
    public function enable_widget_callback() {
        $enable_widget = get_option( 'ntq_ai_connector_enable_widget', 'yes' );
        ?>
        <select name="ntq_ai_connector_enable_widget">
            <option value="yes" <?php selected( $enable_widget, 'yes' ); ?>><?php _e( 'Hiển thị', 'ntq-ai-connector' ); ?></option>
            <option value="no" <?php selected( $enable_widget, 'no' ); ?>><?php _e( 'Ẩn', 'ntq-ai-connector' ); ?></option>
        </select>
        <p class="description">
            <?php _e( 'Cho phép hiển thị hoặc ẩn widget tóm tắt trên trang web.', 'ntq-ai-connector' ); ?>
        </p>
        <?php
    }
}
