<?php

defined('FACTORY_PATH') or define('FACTORY_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'factories');
defined('FACTORY_FILE_SUFFIX') or define('FACTORY_FILE_SUFFIX', 'Factory.php');
defined('SEQUENCE_PATH') or define('SEQUENCE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FactorySequence.php');
require_once(SEQUENCE_PATH);

/**
 * FactoryGirl is a fixtures replacement tool for PHP
 *
 * @copyright kengos
 * @author kengos
 * @license MIT
 * @link https://github.com/kengos/FactoryGirl
 */
class FactoryGirl
{
  public static $cache;
  public static $factoryPath = FACTORY_PATH;
  public static $fileSuffix = FACTORY_FILE_SUFFIX;

  /**
   * @return $class object (not saved)
   */
  public static function build($class, $args = array(), $alias = null)
  {
    return self::buildClass($class, $args, $alias);
  }

  /**
   * @return array $class object attributes
   */
  public static function attributes($class, $args = array(), $alias = null)
  {
    return self::buildAttributes($class, $args, $alias);
  }

  /**
   * @return $class object (saved)
   */
  public static function create($class, $args = array(), $alias = null)
  {
    $obj = self::buildClass($class, $args, $alias);
    if($obj->save())
      return $obj;
    else
      throw new FactoryException('Cannot Save ' . $class, $obj);
  }

  public static function clear()
  {
    self::$cache = array();
  }

  public static function setFactoryPath($path)
  {
    self::$factoryPath = $path;
  }

  public static function setFileSuffix($fileSuffix)
  {
    self::$fileSuffix = $fileSuffix;
  }

  protected static function buildClass($class, $args = array(), $alias = null)
  {
    $classAttr = self::getFactory($class);

    if(isset($classAttr['class']))
      $obj = new $classAttr['class'];
    else
      $obj = new $class;

    $attributes = self::buildAttributes($class, $args, $alias);
    foreach ($attributes as $key => $value) {
      $obj->$key = $value;
    }
    return $obj;
  }

  protected static function buildAttributes($class, $args = array(), $alias = null)
  {
    $classAttr = self::getFactory($class);
    $attributes = $classAttr['attributes'];
    if($alias!==null)
      $attributes = array_merge($attributes, $classAttr[$alias]);

    $attributes = array_merge($attributes, $args);
    foreach ($attributes as $key => $value) {
      $attributes[$key] = FactorySequence::get($value);
    }
    return $attributes;
  }

  protected static function getFactory($class)
  {
    if(self::$cache===null)
      self::$cache = array();

    if(!isset(self::$cache[$class]))
      self::$cache[$class] = require(self::$factoryPath . DIRECTORY_SEPARATOR . $class . self::$fileSuffix);

    return self::$cache[$class];
  }
}

class FactoryException extends Exception
{
  public $errorObject;

  public function __construct($message, $errorObject = null, $code = 0)
  {
    $this->errorObject = $errorObject;
    parent::__construct($message, $code);
  }
}