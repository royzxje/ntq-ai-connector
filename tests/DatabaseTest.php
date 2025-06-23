<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    public function test_class_exists() {
        require_once __DIR__ . '/../includes/class-database.php';
        $this->assertTrue( class_exists( 'Database' ) );
    }
}
