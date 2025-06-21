/**
 * JavaScript cho admin
 * 
 * @package NTQ AI Connector
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Khi document sẵn sàng
    $(document).ready(function() {
        // Xử lý xem nội dung tóm tắt
        $('.view-summary').on('click', function() {
            const summaryId = $(this).data('summary');
            const summaryContent = $('#summary-' + summaryId).html();
            
            // Parse Markdown nếu có thư viện Marked
            let formattedContent = summaryContent;
            if (typeof marked !== 'undefined') {
                formattedContent = marked.parse(summaryContent);
            }
              // Hiển thị modal với nội dung tóm tắt
            $('.ntq-ai-summary-modal-body').html(formattedContent);
            $('#summary-modal').fadeIn(300);
        });
          // Đóng modal khi nhấn nút đóng
        $('.ntq-ai-summary-modal-close').on('click', function() {
            $('#summary-modal').fadeOut(200);
        });
        
        // Đóng modal khi click bên ngoài
        $(window).on('click', function(e) {
            if ($(e.target).is('#summary-modal')) {
                $('#summary-modal').fadeOut(200);
            }
        });
        
        // Bấm Escape để đóng modal
        $(document).keyup(function(e) {
            if (e.key === "Escape" && $('#summary-modal').is(':visible')) { 
                $('#summary-modal').fadeOut(200);
            }
        });
        // Xử lý kiểm tra API key
        const apiKeyField = $('#ntq_ai_connector_api_key');
        const testApiKeyButton = $('#ntq_ai_connector_test_api_key');
        const apiKeyStatus = $('#ntq_ai_connector_api_key_status');
        
        testApiKeyButton.on('click', function(e) {
            e.preventDefault();
            
            const apiKey = apiKeyField.val();
            
            if(!apiKey) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Vui lòng nhập API key trước khi kiểm tra',
                    icon: 'error',
                    confirmButtonColor: '#7C3AED'
                });
                return;
            }
            
            // Hiển thị trạng thái đang kiểm tra
            apiKeyStatus.removeClass('success error').addClass('testing').text(ntqAiConnector.testing).show();
            
            // Gọi AJAX để kiểm tra API key
            $.ajax({
                url: ntqAiConnector.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ntq_ai_test_api_key',
                    nonce: ntqAiConnector.nonce,
                    api_key: apiKey
                },
                success: function(response) {
                    if(response.success) {
                        apiKeyStatus.removeClass('testing error').addClass('success').text(response.data.message);
                        
                        Swal.fire({
                            title: 'Thành công',
                            text: ntqAiConnector.testSuccess,
                            icon: 'success',
                            confirmButtonColor: '#7C3AED'
                        });
                    } else {
                        apiKeyStatus.removeClass('testing success').addClass('error').text(response.data.message);
                        
                        Swal.fire({
                            title: 'Lỗi',
                            text: ntqAiConnector.testFailed,
                            icon: 'error',
                            confirmButtonColor: '#7C3AED'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    apiKeyStatus.removeClass('testing success').addClass('error').text('Error: ' + error);
                    
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi kiểm tra API key: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#7C3AED'
                    });
                }
            });
        });
        
        // Xử lý preview gradient
        const gradientStart = $('#ntq_ai_connector_gradient_start');
        const gradientEnd = $('#ntq_ai_connector_gradient_end');
        const gradientPreview = $('#gradient_preview');
        
        function updateGradientPreview() {
            const start = gradientStart.val();
            const end = gradientEnd.val();
            
            gradientPreview.css('background', `linear-gradient(135deg, ${start}, ${end})`);
        }
        
        gradientStart.on('input', updateGradientPreview);
        gradientEnd.on('input', updateGradientPreview);
        
        // Khởi tạo preview gradient
        updateGradientPreview();
    });
})(jQuery);
