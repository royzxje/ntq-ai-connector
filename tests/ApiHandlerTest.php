<?php
use PHPUnit\Framework\TestCase;

class ApiHandlerTest extends TestCase {
    public function test_class_exists() {
        require_once __DIR__ . '/../includes/api/class-api-handler.php';
        $this->assertTrue( class_exists( 'API_Handler' ) );
    }
}
