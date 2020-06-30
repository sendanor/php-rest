<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

use TypeError;

/**
 * Class SentMessage
 *
 * Envelope class for sent Messages.
 *
 * @property bool success If true, the message has been successfully delivered to the mail system. This does not mean the mail is received.
 * @property string to
 * @property string subject
 * @property string body
 * @property Message original
 * @property array headers
 * @package SimpleREST\Mail
 */
class SentMessage {

  /**
   * @var bool
   */
  protected $_success;

  /**
   * @var Message
   */
  protected $_msg;

  /**
   * MailMessage constructor.
   * @param Message $msg
   * @param bool $success
   */
  public function __construct (Message $msg, bool $success = true) {

    $this->_msg = $msg;

    $this->_success = $success;

  }


  /**
   * @param string $name
   * @return bool
   */
  public function __isset ($name) {
    switch($name) {
      case 'subject':
      case 'body':
      case 'to':
      case 'success':
      case 'original':
        return true;

      case 'headers':
        return $this->_msg->headers !== null;
    }

    throw new TypeError('Undefined property: ' . $name);
  }

  /**
   * @param string $name
   * @return array|string|null
   */
  public function __get ($name) {
    switch($name) {
      case 'success'  : return $this->_success;
      case 'original' : return $this->_msg;
      case 'to'       : return $this->_msg->to;
      case 'subject'  : return $this->_msg->subject;
      case 'body'     : return $this->_msg->body;
      case 'headers'  : return $this->_msg->headers;
    }
    throw new TypeError('Undefined property: ' . $name);
  }

}
