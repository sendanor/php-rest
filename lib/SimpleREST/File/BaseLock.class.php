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

  /** Returns if lock is active */
  protected function isLocked () {
    return $this->locked;
  }

  /** Returns lock file name */
  protected function getFileName () {
    return $this->file;
  }

  /** Nullifies the link file name. You should use this if you remove the file from disk. */
  protected function clearLinkFileName () {

    $this->file = null;

  }

  /** Returns lock file handle */
  protected function getFileHandle () {
    return $this->fp;
  }

  /** Create a lock file */
  public function __construct ( $file ) {

    $this->file = $file;

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

  /** Get file handle inode
   *
   * Remember! You must use `clearstatcache()` first.
   */
  protected function getFileHandleInode () {

    $fstat = fstat($this->fp);

    return $fstat['ino'];

  }

  /** Get file inode
   *
   * Remember! You must use `clearstatcache()` first.
   */
  protected function getFileInode () {

    return fileinode($this->file);

  }

  /** Returns true if lock file is same file as file pointer */
  protected function isSameLockFile () {

    return $this->getFileHandleInode() === $this->getFileInode();

  }

  /** Locks the file handle and calls `$this->postLockFileHandle()` */
  protected function lockFileHandle ( $operation ) {

    if (flock($this->fp, $operation) === FALSE) {

      $this->closeFileHandle();

      throw new Exception('Could not get a lock for ' . $this->file);

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

    // We must clear cache for lockFileExists() and isSameLockFile()
    clearstatcache();

    $this->fp = fopen($this->file, $this->lockFileExists() ? 'w+' : 'c+' );

    if ( $this->fp === FALSE ) {
      throw new Exception('Cannot open lock file!');
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

  /** Called after flock() is called successfully */
  abstract protected function postLockFileHandle ();

  /** Release lock if it is locked */
  abstract public function release ();

  /** Automatically releases the lock */
  public function __destruct () {

    $this->release();

  }

  /** Remove link file */
  protected function removeLinkFile () {

    $file = $this->getFileName();

    if ( $file !== null ) {

      if ( unlink($file) === FALSE ) {
        // FIXME: Should print a warning
      }

      $this->clearLinkFileName();

      clearstatcache();

    }

  }

}
