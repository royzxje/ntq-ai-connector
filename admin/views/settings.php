<?php
/**
 * Template cho trang Settings
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ntq-ai-settings">
    <h1><?php _e( 'NTQ AI Connector - Cài đặt', 'ntq-ai-connector' ); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'ntq_ai_connector_settings' ); ?>
        <?php do_settings_sections( 'ntq_ai_connector_settings' ); ?>
        
        <div class="ntq-ai-settings-footer">
            <?php submit_button(); ?>
            
            <div class="ntq-ai-settings-info">
                <div class="ntq-ai-settings-info-item">
                    <h4><?php _e( 'Hướng dẫn sử dụng', 'ntq-ai-connector' ); ?></h4>
                    <ul>
                        <li><?php _e( 'Đăng ký tài khoản tại <a href="https://openrouter.ai" target="_blank">OpenRouter</a>', 'ntq-ai-connector' ); ?></li>
                        <li><?php _e( 'Lấy API key từ <a href="https://openrouter.ai/keys" target="_blank">trang quản lý API</a>', 'ntq-ai-connector' ); ?></li>
                        <li><?php _e( 'Cấu hình API key và các cài đặt khác ở trên', 'ntq-ai-connector' ); ?></li>
                        <li><?php _e( 'Thiết lập plugin đã hoàn tất, widget sẽ xuất hiện trên frontend', 'ntq-ai-connector' ); ?></li>
                    </ul>
                </div>
                <div class="ntq-ai-settings-info-item">
                    <h4><?php _e( 'Về NTQ AI Connector', 'ntq-ai-connector' ); ?></h4>
                    <p><?php _e( 'Plugin được phát triển bởi ntquan', 'ntq-ai-connector' ); ?></p>
                    <p><?php _e( 'Phiên bản:', 'ntq-ai-connector' ); ?> <?php echo NTQ_AI_CONNECTOR_VERSION; ?></p>
                    <p><?php _e( 'Website:', 'ntq-ai-connector' ); ?> <a href="https://q2k1.com" target="_blank">q2k1.com</a></p>
                </div>
            </div>
        </div>
    </form>
</div>
