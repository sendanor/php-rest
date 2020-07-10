<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */
declare(strict_types=1);

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation
 *
 */
abstract class BaseLock {

  /** True if file locked */
  private $_locked = false;

  /** Lock file name (this is used to delete the lock file) */
  private $_file;

  /**
   * File handle
   *
   * @var resource|false|null
   */
  private $_fp = null;


  /**
   * BaseLock constructor.
   *
   * @param $file string
   */
  public function __construct ( $file ) {

    $this->_file = $file;

  }

  /** Automatically releases the lock */
  public function __destruct () {

    $this->release();

  }

  /** Release lock if it is locked */
  abstract public function release ();


  /**
   * Returns if lock is active
   *
   * @return bool
   */
  protected function _isLocked () {
    return $this->_locked;
  }

  /**
   * Returns lock file name
   *
   * @return string
   */
  protected function _getFileName () {
    return $this->_file;
  }

  /**
   * Returns true if file handle is open
   *
   * @return bool
   */
  protected function _isFileHandleOpen () {
    return $this->_getFileHandle() !== null;
  }

  /** Nullifies the link file name. You should use this if you remove the file from disk. */
  protected function _clearLinkFileName () {

    $this->_file = null;

  }

  /**
   * Try locking again, see lockFileHandle() below
   *
   * @param $operation string
   * @throws Exception if file handle not defined
   */
  protected function _relockFileHandle ( $operation ) {

    $this->_unlockFileHandle();

    $this->_closeFileHandle();

    $this->_openFileHandle();

    $this->_lockFileHandle($operation);

  }

  /**
   * Returns true if lock file exists on filesystem
   *
   * Remember! You must use `clearstatcache()` first.
   *
   * @return bool
   */
  protected function _lockFileExists () {

    return file_exists($this->_file);

  }

  /** Get count of linked files on the filesystem
   *
   * Remember! You must use `clearstatcache()` first.
   *
   * @return bool
   * @throws Exception if file handle not defined
   */
  protected function _isFileHandleLinked () {

    $fp = $this->_getFileHandle();

    if ($fp === null) throw new Exception('File handle was not open');

    $fstat = fstat($fp);

    return $fstat['nlink'] >= 1;

  }

  /**
   * This is raw flock() which only sets $this->_locked
   *
   * @param $operation string
   * @return bool
   * @throws Exception if file handle not defined
   */
  protected function _internalLockFileHandle ( $operation ) {

    $fp = $this->_getFileHandle();

    if ($fp === null) throw new Exception('File handle not open');

    if (flock($fp, $operation)) {
      $this->_locked = true;
      return TRUE;
    }

    return FALSE;

  }

  /** Locks the file handle and calls `$this->_postLockFileHandle()`
   *
   * Note! This call will throw an exception if it cannot get a lock. Use internalLockFileHandle() if you want to use non-blocking flock() and call postLockFileHandle() yourself, if need to.
   *
   * @param $operation string
   * @throws Exception if file handle not defined
   */
  protected function _lockFileHandle ( $operation ) {

    if ( $this->_internalLockFileHandle($operation) === FALSE ) {

      $this->_closeFileHandle();

      throw new Exception('Could not get a lock for ' . $this->_file);

    }

    $this->_postLockFileHandle( $operation );

  }

  /** */
  protected function _preUnlockFileHandle () {
  }

  /**
   * Release the lock on file handle
   *
   * @throws Exception if file handle not defined
   */
  protected function _unlockFileHandle () {

    if ($this->_locked) {

      $fp = $this->_getFileHandle();

      if ($fp === null) throw new Exception('File handle not open');

      $this->_preUnlockFileHandle();

      if ( flock($fp, LOCK_UN) === FALSE ) {
        SimpleREST\Log\warning("Unlocking lock file failed.");
      }

      $this->_locked = false;

    }

  }

  /** Returns lock file handle
   *
   * @return resource|null
   */
  protected function _getFileHandle () {
    return $this->_fp;
  }

  /**
   * Open the file handle
   *
   * @throws Exception if cannot open lock file
   */
  protected function _openFileHandle () {

    $fp = fopen($this->_file, 'c+' );

    if ( $fp === FALSE ) {
      throw new Exception('Cannot open lock file!');
    }

    $this->_fp = $fp;

  }

  /** Close file handle */
  protected function _closeFileHandle () {

    $fp = $this->_getFileHandle();

    if ( $fp !== null ) {

      if ( fclose($fp) === FALSE ) {
        SimpleREST\Log\warning("fclose() failed for lock file.");
      }

      $this->_fp = null;

    }

  }

  /**
   * Called after flock() is called successfully
   *
   * @param $operation string
   * @return mixed
   */
  abstract protected function _postLockFileHandle ($operation);

  /** Remove link file */
  protected function _removeLinkFile () {

    $file = $this->_getFileName();

    if ( $file !== null ) {

      if ( unlink($file) === FALSE ) {
        SimpleREST\Log\warning("unlink() failed for lock file: " . $file);
      }

      $this->_clearLinkFileName();

      clearstatcache();

    }

  }

}
