<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation for writing to files.
 *
 */
class WriteLock extends BaseLock {

  /**
   * WriteLock constructor.
   *
   * Create a lock file for writing files.
   *
   * @param $file
   * @throws Exception if cannot open file handle
   */
  public function __construct ( $file ) {

    parent::__construct( $file );

    $this->_openFileHandle();

    $this->_lockFileHandle(LOCK_EX);

  }

  /** Automatically releases the lock */
  public function __destruct () {

    parent::__destruct();

  }

  /**
   * Release lock if it is locked
   *
   * @throws Exception
   */
  public function release () {

    $this->_removeLinkFile();

    $this->_unlockFileHandle();

    $this->_closeFileHandle();

  }


  /**
   * Truncates the file handle and writes our PID in it
   *
   * @param string $operation
   * @throws Exception
   */
  protected function _postLockFileHandle ( $operation ) {

    // We must clear cache for lockFileExists() and isSameLockFile()
    clearstatcache();

    // Check if another process managed to get a lock while we were waiting and removed the lock file...
    if ( !$this->_lockFileExists() ) {
      $this->_relockFileHandle($operation);
      return;
    }

    // Check if another process managed to create a new different lock file with same name...
    if ( !$this->_isFileHandleLinked() ) {
      $this->_relockFileHandle($operation);
      return;
    }

    $this->_truncateFileHandle();

    $this->_writeToFileHandle( "" . getmypid() );

  }

  /**
   *
   */
  protected function _preUnlockFileHandle () {

      if ( fflush($this->_getFileHandle()) === FALSE ) {
        SimpleREST\Log\warning("fflush() failed for lock file.");
      }

  }

  /**
   * Truncate the file using file handle
   *
   * @throws Exception if file handle not found or ftruncate() fails
   */
  protected function _truncateFileHandle () {

    $fp = $this->_getFileHandle();

    if ($fp === null) throw new Exception('File handle not defined');

    if ( ftruncate($fp, 0) === FALSE ) {
      throw new Exception('Failed to truncate file handle');
    }

  }

  /**
   * Write data to file handle
   *
   * @param $content string Content to write
   * @throws Exception if file handle not found or fwrite() fails
   */
  protected function _writeToFileHandle ($content) {

    $fp = $this->_getFileHandle();

    if ($fp === null) throw new Exception('File handle not defined');

    if ( fwrite($fp, $content ) == FALSE ) {
      throw new Exception('Failed to write to link file handle');
    }

  }

}

