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

  /** Try locking again, see lockFileHandle() below */
  protected function relockFileHandle ( $operation ) {

    $this->unlockFileHandle();

    $this->closeFileHandle();

    $this->openFileHandle();

    return $this->lockFileHandle($operation);

  }

  /** Get file handle inode
   *
   * Remember! You must use `clearstatcache()` first.
   */
  static protected function getFileHandleInode ( $fp ) {

      $fstat = fstat($fp);

      return $fstat['ino'];

  }

  /** Truncates the file handle and writes our PID in it */
  protected function postLockFileHandle () {

      if ( ftruncate($this->fp, 0) === FALSE ) {
        throw new Exception('Failed to lock file handle: ftruncate failed');
      }

      if ( fwrite($this->fp, "" . getmypid() ) == FALSE ) {
        throw new Exception('Failed to lock file handle: fwrite failed');
      }

  }

  /** Locks the file handle and calls `$this->postLockFileHandle()` */
  protected function lockFileHandle ( $operation ) {

    if (flock($this->fp, $operation) === FALSE) {

      $this->closeFileHandle();

      throw new Exception('Could not get a lock for ' . $file);

    }

    // We must clear cache for file_exists() and fstat()
    clearstatcache();

    // Check if another process managed to get a lock while we were waiting and removed the lock file...
    if ( file_exists($this->file) === FALSE ) {
      return $this->relockFileHandle($operation);
    }

    // Check if another process managed to create a new different lock file with same name...
    if ( self::getFileHandleInode($this->fp) !== fileinode($this->file) ) {
      return $this->relockFileHandle($operation);
    }

    $this->locked = true;

    $this->postLockFileHandle();

  }

  /** Release the lock on file handle */
  protected function unlockFileHandle () {

    if ($this->locked) {

      if ( fflush($this->fp) === FALSE ) {
        // FIXME: Should print a warning
      }

      if ( flock($this->fp, LOCK_UN) === FALSE ) {
        // FIXME: Should print a warning
      }

      $this->locked = false;

    }

  }

  /** Open the file handle */
  protected function openFileHandle () {

    clearstatcache();

    $this->fp = fopen($this->file, file_exists($this->file) ? "r+" : "c" );

    if ( $this->fp === FALSE ) {
      throw new Exception('Cannot create lock file!');
    }

  }

  /** Close file handle */
  protected function closeFileHandle () {

    if ( $this->fp !== null ) {

      if ( fclose($this->fp) === FALSE ) {
        // FIXME: Should print a warning
      }

      $this->fp = null;

    }

  }

  /** Remove link file */
  protected function removeLinkFile () {

    if ( $this->file !== null ) {

      if ( unlink($this->file) === FALSE ) {
        // FIXME: Should print a warning
      }

      clearstatcache();

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
