<?php
/**
 * Template cho widget tóm tắt bài viết
 *
 * @package NTQ AI Connector
 * @since 1.0.0
 */

// Nếu file được gọi trực tiếp, thoát.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$position = get_option( 'ntq_ai_connector_widget_position', 'bottom-right' );
$enable_animations = get_option( 'ntq_ai_connector_enable_animations', 'yes' ) === 'yes';
$animation_class = $enable_animations ? 'animate__animated animate__fadeIn' : '';
$widget_header_text = get_option( 'ntq_ai_connector_widget_header_text', 'NTQ AI' );
$widget_footer_text = get_option( 'ntq_ai_connector_widget_footer_text', 'Powered by NTQ AI Connector' );
$summarize_button_text = get_option( 'ntq_ai_connector_summarize_button_text', 'Tóm tắt bài viết hiện tại' );
?>

<div id="ntq-ai-widget" class="ntq-ai-widget ntq-ai-widget-<?php echo esc_attr( $position ); ?> <?php echo esc_attr( $animation_class ); ?>">
    <button id="ntq-ai-widget-button" class="ntq-ai-widget-button">
        <svg
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    class="ntq-icon"
  >
    <path
      class="path"
      stroke-linejoin="round"
      stroke-linecap="round"
      stroke="white"
      fill="white"
      d="M14.187 8.096L15 5.25L15.813 8.096C16.0231 8.83114 16.4171 9.50062 16.9577 10.0413C17.4984 10.5819 18.1679 10.9759 18.903 11.186L21.75 12L18.904 12.813C18.1689 13.0231 17.4994 13.4171 16.9587 13.9577C16.4181 14.4984 16.0241 15.1679 15.814 15.903L15 18.75L14.187 15.904C13.9769 15.1689 13.5829 14.4994 13.0423 13.9587C12.5016 13.4181 11.8321 13.0241 11.097 12.814L8.25 12L11.096 11.187C11.8311 10.9769 12.5006 10.5829 13.0413 10.0423C13.5819 9.50162 13.9759 8.83214 14.186 8.097L14.187 8.096Z"
    ></path>
    <path
      class="path"
      stroke-linejoin="round"
      stroke-linecap="round"
      stroke="white"
      fill="white"
      d="M6 14.25L5.741 15.285C5.59267 15.8785 5.28579 16.4206 4.85319 16.8532C4.42059 17.2858 3.87853 17.5927 3.285 17.741L2.25 18L3.285 18.259C3.87853 18.4073 4.42059 18.7142 4.85319 19.1468C5.28579 19.5794 5.59267 20.1215 5.741 20.715L6 21.75L6.259 20.715C6.40725 20.1216 6.71398 19.5796 7.14639 19.147C7.5788 18.7144 8.12065 18.4075 8.714 18.259L9.75 18L8.714 17.741C8.12065 17.5925 7.5788 17.2856 7.14639 16.853C6.71398 16.4204 6.40725 15.8784 6.259 15.285L6 14.25Z"
    ></path>
    <path
      class="path"
      stroke-linejoin="round"
      stroke-linecap="round"
      stroke="white"
      fill="white"
      d="M6.5 4L6.303 4.5915C6.24777 4.75718 6.15472 4.90774 6.03123 5.03123C5.90774 5.15472 5.75718 5.24777 5.5915 5.303L5 5.5L5.5915 5.697C5.75718 5.75223 5.90774 5.84528 6.03123 5.96877C6.15472 6.09226 6.24777 6.24282 6.303 6.4085L6.5 7L6.697 6.4085C6.75223 6.24282 6.84528 6.09226 6.96877 5.96877C7.09226 5.84528 7.24282 5.75223 7.4085 5.697L8 5.5L7.4085 5.303C7.24282 5.24777 7.09226 5.15472 6.96877 5.03123C6.84528 4.90774 6.75223 4.75718 6.697 4.5915L6.5 4Z"
    ></path>
  </svg>
        <span class="ntq-ai-widget-button-text">AI</span>
    </button>
    
    <div id="ntq-ai-widget-menu" class="ntq-ai-widget-menu">
        <div class="ntq-ai-widget-menu-header">
            <span><?php echo esc_html( $widget_header_text ); ?></span>
            <button id="ntq-ai-widget-menu-close" class="ntq-ai-widget-menu-close">
                <svg class="ntq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="ntq-ai-widget-menu-items">            <button id="ntq-ai-summarize-button" class="ntq-ai-widget-menu-item">
                <svg class="ntq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                <span><?php echo esc_html( $summarize_button_text ); ?></span>
            </button>
            <!-- Model selector đã được ẩn và chỉ sử dụng model mặc định từ settings -->
        </div>        <div class="ntq-ai-widget-menu-footer">
            <span><?php echo esc_html( $widget_footer_text ); ?></span>
        </div>
    </div>
</div>

<div id="ntq-ai-modal" class="ntq-ai-modal">
    <div class="ntq-ai-modal-content">
        <div class="ntq-ai-modal-header">
            <h3 id="ntq-ai-modal-title"><?php _e( 'Tóm tắt bài viết', 'ntq-ai-connector' ); ?></h3>
            <button id="ntq-ai-modal-close" class="ntq-ai-modal-close">
                <svg class="ntq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="ntq-ai-modal-body">
            <div id="ntq-ai-loading" class="ntq-ai-loading">
                <!-- From Uiverse.io by elijahgummer --> 
<div class="analyze">
  <svg
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    height="54"
    width="54"
  >
    <rect height="24" width="24"></rect>
    <path
      stroke-linecap="round"
      stroke-width="1.5"
      stroke="black"
      d="M19.25 9.25V5.25C19.25 4.42157 18.5784 3.75 17.75 3.75H6.25C5.42157 3.75 4.75 4.42157 4.75 5.25V18.75C4.75 19.5784 5.42157 20.25 6.25 20.25H12.25"
      class="board"
    ></path>
    <path
      d="M9.18748 11.5066C9.12305 11.3324 8.87677 11.3324 8.81234 11.5066L8.49165 12.3732C8.47139 12.428 8.42823 12.4711 8.37348 12.4914L7.50681 12.8121C7.33269 12.8765 7.33269 13.1228 7.50681 13.1872L8.37348 13.5079C8.42823 13.5282 8.47139 13.5714 8.49165 13.6261L8.81234 14.4928C8.87677 14.6669 9.12305 14.6669 9.18748 14.4928L9.50818 13.6261C9.52844 13.5714 9.5716 13.5282 9.62634 13.5079L10.493 13.1872C10.6671 13.1228 10.6671 12.8765 10.493 12.8121L9.62634 12.4914C9.5716 12.4711 9.52844 12.428 9.50818 12.3732L9.18748 11.5066Z"
      class="star-2"
    ></path>
    <path
      d="M11.7345 6.63394C11.654 6.41629 11.3461 6.41629 11.2656 6.63394L10.8647 7.71728C10.8394 7.78571 10.7855 7.83966 10.717 7.86498L9.6337 8.26585C9.41605 8.34639 9.41605 8.65424 9.6337 8.73478L10.717 9.13565C10.7855 9.16097 10.8394 9.21493 10.8647 9.28335L11.2656 10.3667C11.3461 10.5843 11.654 10.5843 11.7345 10.3667L12.1354 9.28335C12.1607 9.21493 12.2147 9.16097 12.2831 9.13565L13.3664 8.73478C13.5841 8.65424 13.5841 8.34639 13.3664 8.26585L12.2831 7.86498C12.2147 7.83966 12.1607 7.78571 12.1354 7.71728L11.7345 6.63394Z"
      class="star-1"
    ></path>
    <path
      class="stick"
      stroke-linejoin="round"
      stroke-width="1.5"
      stroke="black"
      d="M17 14L21.2929 18.2929C21.6834 18.6834 21.6834 19.3166 21.2929 19.7071L20.7071 20.2929C20.3166 20.6834 19.6834 20.6834 19.2929 20.2929L15 16M17 14L15.7071 12.7071C15.3166 12.3166 14.6834 12.3166 14.2929 12.7071L13.7071 13.2929C13.3166 13.6834 13.3166 14.3166 13.7071 14.7071L15 16M17 14L15 16"
    ></path>
  </svg>
</div>

                <p><?php _e( 'Đang tóm tắt...', 'ntq-ai-connector' ); ?></p>
            </div>
            <div id="ntq-ai-result" class="ntq-ai-result"></div>
        </div>
        <div class="ntq-ai-modal-footer">
            <span id="ntq-ai-model-info" class="ntq-ai-model-info"><?php _e( 'Model: ', 'ntq-ai-connector' ); ?></span>
            <button id="ntq-ai-modal-close-btn" class="ntq-ai-button"><?php _e( 'Đóng', 'ntq-ai-connector' ); ?></button>
        </div>
    </div>
</div>
