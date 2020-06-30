<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

use TypeError;
use JsonSerializable;

/**
 * Class SentMessage
 *
 * Envelope class for sent Messages.
 *
 * @property bool sent If true, the message has been successfully delivered to local queue. This does not mean the mail is received.
 * @property bool success If true, the message has been successfully delivered to the mail system. This does not mean the mail is received.
 * @property string to
 * @property string subject
 * @property string body
 * @property Message original
 * @property array headers
 * @package SimpleREST\Mail
 */
class SentMessage implements JsonSerializable {

  /**
   * TRUE if the message has been sent to a local queue.
   *
   * FALSE if the message is still on local queue.
   *
   * @var bool
   */
  protected $_sent = false;

  /**
   * TRUE if the message has been sent to mail system. This does not mean it has been delivered.
   *
   * FALSE if it is not yet sent to the mail system.
   *
   * @var bool
   */
  protected $_success = false;

  /**
   * @var Message
   */
  protected $_msg;

  /**
   * MailMessage constructor.
   * @param Message $msg
   * @param bool $success TRUE if message has been delivered to the mail system
   * @param bool $sent TRUE if message has been delivered to the local queue
   */
  public function __construct (Message $msg, bool $sent = false, bool $success = false) {

    $this->_msg = $msg;
    $this->_sent = $sent;
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
      case 'sent':
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
      case 'sent'  : return $this->_sent;
      case 'success'  : return $this->_success;
      case 'original' : return $this->_msg;
      case 'to'       : return $this->_msg->to;
      case 'subject'  : return $this->_msg->subject;
      case 'body'     : return $this->_msg->body;
      case 'headers'  : return $this->_msg->headers;
    }
    throw new TypeError('Undefined property: ' . $name);
  }

  /**
   * @return string
   */
  public function __toString () {
    return "SentMessage(Sent={$this->_sent};Success={$this->_success};To:{$this->to};Subject:{$this->subject})";
  }

  /**
   * @return string[]
   * @noinspection PhpUnused
   */
  public function jsonSerialize () {

    return array(
      "sent" => $this->_sent,
      "success" => $this->_success,
      "to" => $this->to,
      "subject" => $this->subject,
      "body" => $this->body,
      "headers" => $this->headers ?? array()
    );

  }

}
