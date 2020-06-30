<?php
/*
 * Copyright 2020 Sendanor <info@sendanor.fi>
 */
namespace SimpleREST\Mail;

require_once( dirname(dirname(dirname(__FILE__))) . '/File/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/File/JSON/index.php' );

use \SimpleREST\File\EditableJSON;

/**
 * Class SentMessagesEditableJSONFile
 *
 * Simple mailer which does not actually sent mail, just saves it on disk as a JSON file.
 *
 * @property SentMessage[] messages
 * @package SimpleREST\Mail
 */
class SentMessagesEditableJSONFile extends EditableJSON {


}
