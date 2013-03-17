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
  protected static $_cache = array();
  protected static $_factoryPaths = array();
  protected static $_fileSuffix = 'Factory.php';
  protected static $_createdClasses = array();
  protected static $_definitions = array();


  public static function setup($factoryPaths, $fileSuffix = null)
  {
    self::setFactoryPaths($factoryPaths);
    if(is_string($fileSuffix))
      self::setFileSuffix($fileSuffix);
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
    $classAttr = self::getFactory($class);
    if(isset($classAttr['save']))
    {
      $saveArgs = $classAttr['save'];
      $saveMethod = array_shift($saveArgs);
      if(count($saveArgs) > 0 && $obj->{$saveMethod}($saveArgs) || $obj->{$saveMethod}())
      {
        self::$_createdClasses[$class] = true;
        return $obj;
      }
    }
    elseif($obj->save())
    {
      self::$_createdClasses[$class] = true;
      return $obj;
    }

    throw new FactoryException('Cannot Save ' . $class, $obj);
  }

  public static function defineFactory($name, $class, $attributes, $callback = null)
  {
    $classAttr = array();
    $classAttr['class'] = $class;
    $classAttr['attributes'] = $attributes;
    if(is_callable($callback))
      $classAttr = $callback($classAttr);

    self::$_definitions[$name] = $classAttr;
  }

  protected static function buildClass(&$class, &$args, &$alias)
  {
    $classAttr = self::getFactory($class);

    $obj = isset($classAttr['class']) ? new $classAttr['class'] : new $class;
    $attributes = self::buildAttributes($class, $args, $alias);
    foreach ($attributes as $key => $value) {
      $obj->$key = $value;
    }
    return $obj;
  }

  protected static function buildAttributes(&$class, &$args, &$alias)
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

  protected static function getFactory(&$class)
  {
    if(isset(self::$_definitions[$class]))
      return self::$_definitions[$class];

    if(isset(self::$_cache[$class]))
      return self::$_cache[$class];

    foreach (self::$_factoryPaths as $path)
    {
      $file = $path . DIRECTORY_SEPARATOR . $class . self::$_fileSuffix;
      if(file_exists($file))
        return self::$_cache[$class] = require($file);
    }
    throw new \FactoryGirl\FactoryException('Not found factory file: ' . $class . self::$_fileSuffix);
  }

  public static function flush()
  {
    foreach (self::$_createdClasses as $className => $value) {
      $className::model() -> deleteAll();
    }
    self::$_createdClasses = array();
  }

  public static function resetAll()
  {
    self::$_cache = array();
    self::resetCreatedClasses();
    self::resetDefinitions();
    self::resetSequence();
  }

  public static function resetSequence()
  {
    \FactoryGirl\Sequence::resetAll();
  }

  public static function resetDefinitions()
  {
    self::$_definitions = array();
  }

  public static function resetCreatedClasses()
  {
    self::$_createdClasses = array();
  }

  public static function setFactoryPaths($path)
  {
    if(is_string($path))
      self::$_factoryPaths[] = $path;
    elseif(is_array($path))
      self::$_factoryPaths = $path;
  }

  public static function setFileSuffix($fileSuffix)
  {
    self::$_fileSuffix = $fileSuffix;
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
