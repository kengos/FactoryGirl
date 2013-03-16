<?php

namespace FactoryGirl;

/**
 * FactoryGirl is a fixtures replacement tool for PHP
 *
 * @copyright kengos
 * @author kengos
 * @license MIT
 * @link https://github.com/kengos/FactoryGirl
 */
class Factory
{
  public static $cache;
  public static $factoryPaths;
  public static $fileSuffix;

  private static $createdClasses = array();

  public static function setup($factoryPaths, $fileSuffix = 'Factory.php')
  {
    self::$factoryPaths = array();
    if(is_string($factoryPaths))
      self::$factoryPaths[] = $factoryPaths;
    elseif(is_array($factoryPaths))
      self::$factoryPaths = $factoryPaths;
    self::$fileSuffix = $fileSuffix;
  }

  public static function resetSequence()
  {
    \FactoryGirl\Sequence::resetAll();
  }

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
    if($obj->save()) {
      self::$createdClasses[$class] = true;
      return $obj;
    } else {
      throw new FactoryException('Cannot Save ' . $class, $obj);
    }
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

    $obj = isset($classAttr['class']) ? new $classAttr['class'] : new $class;
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
      $attributes[$key] = \FactoryGirl\Sequence::get($value);
    }
    return $attributes;
  }

  protected static function getFactory($class)
  {
    if(self::$cache===null)
      self::$cache = array();

    if(isset(self::$cache[$class]))
      return self::$cache[$class];

    foreach (self::$factoryPaths as $path)
    {
      $file = $path . DIRECTORY_SEPARATOR . $class . self::$fileSuffix;
      if(file_exists($file))
        return self::$cache[$class] = require($file);
    }
    throw new \FactoryGirl\FactoryException('Not found factory file: ' . $class . self::$fileSuffix);
  }

  public static function flush()
  {
    foreach (self::$createdClasses as $className => $value) {
      $className::model() -> deleteAll();
    }
  }
}

class FactoryException extends \Exception
{
  public $errorObject;

  public function __construct($message, $errorObject = null, $code = 0)
  {
    $this->errorObject = $errorObject;
    parent::__construct($message, $code);
  }
}
