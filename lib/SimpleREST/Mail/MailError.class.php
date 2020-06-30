<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

use Exception;

class MailError extends Exception {

  /**
   * @var Message|null Optional mail message object
   */
  protected $_msg = null;

  /**
   * MailError constructor.
   * @param string $message
   * @param int $code
   * @param Exception|null $previous
   * @param Message|null $msg Optional mail message
   */
  public function __construct ($message, $code = 0, Exception $previous = null, Message $msg = null) {

    parent::__construct($message, $code, $previous);

    $this->_msg = $msg;

  }

  /**
   * @return string
   */
  public function __toString () {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  /**
   * @return Message|null
   */
  public function getMailMessage () {
    return $this->_msg;
  }

}