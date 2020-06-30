<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

use TypeError;

/**
 * Abstract Class Mailer
 *
 * Base class for our mailers.
 *
 * @package SimpleREST\Mail
 */
class Mailer {

  /**
   * @var BaseMailer|null
   */
  static private $_mailer = null;

  /**
   * @param BaseMailer $mailer
   */
  public static function setMailer (BaseMailer $mailer) {
    self::$_mailer = $mailer;
  }

  /**
   * @return BaseMailer
   */
  public static function createDefaultMailer () {

    return self::createMailer( defined('REST_MAILER') ? REST_MAILER : 'php' );

  }

  /**
   * Creates a mailer by string name
   *
   * @param string $name The name of mailer
   * @param mixed|null $opt Optional options
   * @return BaseMailer
   */
  public static function createMailer ($name, $opt = null) {

    switch ($name) {

      case "file":
        require_once( dirname(__FILE__) . '/File/index.php');
        return new FileMailer($opt);

      case "php":
        require_once( dirname(__FILE__) . '/PHP/index.php');
        return new PHPMailer($opt);

      default:
        throw new TypeError('Unknown mailer: ' . $name);

    }

  }


  /**
   * @param Message $msg
   * @return SentMessage
   * @throws MailError if cannot send the mail
   */
  public static function send (Message $msg) {

    if (self::$_mailer === null) {
      self::$_mailer = self::createDefaultMailer();
    }

    return self::$_mailer->send($msg);

  }

  /**
   * @param Message[] $messages
   * @return SentMessage
   * @throws MailError if cannot send the mail
   */
  public static function sendMessages (array $messages) {

    if (self::$_mailer === null) {
      self::$_mailer = self::createDefaultMailer();
    }

    return self::$_mailer->sendMessages($messages);

  }

}
