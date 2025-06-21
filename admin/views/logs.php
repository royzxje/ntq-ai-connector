<?php
/**
 * Template cho trang Logs
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ntq-ai-logs">
    <h1><?php _e( 'NTQ AI Connector - Lịch sử', 'ntq-ai-connector' ); ?></h1>
    
    <?php if ( empty( $logs ) ) : ?>
        <div class="ntq-ai-no-data">
            <p><?php _e( 'Chưa có dữ liệu lịch sử. Hãy sử dụng tính năng tóm tắt bài viết để xem lịch sử tại đây.', 'ntq-ai-connector' ); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>                    <th width="80"><?php _e( 'ID', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Bài viết', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Model sử dụng', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Thời gian yêu cầu', 'ntq-ai-connector' ); ?></th>
                    <th width="100"><?php _e( 'Thời gian phản hồi', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Người dùng', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Ngày tạo', 'ntq-ai-connector' ); ?></th>
                    <th><?php _e( 'Thao tác', 'ntq-ai-connector' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $logs as $log ) : ?>
                    <tr>                        <td><?php echo intval( $log->id ); ?></td>
                        <td>
                            <?php 
                            $post_title = get_the_title( $log->post_id );
                            $post_title = ! empty( $post_title ) ? $post_title : __( '(Không có tiêu đề)', 'ntq-ai-connector' );
                            $post_url = get_permalink( $log->post_id );
                            ?>
                            <a href="<?php echo esc_url( $post_url ); ?>" target="_blank"><?php echo esc_html( $post_title ); ?></a>
                        </td>
                        <td>
                            <?php 
                            $model_display_name = $log->model_used;
                            if ( $model_display_name === 'deepseek/deepseek-chat-v3-0324:free' ) {
                                echo 'DeepSeek Chat v3';
                            } elseif ( $model_display_name === 'deepseek/deepseek-r1-0528:free' ) {
                                echo 'DeepSeek R1';
                            } else {
                                echo esc_html( $model_display_name );
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html( $log->request_time ); ?></td>
                        <td><?php echo intval( $log->response_time ) . ' ms'; ?></td>
                        <td>
                            <?php 
                            if ( $log->user_id > 0 ) {
                                $user = get_userdata( $log->user_id );
                                echo isset( $user->display_name ) ? esc_html( $user->display_name ) : __( '(Không xác định)', 'ntq-ai-connector' );
                            } else {
                                _e( 'Khách', 'ntq-ai-connector' );
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html( $log->created_at ); ?></td>
                        <td>
                            <?php if (!empty($log->summary_text)) : ?>
                                <button type="button" class="button button-secondary view-summary" data-summary="<?php echo esc_attr($log->id); ?>">
                                    <span class="dashicons dashicons-visibility"></span> <?php _e('Xem tóm tắt', 'ntq-ai-connector'); ?>
                                </button>
                                <div class="summary-content" id="summary-<?php echo intval($log->id); ?>" style="display: none;">
                                    <?php echo wp_kses_post($log->summary_text); ?>
                                </div>
                            <?php else: ?>
                                <em><?php _e('Không có dữ liệu', 'ntq-ai-connector'); ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
          <?php
        // Hiển thị phân trang
        if ( $total_pages > 1 ) : 
            $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
            $page_links = paginate_links( array(
                'base' => add_query_arg( 'paged', '%#%' ),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $total_pages,
                'current' => $current_page
            ));
            
            if ( $page_links ) {
                echo '<div class="tablenav"><div class="tablenav-pages">';
                echo '<span class="displaying-num">' . sprintf( _n( '%s mục', '%s mục', $total_logs, 'ntq-ai-connector' ), number_format_i18n( $total_logs ) ) . '</span>';
                echo $page_links;
                echo '</div></div>';
            }
        endif;        ?>
    <?php endif; ?>
    
    <!-- Modal for displaying summary content -->
    <div id="summary-modal" class="ntq-ai-summary-modal" style="display:none;">
        <div class="ntq-ai-summary-modal-content">
            <div class="ntq-ai-summary-modal-header">
                <h2><?php _e('Nội dung tóm tắt', 'ntq-ai-connector'); ?></h2>
                <button type="button" class="ntq-ai-summary-modal-close">&times;</button>
            </div>
            <div class="ntq-ai-summary-modal-body markdown-body">
                <!-- Summary content will be inserted here -->
            </div>
            <div class="ntq-ai-summary-modal-footer">
                <button type="button" class="button button-primary ntq-ai-summary-modal-close"><?php _e('Đóng', 'ntq-ai-connector'); ?></button>
            </div>
        </div>
    </div>
</div>
