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
abstract class BaseLock {

  /** True if file locked */
  private $locked = false;

  /** Lock file name (this is used to delete the lock file) */
  private $file = null;

  /** File handle for lock file */
  private $fp = null;

  /** Create a lock file */
  public function __construct ( $file ) {

    $this->file = $file;

  }

  /** Automatically releases the lock */
  public function __destruct () {

    $this->release();

  }

  /** Returns if lock is active */
  protected function isLocked () {
    return $this->locked;
  }

  /** Returns lock file name */
  protected function getFileName () {
    return $this->file;
  }

  /** Returns true if file handle is open */
  protected function isFileHandleOpen () {
    return $this->getFileHandle() !== null;
  }

  /** Nullifies the link file name. You should use this if you remove the file from disk. */
  protected function clearLinkFileName () {

    $this->file = null;

  }

  /** Try locking again, see lockFileHandle() below */
  protected function relockFileHandle ( $operation ) {

    $this->unlockFileHandle();

    $this->closeFileHandle();

    $this->openFileHandle();

    return $this->lockFileHandle($operation);

  }

  /** Returns true if lock file exists on filesystem
   *
   * Remember! You must use `clearstatcache()` first.
   */
  protected function lockFileExists () {

    return file_exists($this->file);

  }

  /** Get count of linked files on the filesystem
   *
   * Remember! You must use `clearstatcache()` first.
   */
  protected function isFileHandleLinked () {

    $fp = $this->getFileHandle();

    if ($fp === null) throw new Exception('File handle was not open');

    $fstat = fstat($fp);

    return $fstat['nlink'] >= 1;

  }

  /**
   * This is raw flock() which only sets $this->locked
   */
  protected function internalLockFileHandle ( $operation ) {

    $fp = $this->getFileHandle();

    if ($fp === null) throw new Exception('File handle not open');

    if (flock($fp, $operation)) {
      $this->locked = true;
      return TRUE;
    }

    return FALSE;

  }

  /** Locks the file handle and calls `$this->postLockFileHandle()`
   *
   * Note! This call will throw an exception if it cannot get a lock. Use internalLockFileHandle() if you want to use non-blocking flock() and call postLockFileHandle() yourself, if need to.
   *
   */
  protected function lockFileHandle ( $operation ) {

    if ( $this->internalLockFileHandle($operation) === FALSE ) {

      $this->closeFileHandle();

      throw new Exception('Could not get a lock for ' . $this->file);

    }

    $this->postLockFileHandle( $operation );

  }

  /** */
  protected function preUnlockFileHandle () {
  }

  /** Release the lock on file handle */
  protected function unlockFileHandle () {

    if ($this->locked) {

      $this->preUnlockFileHandle();

      $fp = $this->getFileHandle();

      if ($fp === null) throw new Exception('File handle not open');

      if ( flock($fp, LOCK_UN) === FALSE ) {
        syslog(LOG_WARNING, "Unlocking lock file failed.");
      }

      $this->locked = false;

    }

  }

  /** Returns lock file handle */
  protected function getFileHandle () {
    return $this->fp;
  }

  /** Open the file handle */
  protected function openFileHandle () {

    $this->fp = fopen($this->file, 'c+' );

    if ( $this->fp === FALSE ) {
      throw new Exception('Cannot open lock file!');
    }

  }

  /** Close file handle */
  protected function closeFileHandle () {

    $fp = $this->getFileHandle();

    if ( $fp !== null ) {

      if ( fclose($fp) === FALSE ) {
        syslog(LOG_WARNING, "fclose() failed for lock file.");
      }

      $this->fp = null;

    }

  }

  /** Called after flock() is called successfully */
  abstract protected function postLockFileHandle ($operation);

  /** Release lock if it is locked */
  abstract public function release ();

  /** Remove link file */
  protected function removeLinkFile () {

    $file = $this->getFileName();

    if ( $file !== null ) {

      if ( unlink($file) === FALSE ) {
        syslog(LOG_WARNING, "unlink() failed for lock file: " . $file);
      }

      $this->clearLinkFileName();

      clearstatcache();

    }

  }

}
