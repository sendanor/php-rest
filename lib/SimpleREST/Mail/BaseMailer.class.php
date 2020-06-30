<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

/**
 * Abstract Class Mailer
 *
 * Base class for our mailers.
 *
 * @package SimpleREST\Mail
 */
abstract class BaseMailer {

  /**
   * @param Message $msg
   * @return SentMessage
   * @throws MailError if cannot send the mail
   */
  abstract public function send (Message $msg);

  /**
   * @param Message[] $msgs
   * @return SentMessage[]
   */
  abstract public function sendMessages (array $msgs);

}
