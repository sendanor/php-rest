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
  public function testCanDetectPositiveIntegers () {

    $this->assertSame(
      false,
      (new Decimal('1.23'))->isInteger(),
      '1.23'
    );

    $this->assertSame(
      false,
      (new Decimal('1.2'))->isInteger(),
      '1.2'
    );

    $this->assertSame(
      true,
      (new Decimal('1'))->isInteger(),
      '1'
    );

    $this->assertSame(
      true,
      (new Decimal('1.0'))->isInteger(),
      '1.0'
    );

    $this->assertSame(
      true,
      (new Decimal('0'))->isInteger(),
      '0'
    );

  }

  /**
   *
   */
  public function testCanDetectNegativeIntegers () {

    $this->assertSame(
      false,
      (new Decimal('-1.23'))->isInteger(),
      '-1.23'
    );

    $this->assertSame(
      false,
      (new Decimal('-1.2'))->isInteger(),
      '-1.2'
    );

    $this->assertSame(
      true,
      (new Decimal('-1'))->isInteger(),
      '-1'
    );

    $this->assertSame(
      true,
      (new Decimal('-1.0'))->isInteger(),
      '-1.0'
    );

  }

  /**
   *
   */
  public function testCanDetectPositiveDecimals () {

    $this->assertSame(
      false,
      (new Decimal('1.23'))->isNegative()
    );

    $this->assertSame(
      false,
      (new Decimal('1.2'))->isNegative()
    );

    $this->assertSame(
      false,
      (new Decimal('1'))->isNegative()
    );

    $this->assertSame(
      false,
      (new Decimal('1.0'))->isNegative()
    );

    $this->assertSame(
      false,
      (new Decimal('0'))->isNegative()
    );
  }

  /**
   *
   */
  public function testCanDetectNegativeDecimals () {

    $this->assertSame(
      true,
      (new Decimal('-1.23'))->isNegative(),
      '-1.23'
    );

    $this->assertSame(
      true,
      (new Decimal('-1.2'))->isNegative(),
      '-1.2'
    );

    $this->assertSame(
      true,
      (new Decimal('-1'))->isNegative(),
      '-1'
    );

    $this->assertSame(
      true,
      (new Decimal('-1.0'))->isNegative(),
      '-1.0'
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
  public function testCanTestGreaterThan () {

    $this->assertSame(
      true,
      Decimal::isGreater( '124.56', '10.5'),
      '124.56 > 10.5'
    );

    $this->assertSame(
      true,
      Decimal::isGreater( '10.5', '1'),
      '10.5 > 1'
    );

    $this->assertSame(
      true,
      Decimal::isGreater( '10', '-10.5'),
      '10 > -10.5'
    );

    $this->assertSame(
      false,
      Decimal::isGreater( '10', '10'),
      '10 > 10'
    );

    $this->assertSame(
      false,
      Decimal::isGreater(  '10.5', '124.56'),
      '10.5 > 124.56'
    );

    $this->assertSame(
      false,
      Decimal::isGreater( '1', '10.5'),
      '1 > 10.5'
    );

    $this->assertSame(
      false,
      Decimal::isGreater( '-10.5', '10'),
      '-10.5 > 10'
    );

  }
  /**
   *
   */
  public function testCanTestGreaterOrEqual () {

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '124.56', '10.5'),
      '124.56 >= 10.5'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '10.5', '1'),
      '10.5 >= 1'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '10', '-10.5'),
      '10 >= -10.5'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '10', '10'),
      '10 >= 10'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '-10', '-10'),
      '-10 >= -10'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '0', '0.00'),
      '0 >= 0.00'
    );

    $this->assertSame(
      true,
      Decimal::isGreaterOrEqual( '0.000100', '0.000100'),
      '0.000100 >= 0.000100'
    );

    $this->assertSame(
      false,
      Decimal::isGreaterOrEqual(  '10.5', '124.56'),
      '10.5 >= 124.56'
    );

    $this->assertSame(
      false,
      Decimal::isGreaterOrEqual( '1', '10.5'),
      '1 >= 10.5'
    );

    $this->assertSame(
      false,
      Decimal::isGreaterOrEqual( '-10.5', '10'),
      '-10.5 >= 10'
    );

  }

  /**
   *
   */
  public function testCanTestLowerThan () {

    $this->assertSame(
      true,
      Decimal::isLower(  '10.5', '124.56')
    );

    $this->assertSame(
      true,
      Decimal::isLower( '1', '10.5')
    );

    $this->assertSame(
      true,
      Decimal::isLower( '-10.5', '10')
    );


    $this->assertSame(
      false,
      Decimal::isLower(   '124.56', '10.5')
    );

    $this->assertSame(
      false,
      Decimal::isLower(  '10.5', '1')
    );

    $this->assertSame(
      false,
      Decimal::isLower( '10', '-10.5')
    );

    $this->assertSame(
      false,
      Decimal::isLower( '10', '0')
    );

    $this->assertSame(
      true,
      Decimal::isLower( '-10', '0')
    );

    $this->assertSame(
      false,
      Decimal::isLower( '-10', '-10')
    );

    $this->assertSame(
      false,
      Decimal::isLower( '0', '0')
    );

    $this->assertSame(
      false,
      Decimal::isLower( '12', '12')
    );

  }

  /**
   *
   */
  public function testCanTestLowerOrEqual () {

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual(  '10.5', '124.56')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '1', '10.5')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '-10.5', '10')
    );


    $this->assertSame(
      false,
      Decimal::isLowerOrEqual(   '124.56', '10.5')
    );

    $this->assertSame(
      false,
      Decimal::isLowerOrEqual(  '10.5', '1')
    );

    $this->assertSame(
      false,
      Decimal::isLowerOrEqual( '10', '-10.5')
    );

    $this->assertSame(
      false,
      Decimal::isLowerOrEqual( '10', '0')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '-10', '0')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '-10', '-10')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '0', '0')
    );

    $this->assertSame(
      true,
      Decimal::isLowerOrEqual( '12', '12')
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
  public function testCanFloorDecimals () {

    $this->assertSame(
      '124',
      '' . Decimal::floor( new Decimal('124.56'))
    );

    $this->assertSame(
      '124',
      '' . Decimal::floor( new Decimal('124.1'))
    );

    $this->assertSame(
      '14',
      '' . Decimal::floor( new Decimal('14.00'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::floor( new Decimal('4.9'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::floor( new Decimal('4.0'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanFloorNegativeDecimals () {

    $this->assertSame(
      '-125',
      '' . Decimal::floor( new Decimal('-124.56'))
    );

    $this->assertSame(
      '-125',
      '' . Decimal::floor( new Decimal('-124.1'))
    );

    $this->assertSame(
      '-14',
      '' . Decimal::floor( new Decimal('-14.00'))
    );

    $this->assertSame(
      '-5',
      '' . Decimal::floor( new Decimal('-4.9'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::floor( new Decimal('-4.0'))
    );

  }


  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanCeilDecimals () {

    $this->assertSame(
      '125',
      '' . Decimal::ceil( new Decimal('124.56'))
    );

    $this->assertSame(
      '125',
      '' . Decimal::ceil( new Decimal('124.1'))
    );

    $this->assertSame(
      '14',
      '' . Decimal::ceil( new Decimal('14.00'))
    );

    $this->assertSame(
      '5',
      '' . Decimal::ceil( new Decimal('4.9'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::ceil( new Decimal('4.0'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanCeilNegativeDecimals () {

    $this->assertSame(
      '-124',
      '' . Decimal::ceil( new Decimal('-124.56'))
    );

    $this->assertSame(
      '-124',
      '' . Decimal::ceil( new Decimal('-124.1'))
    );

    $this->assertSame(
      '-14',
      '' . Decimal::ceil( new Decimal('-14.00'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::ceil( new Decimal('-4.9'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::ceil( new Decimal('-4.0'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanRoundDecimals () {

    $this->assertSame(
      '125',
      '' . Decimal::round( new Decimal('124.56'))
    );

    $this->assertSame(
      '124',
      '' . Decimal::round( new Decimal('124.1'))
    );

    $this->assertSame(
      '14',
      '' . Decimal::round( new Decimal('14.00'))
    );

    $this->assertSame(
      '5',
      '' . Decimal::round( new Decimal('4.9'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::round( new Decimal('4.0'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::round( new Decimal('4.1'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::round( new Decimal('4.01'))
    );

    $this->assertSame(
      '4',
      '' . Decimal::round( new Decimal('4.49999'))
    );

    $this->assertSame(
      '5',
      '' . Decimal::round( new Decimal('4.5'))
    );

    $this->assertSame(
      '5',
      '' . Decimal::round( new Decimal('4.5001'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanRoundNegativeDecimals () {

    $this->assertSame(
      '-125',
      '' . Decimal::round( new Decimal('-124.56'))
    );

    $this->assertSame(
      '-124',
      '' . Decimal::round( new Decimal('-124.1'))
    );

    $this->assertSame(
      '-14',
      '' . Decimal::round( new Decimal('-14.00'))
    );

    $this->assertSame(
      '-5',
      '' . Decimal::round( new Decimal('-4.9'))
    );

    $this->assertSame(
      '-5',
      '' . Decimal::round( new Decimal('-4.99999999999999999999999999999999999999999'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::round( new Decimal('-4.499999999999999'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::round( new Decimal('-4.499999999999999999999999999999999'))
    );

    $this->assertSame(
      '-5',
      '' . Decimal::round( new Decimal('-5.000000000000000000000000000000001'))
    );

    $this->assertSame(
      '-4',
      '' . Decimal::round( new Decimal('-4.0'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanAbsNegativeDecimals () {

    $this->assertSame(
      '124.56',
      '' . Decimal::abs( new Decimal('-124.56'))
    );

    $this->assertSame(
      '124.1',
      '' . Decimal::abs( new Decimal('-124.1'))
    );

    $this->assertSame(
      '14.00',
      '' . Decimal::abs( new Decimal('-14.00'))
    );

    $this->assertSame(
      '4.9',
      '' . Decimal::abs( new Decimal('-4.9'))
    );

    $this->assertSame(
      '4.99999999999999999999999999999999999999999',
      '' . Decimal::abs( new Decimal('-4.99999999999999999999999999999999999999999'))
    );

    $this->assertSame(
      '4.499999999999999',
      '' . Decimal::abs( new Decimal('-4.499999999999999'))
    );

    $this->assertSame(
      '4.499999999999999999999999999999999',
      '' . Decimal::abs( new Decimal('-4.499999999999999999999999999999999'))
    );

    $this->assertSame(
      '5.000000000000000000000000000000001',
      '' . Decimal::abs( new Decimal('-5.000000000000000000000000000000001'))
    );

    $this->assertSame(
      '4.0',
      '' . Decimal::abs( new Decimal('-4.0'))
    );

  }

  /**
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function testCanAbsPositiveDecimals () {

    $this->assertSame(
      '124.56',
      '' . Decimal::abs( new Decimal('124.56'))
    );

    $this->assertSame(
      '124.1',
      '' . Decimal::abs( new Decimal('124.1'))
    );

    $this->assertSame(
      '14.00',
      '' . Decimal::abs( new Decimal('14.00'))
    );

    $this->assertSame(
      '4.9',
      '' . Decimal::abs( new Decimal('4.9'))
    );

    $this->assertSame(
      '4.99999999999999999999999999999999999999999',
      '' . Decimal::abs( new Decimal('4.99999999999999999999999999999999999999999'))
    );

    $this->assertSame(
      '4.499999999999999',
      '' . Decimal::abs( new Decimal('4.499999999999999'))
    );

    $this->assertSame(
      '4.499999999999999999999999999999999',
      '' . Decimal::abs( new Decimal('4.499999999999999999999999999999999'))
    );

    $this->assertSame(
      '5.000000000000000000000000000000001',
      '' . Decimal::abs( new Decimal('5.000000000000000000000000000000001'))
    );

    $this->assertSame(
      '4.0',
      '' . Decimal::abs( new Decimal('4.0'))
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
