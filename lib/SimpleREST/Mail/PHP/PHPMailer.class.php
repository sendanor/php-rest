<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

use \SimpleREST\Log\Log as Log;

/**
 * Class PHPMailer
 *
 * Simple mailer which uses PHP mail() function to send emails.
 *
 * @package SimpleREST\Mail
 */
class PHPMailer extends BaseMailer {

  /**
   * PHPMailer constructor.
   */
  public function __construct() {

  }

  /**
   * @param Message $msg
   * @return bool
   */
  protected static function _sendMessage (Message $msg) {
    return \mail($msg->to, $msg->subject, $msg->body, $msg->headers);
  }

  /**
   * @return string
   */
  protected static function _getLastMessageError () {
    return '' . error_get_last()['message'];
  }

  /**
   * @noinspection PhpUnused
   * @param Message $msg
   * @return SentMessage
   * @throws MailError if cannot send mail
   */
  public function send (Message $msg) {

    $success = self::_sendMessage($msg);

    if ($success === FALSE) {
      throw new MailError(self::_getLastMessageError(), null, null, $msg);
    }

    return new SentMessage($msg, true, $success);

  }

  /**
   *
   * @param array $messages
   * @return SentMessage[]
   */
  public function sendMessages (array $messages) {

    return array_map(function ($msg) {

      $success = self::_sendMessage($msg);

      if ($success === FALSE) {
        Log::warning('Warning! Could not send mail to "'. $msg->to .'": ' . self::_getLastMessageError() );
      }

      return new SentMessage($msg, true, $success);

    }, $messages);

  }

}
