<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
declare(strict_types=1);

namespace SimpleREST\Mail;

use TypeError;
use Exception;

use SimpleREST\Log\Log;

/**
 * Class FileMailer
 *
 * Simple mailer which does not actually sent mail, just saves it on disk as a JSON file.
 *
 * @package SimpleREST\Mail
 */
class FileMailer extends BaseMailer {

  /**
   * @var string|null The JSON file name where messages are saved.
   */
  protected $_file = null;

  /**
   * FileMailer constructor.
   * @param null $file The JSON file name where messages are saved.
   * @throws TypeError if no $file provided and REST_MAILER_FILE is not configured
   */
  public function __construct( $file = null ) {

    if ($file === null) {

      if (!defined('REST_MAILER_FILE')) {
        throw new TypeError('You need to configure REST_MAILER_FILE');
      }

      $this->_file = REST_MAILER_FILE;

    } else {
      $this->_file = $file;
    }

  }

  /**
   * @noinspection PhpUnused
   * @param Message $msg
   * @return SentMessage
   * @throws MailError if could not save the mail to the file queue
   */
  public function send (Message $msg) {

    try {

      $sentMsg = new SentMessage($msg, false, false);

      Log::debug('Using file ' . $this->_file);

      $DATA = new SentMessagesEditableJSONFile($this->_file);

      $messages = $DATA->messages ?? array();
      array_push($messages, $sentMsg);
      $DATA->messages = $messages;

      $DATA->save();

      return $sentMsg;

    } catch (Exception $e) {
      throw new MailError($e->getMessage(), $e->getCode(), $e, $msg);
    }

  }

  /**
   * @noinspection PhpUnused
   * @param Message[] $messages
   * @return SentMessage[]
   * @throws MailError if could not save the mail to the file queue
   */
  public function sendMessages (array $messages) {

    try {

      $sentMessages = array_map(function($msg) {
        return new SentMessage($msg, false, false);
      }, $messages);

      $DATA = new SentMessagesEditableJSONFile($this->_file);

      $messages = $DATA->messages;
      array_push($messages, ...$sentMessages);
      $DATA->messages = $messages;

      $DATA->save();

      return $sentMessages;

    } catch (Exception $e) {
      throw new MailError($e->getMessage(), $e->getCode(), $e);
    }

  }

}
