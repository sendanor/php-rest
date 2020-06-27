<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation
 *
 */
class Lock {

  /** Lock file name (this is used to delete the lock file) */
  private $file = null;

  /** File handle for lock file */
  private $fp = null;

  /** True if file locked */
  private $locked = false;

  /** Create a lock file */
  public function __construct ( $file ) {

    $this->file = $file;

    $this->openFileHandle();

    $this->lockFileHandle(LOCK_EX);

  }

  /** Locks the file handle */
  protected function lockFileHandle ($operation = LOCK_EX) {

    if (flock($this->fp, $operation)) {

      // Check if another process managed to get a lock while we were waiting and removed the lock file...
      if (!file_exists($this->file)) {

	// Release lock and try again
        $this->unlockFileHandle();
        $this->closeFileHandle();

        $this->openFileHandle();

        return $this->lockFileHandle($operation);

      }

      ftruncate($this->fp, 0);

      fwrite($this->fp, "" . getmypid() );

      $this->locked = true;

    } else {

      fclose($this->fp);

      $this->fp = null;

      throw new Exception('Could not get a lock for ' . $file);

    }

  }

  /** Release the lock on file handle */
  protected function unlockFileHandle () {

    if ($this->locked) {

      fflush($this->fp);
      flock($this->fp, LOCK_UN);
      $this->locked = false;

    }

  }

  /** Open the file handle */
  protected function openFileHandle () {

    $this->fp = fopen($this->file, file_exists($this->file) ? "r+" : "c" );

    if (!$this->fp) {
      throw new Exception('Cannot create lock file!');
    }

  }

  /** Close file handle */
  protected function closeFileHandle () {

    if ($this->fp) {
      fclose($this->fp);
      $this->fp = null;
    }

  }

  /** Remove link file */
  protected function removeLinkFile () {

    if ($this->file) {
      unlink($this->file);
      $this->file = null;
    }

  }

  /** Release lock if it is locked */
  public function release () {

    $this->removeLinkFile();
    $this->unlockFileHandle();
    $this->closeFileHandle();

  }

  /** Automatically releases the lock */
  public function __destruct () {

    $this->release();

  }

}
