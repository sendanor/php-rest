<?php declare(strict_types=1);

require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/lib/SimpleREST/Decimal/Decimal.class.php');

use SimpleREST\Decimal;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase {

  /**
   *
   */
  public function testCanCreateDecimal () {

    $this->assertInstanceOf(
      Decimal::class,
      new Decimal('1.23')
    );

  }

  /**
   *
   */
  public function testCanStringifyDecimal () {

    $this->assertEquals(
      '' . new Decimal('1.23'),
      '1.23'
    );

  }

  /**
   *
   */
  public function testCanSerializeJSON () {

    $this->assertEquals(
      json_encode(new Decimal('1.23')),
      '1.23'
    );

  }

  /**
   *
   */
  public function testCanSumDecimals () {

    $this->assertEquals(
      json_encode(Decimal::sum('0.23', '1.04')),
      '1.27'
    );

  }

  /**
   *
   */
  public function testCanSubstractDecimals () {

    $this->assertEquals(
      json_encode(Decimal::sub( '1.04', '0.23')),
      '0.81'
    );

  }

  /**
   *
   */
  public function testCanDivideDecimals () {

    $this->assertEquals(
      json_encode(Decimal::div( '124.56', '1.24')),
      '100.4516129032'
    );

  }

  /**
   *
   */
  public function testCanMultiplyDecimals () {

    $this->assertEquals(
      json_encode(Decimal::mul( '124.56', '10.5')),
      '1307.88'
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsPositive () {

    $this->assertEquals(
      json_encode(Decimal::compare( '124.56', '10.5')),
      1
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsNegative () {

    $this->assertEquals(
      json_encode(Decimal::compare( '10.5', '124.56')),
      -1
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsEqual () {

    $this->assertEquals(
      json_encode(Decimal::compare( '124.56', '124.56')),
      0
    );

  }

  /**
   *
   */
  public function testCanTestEquality () {

    $this->assertEquals(
      json_encode(Decimal::isEqual( '124.56', '124.56')),
      true
    );

  }

  /**
   *
   */
  public function testCanFormatDecimal () {

    $this->assertEquals(
      json_encode(Decimal::format( '124.56789', 2)),
      '124.56'
    );

  }

}
