<?php
/**
 * Plugin Name: NTQ AI Connector
 * Plugin URI: https://q2k1.com
 * Description: Kết nối WordPress với OpenRouter API để tóm tắt bài viết và phân tích dữ liệu
 * Version: 1.2.0
 * Requires PHP: 7.4
 * Author: ntquan
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ntq-ai-connector
 * Domain Path: /languages
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Định nghĩa các hằng số cho plugin
 */
define( 'NTQ_AI_CONNECTOR_VERSION', '1.2.0' );
define( 'NTQ_AI_CONNECTOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NTQ_AI_CONNECTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NTQ_AI_CONNECTOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Class chính của plugin
 */
class NTQ_AI_Connector {

    /**
     * Instance của class (Singleton pattern)
     *
     * @var NTQ_AI_Connector
     */
    private static $instance = null;

    /**
     * Lấy instance của class
     *
     * @return NTQ_AI_Connector
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Load các file cần thiết
        $this->load_dependencies();
        
        // Khởi tạo hooks
        $this->init_hooks();
    }

    /**
     * Load các file phụ thuộc
     */
    private function load_dependencies() {
        // API Handler
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/api/class-api-handler.php';
        
        // Admin
        if ( is_admin() ) {
            require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'admin/class-admin.php';
        }
        
        // Public/Frontend
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'public/class-public.php';
        
        // Database
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/class-database.php';
    }

    /**
     * Đăng ký các hook
     */
    private function init_hooks() {
        // Hook kích hoạt plugin
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        
        // Hook hủy kích hoạt plugin
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        
        // Hook gỡ bỏ plugin
        register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
        
        // Khởi tạo các thành phần
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Kích hoạt plugin
     */
    public function activate() {
        // Tạo bảng database nếu cần
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'includes/class-database.php';
        $database = new Database();
        $current = get_option( 'ntq_ai_connector_db_version' );
        if ( $current !== $database->get_db_version() ) {
            $database->create_tables();
        }
        
        // Thêm các tùy chọn mặc định
        $this->add_default_options();
        
        // Xóa cache
        flush_rewrite_rules();
    }    /**
     * Thêm các tùy chọn mặc định khi kích hoạt plugin
     */    private function add_default_options() {
        $defaults = array(
            'api_key' => '',
            'default_model' => 'deepseek/deepseek-chat-v3-0324:free',
            'widget_position' => 'bottom-right',
            'gradient_start' => '#7C3AED',
            'gradient_end' => '#2563EB',
            'enable_animations' => 'yes',
            'daily_limit' => 50,
            'max_tokens' => 2000,
            'temperature' => 0.3,
            'widget_header_text' => 'NTQ AI',
            'widget_footer_text' => 'Powered by NTQ AI Connector',
            'summarize_button_text' => 'Tóm tắt bài viết hiện tại',
            'custom_prompt' => "Hãy tóm tắt bài viết sau một cách ngắn gọn và đầy đủ ý chính:\n\nTiêu đề: {title}\n\nNội dung:\n{content}\n\nYêu cầu:\n1. Tóm tắt phải bằng tiếng Việt\n2. Tóm tắt phải ngắn gọn, rõ ràng, nhưng vẫn giữ được các ý chính\n3. Giữ nguyên các thông tin quan trọng như số liệu, dữ liệu\n4. Tổ chức nội dung thành các đoạn ngắn để dễ đọc\n5. Sử dụng cú pháp Markdown để định dạng văn bản:\n   - Sử dụng **từ khóa** hoặc __từ khóa__ cho văn bản in đậm\n   - Sử dụng *văn bản* hoặc _văn bản_ cho văn bản in nghiêng\n   - Sử dụng # cho tiêu đề lớn, ## cho tiêu đề nhỏ hơn\n   - Sử dụng - hoặc * cho danh sách\n   - Sử dụng > cho trích dẫn\n6. Không thêm thông tin không có trong bài viết gốc",
            'enable_widget' => 'yes',
            'load_js_local' => 'no',
        );
        
        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( 'ntq_ai_connector_' . $key ) ) {
                update_option( 'ntq_ai_connector_' . $key, $value );
            }
        }
    }

    /**
     * Hủy kích hoạt plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }    /**
     * Gỡ bỏ plugin
     */
    public static function uninstall() {
        // Xóa các tùy chọn
        $options = array(
            'api_key',
            'default_model',
            'widget_position',
            'gradient_start',
            'gradient_end',
            'enable_animations',
            'daily_limit',
            'max_tokens',
            'temperature',
            'widget_header_text',
            'widget_footer_text',
            'summarize_button_text',
            'custom_prompt',
            'enable_widget',
            'load_js_local',
        );
        
        foreach ( $options as $option ) {
            delete_option( 'ntq_ai_connector_' . $option );
        }
        
        // Xóa bảng trong database
        global $wpdb;
        $table_name = $wpdb->prefix . 'ntq_ai_logs';
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    }

    /**
     * Khởi tạo các thành phần của plugin
     */
    public function init() {
        // Tải textdomain
        load_plugin_textdomain( 'ntq-ai-connector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        
        // Khởi tạo Admin
        if ( is_admin() ) {
            require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'admin/class-admin.php';
            new Admin();
        }
        
        // Khởi tạo Public
        require_once NTQ_AI_CONNECTOR_PLUGIN_DIR . 'public/class-public.php';
        new NTQ_Public();
    }
}

// Khởi chạy plugin
function run_ntq_ai_connector() {
    return NTQ_AI_Connector::get_instance();
}

run_ntq_ai_connector();
