<?php
/**
 * Template cho trang Dashboard
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ntq-ai-dashboard">
    <h1><?php _e( 'NTQ AI Connector - Dashboard', 'ntq-ai-connector' ); ?></h1>
    
    <div class="ntq-ai-dashboard-header">
        <div class="ntq-ai-welcome">
            <h2><?php _e( 'Xin chào!', 'ntq-ai-connector' ); ?></h2>
            <p>
                <?php _e( 'Chào mừng bạn đến với NTQ AI Connector Dashboard. Tại đây bạn có thể theo dõi thống kê sử dụng AI trong trang web của bạn.', 'ntq-ai-connector' ); ?>
            </p>
        </div>
        <div class="ntq-ai-status">
            <h3><?php _e( 'Trạng thái', 'ntq-ai-connector' ); ?></h3>
            <?php 
            $api_key = get_option( 'ntq_ai_connector_api_key', '' );
            if ( empty( $api_key ) ) : 
            ?>
                <div class="ntq-ai-status-warning">
                    <span class="dashicons dashicons-warning"></span>
                    <?php _e( 'Bạn chưa cấu hình API key!', 'ntq-ai-connector' ); ?>
                    <a href="<?php echo admin_url( 'admin.php?page=ntq-ai-connector-settings' ); ?>"><?php _e( 'Cấu hình ngay', 'ntq-ai-connector' ); ?></a>
                </div>
            <?php else : ?>                <div class="ntq-ai-status-ok">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e( 'Plugin đang hoạt động!', 'ntq-ai-connector' ); ?>
                </div>                <div class="ntq-ai-status-info">
                    <?php if ( get_option( 'ntq_ai_connector_enable_widget', 'yes' ) === 'yes' ) : ?>
                        <p><?php _e( 'Widget tóm tắt đang được hiển thị trên các trang bài viết của bạn.', 'ntq-ai-connector' ); ?></p>
                    <?php else : ?>
                        <p><?php _e( 'Widget tóm tắt hiện đang bị tắt.', 'ntq-ai-connector' ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo admin_url( 'admin.php?page=ntq-ai-connector-settings' ); ?>"><?php _e( 'Tùy chỉnh widget', 'ntq-ai-connector' ); ?></a>
                </div>
            <?php endif; ?>
              <div class="ntq-ai-daily-usage">
                <?php 
                $daily_limit = get_option( 'ntq_ai_connector_daily_limit', 50 );
                $today_requests = get_option( 'ntq_ai_connector_today_requests', 0 );
                $percentage = ( $daily_limit > 0 ) ? round( ( $today_requests / $daily_limit ) * 100 ) : 0;
                ?>
                <h4><?php _e( 'Sử dụng hôm nay', 'ntq-ai-connector' ); ?></h4>
                <div class="ntq-ai-progress-bar">
                    <div class="ntq-ai-progress" style="width: <?php echo $percentage; ?>%;"></div>
                </div>
                <div class="ntq-ai-progress-text">
                    <?php echo sprintf( __( '%d / %d requests (%d%%)', 'ntq-ai-connector' ), $today_requests, $daily_limit, $percentage ); ?>
                </div>
            </div>
            
            <div class="ntq-ai-current-settings">
                <h4><?php _e( 'Cài đặt hiện tại', 'ntq-ai-connector' ); ?></h4>
                <ul>
                    <li>
                        <strong><?php _e( 'Max Tokens:', 'ntq-ai-connector' ); ?></strong> 
                        <?php echo get_option( 'ntq_ai_connector_max_tokens', 2000 ); ?>
                    </li>
                    <li>
                        <strong><?php _e( 'Temperature:', 'ntq-ai-connector' ); ?></strong> 
                        <?php echo get_option( 'ntq_ai_connector_temperature', 0.3 ); ?>
                    </li>
                    <li>
                        <strong><?php _e( 'Widget:', 'ntq-ai-connector' ); ?></strong> 
                        <?php echo get_option( 'ntq_ai_connector_enable_widget', 'yes' ) === 'yes' ? __( 'Đang bật', 'ntq-ai-connector' ) : __( 'Đang tắt', 'ntq-ai-connector' ); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="ntq-ai-stats-filter">
        <form method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
            <input type="hidden" name="page" value="ntq-ai-connector">
            <label for="period"><?php _e( 'Hiển thị thống kê theo:', 'ntq-ai-connector' ); ?></label>
            <select name="period" id="period" onchange="this.form.submit()">
                <option value="day" <?php selected( $period, 'day' ); ?>><?php _e( 'Ngày (24h qua)', 'ntq-ai-connector' ); ?></option>
                <option value="week" <?php selected( $period, 'week' ); ?>><?php _e( 'Tuần (7 ngày qua)', 'ntq-ai-connector' ); ?></option>
                <option value="month" <?php selected( $period, 'month' ); ?>><?php _e( 'Tháng (30 ngày qua)', 'ntq-ai-connector' ); ?></option>
                <option value="year" <?php selected( $period, 'year' ); ?>><?php _e( 'Năm (365 ngày qua)', 'ntq-ai-connector' ); ?></option>
            </select>
        </form>
    </div>
    
    <div class="ntq-ai-dashboard-stats">
        <div class="ntq-ai-stats-row">            <div class="ntq-ai-stats-box">
                <h3><?php _e( 'Tổng số request', 'ntq-ai-connector' ); ?></h3>
                <div class="stats-number"><?php echo isset( $stats['total_requests'] ) ? intval( $stats['total_requests'] ) : 0; ?></div>
                <p>
                    <?php 
                    switch ( $period ) {
                        case 'day':
                            _e( 'Trong 24 giờ qua', 'ntq-ai-connector' );
                            break;
                        case 'week':
                            _e( 'Trong 7 ngày qua', 'ntq-ai-connector' );
                            break;
                        case 'month':
                            _e( 'Trong 30 ngày qua', 'ntq-ai-connector' );
                            break;
                        case 'year':
                            _e( 'Trong 365 ngày qua', 'ntq-ai-connector' );
                            break;
                    }
                    ?>
                </p>
            </div>
            
            <div class="ntq-ai-stats-box">
                <h3><?php _e( 'Model được dùng nhiều nhất', 'ntq-ai-connector' ); ?></h3>
                <div class="stats-text">
                    <?php 
                    if ( ! empty( $stats['top_model'] ) ) {
                        $model_display_name = $stats['top_model'];
                        if ( $model_display_name === 'deepseek/deepseek-chat-v3-0324:free' ) {
                            echo 'DeepSeek Chat v3';
                        } elseif ( $model_display_name === 'deepseek/deepseek-r1-0528:free' ) {
                            echo 'DeepSeek R1';
                        } else {
                            echo esc_html( $model_display_name );
                        }
                    } else {
                        _e( 'Chưa có dữ liệu', 'ntq-ai-connector' );
                    }
                    ?>
                </div>
            </div>
            
            <div class="ntq-ai-stats-box">
                <h3><?php _e( 'Thời gian phản hồi trung bình', 'ntq-ai-connector' ); ?></h3>
                <div class="stats-number">
                    <?php 
                    if ( ! empty( $stats['avg_response_time'] ) ) {
                        echo round( $stats['avg_response_time'] ) . ' ms';
                    } else {
                        _e( 'Chưa có dữ liệu', 'ntq-ai-connector' );
                    }
                    ?>
                </div>
            </div>
        </div>
          <div class="ntq-ai-chart-container">
            <h3><?php _e( 'Thống kê sử dụng', 'ntq-ai-connector' ); ?></h3>
            <?php if (!empty($stats['daily_data'])) : ?>
                <div class="ntq-ai-chart-wrapper">
                    <canvas id="usageChart"></canvas>
                </div>
            <?php else : ?>
                <div class="ntq-ai-no-data">
                    <?php _e( 'Chưa có dữ liệu thống kê. Hãy sử dụng tính năng tóm tắt bài viết để xem biểu đồ thống kê tại đây.', 'ntq-ai-connector' ); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="ntq-ai-top-posts">
            <h3><?php _e( 'Top 10 bài viết được tóm tắt nhiều nhất', 'ntq-ai-connector' ); ?></h3>
            
            <?php if ( ! empty( $stats['top_posts'] ) ) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e( 'Bài viết', 'ntq-ai-connector' ); ?></th>
                            <th width="100"><?php _e( 'Số lần tóm tắt', 'ntq-ai-connector' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $stats['top_posts'] as $post_data ) : ?>
                            <tr>
                                <td>
                                    <?php 
                                    $post_title = get_the_title( $post_data->post_id );
                                    $post_title = ! empty( $post_title ) ? $post_title : __( '(Không có tiêu đề)', 'ntq-ai-connector' );
                                    $post_url = get_permalink( $post_data->post_id );
                                    ?>
                                    <a href="<?php echo esc_url( $post_url ); ?>" target="_blank"><?php echo esc_html( $post_title ); ?></a>
                                </td>
                                <td><?php echo intval( $post_data->count ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="ntq-ai-no-data">
                    <?php _e( 'Chưa có dữ liệu. Hãy sử dụng tính năng tóm tắt bài viết để xem thống kê tại đây.', 'ntq-ai-connector' ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Khởi tạo biểu đồ thống kê
jQuery(document).ready(function($) {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js không được tải');
        return;
    }
    
    // Kiểm tra phần tử canvas có tồn tại không
    var canvas = document.getElementById('usageChart');
    if (!canvas) {
        return;
    }
    
    var ctx = canvas.getContext('2d');
    
    var chartLabels = <?php echo $chart_labels_json; ?>;
    var chartData = <?php echo $chart_data_json; ?>;
    // Gradient cho chart
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(124, 58, 237, 0.5)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.2)');
    
    var myChart;
    try {
        myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: '<?php _e( 'Số request', 'ntq-ai-connector' ); ?>',
                data: chartData,
                backgroundColor: gradient,
                borderColor: '#7C3AED',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: '#2563EB',
                fill: true,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: '<?php _e( 'Số lượng yêu cầu tóm tắt theo ngày', 'ntq-ai-connector' ); ?>',
                    font: {
                        size: 16
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                }
            }
        }        });
    } catch (e) {
        console.error('Lỗi khi tạo biểu đồ:', e);
        // Hiển thị lỗi trên UI nếu cần
        $('#usageChart').parent().html('<div class="ntq-ai-no-data"><?php _e( 'Không thể tạo biểu đồ. Vui lòng thử lại sau.', 'ntq-ai-connector' ); ?></div>');
    }
});
</script>
