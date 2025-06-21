/**
 * JavaScript cho frontend
 * 
 * @package NTQ AI Connector
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Khi document sẵn sàng
    $(document).ready(function() {        const widget = $('#ntq-ai-widget');
        const widgetButton = $('#ntq-ai-widget-button');
        const widgetMenu = $('#ntq-ai-widget-menu');
        const widgetMenuClose = $('#ntq-ai-widget-menu-close');
        const summarizeButton = $('#ntq-ai-summarize-button');
        
        const modal = $('#ntq-ai-modal');
        const modalTitle = $('#ntq-ai-modal-title');
        const modalClose = $('#ntq-ai-modal-close');
        const modalCloseBtn = $('#ntq-ai-modal-close-btn');
        const loading = $('#ntq-ai-loading');
        const result = $('#ntq-ai-result');
        const modelInfo = $('#ntq-ai-model-info');
        
        const animationsEnabled = ntqAiConnector.animations;
        
        // Hiệu ứng mở/đóng widget menu
        widgetButton.on('click', function(e) {
            e.preventDefault();
            
            if(widgetMenu.hasClass('active')) {
                closeWidgetMenu();
            } else {
                openWidgetMenu();
            }
        });
        
        // Đóng widget menu khi click vào nút đóng
        widgetMenuClose.on('click', function(e) {
            e.preventDefault();
            closeWidgetMenu();
        });
        
        // Đóng widget menu khi click outside
        $(document).on('click', function(e) {
            if(widgetMenu.hasClass('active') && 
               !widgetMenu.is(e.target) && 
               widgetMenu.has(e.target).length === 0 && 
               !widgetButton.is(e.target) && 
               widgetButton.has(e.target).length === 0) {
                closeWidgetMenu();
            }
        });
          // Xử lý sự kiện tóm tắt
        summarizeButton.on('click', function(e) {
            e.preventDefault();
            
            // Đóng widget menu
            closeWidgetMenu();
            
            // Mở modal
            openModal();
            
            // Reset modal
            resetModal();
            
            // Gọi API để tóm tắt với model mặc định từ backend
            summarizePost();
        });
        
        // Đóng modal
        modalClose.add(modalCloseBtn).on('click', function(e) {
            e.preventDefault();
            closeModal();
        });
        
        // Đóng modal khi click outside
        modal.on('click', function(e) {
            if($(e.target).is(modal)) {
                closeModal();
            }
        });
        
        /**
         * Mở widget menu
         */
        function openWidgetMenu() {
            widgetMenu.addClass('active');
            
            if(animationsEnabled) {
                widgetMenu.addClass('animate__animated animate__fadeIn');
                widgetMenu.css('animation-duration', '0.3s');
            }
        }
        
        /**
         * Đóng widget menu
         */
        function closeWidgetMenu() {
            if(animationsEnabled) {
                widgetMenu.addClass('animate__animated animate__fadeOut');
                widgetMenu.css('animation-duration', '0.2s');
                
                setTimeout(function() {
                    widgetMenu.removeClass('active animate__animated animate__fadeIn animate__fadeOut');
                }, 200);
            } else {
                widgetMenu.removeClass('active');
            }
        }
        
        /**
         * Mở modal
         */
        function openModal() {
            modal.addClass('active');
        }
        
        /**
         * Đóng modal
         */
        function closeModal() {
            modal.removeClass('active');
            
            // Reset modal sau 300ms (sau khi hiệu ứng fade out hoàn tất)
            setTimeout(function() {
                resetModal();
            }, 300);
        }
        
        /**
         * Reset modal về trạng thái ban đầu
         */
        function resetModal() {
            loading.show();
            result.html('').removeClass('active');
            modalTitle.text(ntqAiConnector.summaryTitle);
            modelInfo.text(ntqAiConnector.loadingText);
        }
        
        /**
         * Gọi AJAX để tóm tắt bài viết
         */
        function summarizePost(model) {
            $.ajax({
                url: ntqAiConnector.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ntq_ai_summarize_post',
                    nonce: ntqAiConnector.nonce,
                    post_id: ntqAiConnector.postId,
                    model: model
                },
                success: function(response) {
                    if(response.success) {
                        // Hiển thị kết quả
                        loading.hide();
                          // Format kết quả với Markdown
                        let formattedSummary = response.data.summary;
                        
                        // Sử dụng marked.js để chuyển đổi Markdown thành HTML
                        formattedSummary = marked.parse(formattedSummary);
                        
                        // Thêm class và hiển thị kết quả
                        result.html(formattedSummary).addClass('active');
                        
                        // Hiển thị model đã sử dụng
                        let modelName = response.data.model;
                        if(modelName.includes('deepseek-chat-v3')) {
                            modelName = 'DeepSeek Chat v3';
                        } else if(modelName.includes('deepseek-r1')) {
                            modelName = 'DeepSeek R1';
                        }
                        
                        modelInfo.text('Model: ' + modelName);
                        
                        // Thêm hiệu ứng nếu được bật
                        if(animationsEnabled) {
                            result.addClass('animate__animated animate__fadeIn');
                            result.css('animation-duration', '0.5s');
                            
                            // Xóa class animation sau khi hoàn tất
                            setTimeout(function() {
                                result.removeClass('animate__animated animate__fadeIn');
                            }, 500);
                        }
                    } else {
                        // Hiển thị lỗi
                        loading.hide();
                        
                        // Hiển thị thông báo lỗi
                        Swal.fire({
                            title: ntqAiConnector.errorTitle,
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: ntqAiConnector.closeText,
                            confirmButtonColor: '#7C3AED'
                        });
                        
                        // Đóng modal
                        closeModal();
                    }
                },
                error: function(xhr, status, error) {
                    // Hiển thị lỗi
                    loading.hide();
                    
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: ntqAiConnector.errorTitle,
                        text: 'Error: ' + error,
                        icon: 'error',
                        confirmButtonText: ntqAiConnector.closeText,
                        confirmButtonColor: '#7C3AED'
                    });
                    
                    // Đóng modal
                    closeModal();
                }
            });
        }
    });
})(jQuery);
