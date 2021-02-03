<?php

use PHPUnit\Framework\TestCase;

/**
 * Basic Test.
 */
final class BasicTest extends TestCase {

  /**
   * Sample test.
   */
  public function testHelloWorld() {
    $hello = 'Hello, World!';

    $this->assertNotEquals('Hello World', $hello);
    $this->assertEquals('Hello, World!', $hello);
  }

}
