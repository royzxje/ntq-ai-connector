<?php
/**
 * Class xử lý database cho plugin
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Database {
    /**
     * Tên của bảng logs
     * 
     * @var string
     */
    private $table_name;
      /**
     * Phiên bản schema
     * 
     * @var string
     */
    private $db_version = '1.1';

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ntq_ai_logs';
        
        // Kiểm tra và nâng cấp bảng nếu cần
        $this->maybe_upgrade();
    }

    /**
     * Tạo bảng trong database
     */    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            model_used varchar(255) NOT NULL,
            request_time datetime NOT NULL,
            response_time int(11) NOT NULL,
            user_id bigint(20) NOT NULL,
            summary_text LONGTEXT,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY model_used (model_used),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( 'ntq_ai_connector_db_version', $this->db_version );
    }
    
    /**
     * Kiểm tra và nâng cấp bảng nếu cần
     */
    private function maybe_upgrade() {
        $current_version = get_option( 'ntq_ai_connector_db_version', '1.0' );
        
        // Nếu version đã cập nhật, không cần làm gì
        if ( version_compare( $current_version, $this->db_version, '>=' ) ) {
            return;
        }
        
        global $wpdb;
        
        // Nâng cấp từ 1.0 lên 1.1
        if ( version_compare( $current_version, '1.1', '<' ) ) {
            // Thêm cột lưu kết quả tóm tắt
            $wpdb->query( "ALTER TABLE {$this->table_name} ADD COLUMN summary_text LONGTEXT AFTER user_id" );
        }
        
        // Cập nhật version
        update_option( 'ntq_ai_connector_db_version', $this->db_version );
    }

    /**
     * Lấy dữ liệu thống kê từ logs
     * 
     * @param string $period Khoảng thời gian ('day', 'week', 'month', 'year')
     * @return array Dữ liệu thống kê
     */
    public function get_stats( $period = 'week' ) {
        global $wpdb;
        
        // Xác định thời gian bắt đầu dựa trên period
        $start_date = new DateTime();
        
        switch ( $period ) {
            case 'day':
                $start_date->modify( '-1 day' );
                break;
            case 'week':
                $start_date->modify( '-1 week' );
                break;
            case 'month':
                $start_date->modify( '-1 month' );
                break;
            case 'year':
                $start_date->modify( '-1 year' );
                break;
            default:
                $start_date->modify( '-1 week' );
                break;
        }
        
        $stats = array();
        
        // Tổng số request
        $stats['total_requests'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $this->table_name WHERE created_at >= %s",
                $start_date->format( 'Y-m-d H:i:s' )
            )
        );
        
        // Model được dùng nhiều nhất
        $stats['top_model'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT model_used FROM $this->table_name WHERE created_at >= %s GROUP BY model_used ORDER BY COUNT(*) DESC LIMIT 1",
                $start_date->format( 'Y-m-d H:i:s' )
            )
        );
        
        // Thời gian phản hồi trung bình
        $stats['avg_response_time'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT AVG(response_time) FROM $this->table_name WHERE created_at >= %s",
                $start_date->format( 'Y-m-d H:i:s' )
            )
        );
        
        // Top 10 bài viết được tóm tắt nhiều nhất
        $stats['top_posts'] = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id, COUNT(*) as count FROM $this->table_name WHERE created_at >= %s GROUP BY post_id ORDER BY count DESC LIMIT 10",
                $start_date->format( 'Y-m-d H:i:s' )
            )
        );
        
        // Dữ liệu cho biểu đồ theo ngày
        $stats['daily_data'] = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE(created_at) as date, COUNT(*) as count FROM $this->table_name WHERE created_at >= %s GROUP BY DATE(created_at) ORDER BY date ASC",
                $start_date->format( 'Y-m-d H:i:s' )
            )
        );
        
        return $stats;
    }
    
    /**
     * Lấy logs gần đây
     * 
     * @param int $limit Số lượng logs tối đa
     * @return array Danh sách logs
     */
    public function get_recent_logs( $limit = 20 ) {
        global $wpdb;
        
        $logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name ORDER BY created_at DESC LIMIT %d",
                $limit
            )
        );
        
        return $logs;
    }

    /**
     * Đếm tổng số logs trong database
     * 
     * @return int Tổng số logs
     */
    public function count_logs() {
        global $wpdb;
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
    }
    
    /**
     * Lấy danh sách logs từ database
     * 
     * @param int $limit Số lượng logs tối đa
     * @param int $offset Bắt đầu từ vị trí
     * @return array Danh sách logs
     */
    public function get_logs( $limit = 100, $offset = 0 ) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );
        
        return $wpdb->get_results( $sql );
    }
}
