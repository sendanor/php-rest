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

    $this->assertSame(
      '1.23',
      '' . new Decimal('1.23')
    );

  }

  /**
   *
   */
  public function testCanSerializeJSON () {

    $this->assertSame(
      '1.23',
      json_encode(new Decimal('1.23'))
    );

  }

  /**
   *
   */
  public function testCanSumDecimals () {

    $this->assertSame(
      '1.27',
      rtrim('' . Decimal::sum('0.23', '1.04'), "0")
    );

  }

  /**
   *
   */
  public function testCanSubstractDecimals () {

    $this->assertSame(
      '0.81',
      rtrim('' . Decimal::sub( '1.04', '0.23'), "0")
    );

  }

  /**
   *
   */
  public function testCanDivideDecimals () {

    $this->assertSame(
      '100.4516129032',
      '' . Decimal::div( '124.56', '1.24')
    );

  }

  /**
   *
   */
  public function testCanMultiplyDecimals () {

    $this->assertSame(
      '1307.88',
      rtrim('' . Decimal::mul( '124.56', '10.5'), "0")
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsPositive () {

    $this->assertSame(
      1,
      Decimal::compare( '124.56', '10.5')
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsNegative () {

    $this->assertSame(
      -1,
      Decimal::compare( '10.5', '124.56')
    );

  }

  /**
   *
   */
  public function testCanCompareDecimalsEqual () {

    $this->assertSame(
      0,
      Decimal::compare( '124.56', '124.56')
    );

  }

  /**
   *
   */
  public function testCanTestEquality () {

    $this->assertSame(
      true,
      Decimal::isEqual( '124.56', '124.56')
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanFormatDecimal () {

    $this->assertSame(
      '124.56',
      Decimal::format( new Decimal('124.56789'), 2)
    );

  }

}
