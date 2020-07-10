<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
declare(strict_types=1);

namespace SimpleREST\Mail;

use TypeError;
use JsonSerializable;

/**
 * Class PHPMailer
 *
 * Simple mailer which uses PHP mail() function to send emails.
 *
 * @property string to
 * @property string subject
 * @property string body
 * @property array headers
 * @package SimpleREST\Mail
 */
class Message implements JsonSerializable {

  /**
   * @var string
   */
  protected $_to;

  /**
   * @var string
   */
  protected $_subject;

  /**
   * @var string
   */
  protected $_body;

  /**
   * @var array|null
   */
  protected $_headers;

  /**
   * MailMessage constructor.
   * @param $to
   * @param $subject
   * @param $body
   * @param $headers
   */
  public function __construct (string $to, string $subject = '', string $body = '', array $headers = array()) {

    $this->_to = $to;
    $this->_subject = $subject;
    $this->_body = $body;
    $this->_headers = $headers;

  }

  /**
   * @param string $name
   * @return bool
   */
  public function __isset ($name) {
    switch($name) {
      case 'subject':
      case 'body':
      case 'to'     :
        return true;

      case 'headers':
        return $this->_headers !== null;
    }
    throw new TypeError('Undefined property: ' . $name);
  }

  /**
   * @param string $name
   * @return array|string|null
   */
  public function __get ($name) {
    switch($name) {
      case 'to'      : return $this->_to;
      case 'subject' : return $this->_subject;
      case 'body' : return $this->_body;
      case 'headers' : return $this->_headers;
    }
    throw new TypeError('Undefined property: ' . $name);
  }

  /**
   * @param string $name
   * @return string|null
   */
  public function getHeader ($name) {
    return $this->_headers !== null ? ( isset($this->_headers[$name]) ? $this->_headers[$name] : null ) : null;
  }


  /**
   * @return string
   */
  public function __toString () {
    return "Message(To:" . $this->to . ";Subject:" . $this->subject . ")";
  }

  /**
   * @return string[]
   * @noinspection PhpUnused
   */
  public function jsonSerialize () {

    return array(
      "to" => $this->to,
      "subject" => $this->subject,
      "body" => $this->body,
      "headers" => $this->headers ?? array()
    );

  }

}
