<?php

defined('FACTORY_PATH') or define('FACTORY_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'factories');
defined('FACTORY_FILE_SUFFIX') or define('FACTORY_FILE_SUFFIX', 'Factory.php');

/**
 * FactoryGirl is a fixtures replacement tool
 *   For Yii framework 1.x
 *
 * @copyright kengos
 * @author kengos
 * @license http://www.yiiframework.com/license/
 */
class FactoryGirl
{
  protected static $cache;
  protected static $factoryPath = FACTORY_PATH;
  protected static $fileSuffix = FACTORY_FILE_SUFFIX;

  /**
   * @return $class object (not saved)
   */
  public static function build($class, $args = array(), $alias = null)
  {
    return self::buildClass($class);
  }

  /**
   * @return array $class object attributes
   */
  public static function attributes_for($class, $args = array(), $alias = null)
  {
    return self::buildClass($class)->getAttributes();
  }

  /**
   * @return $class object (saved)
   */
  public static function create($class, $args = array(), $alias = null)
  {
    $obj = self::buildClass($class);
    if($obj->save())
      return $obj;
    else
      throw new Exception('Cannot Save ' . $class);
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
    if(self::$cache===null)
      self::$cache = array();

    if(!isset(self::$cache[$class]))
      self::$cache[$class] = require(self::getFactory($class));

    $classAttr = self::$cache[$class];
    $obj = new $classAttr['class'];
    if($alias===null)
      $attributes = CMap::mergeArray($classAttr['attributes'], $args);
    else
      $attributes = CMap::mergeArray($classAttr[$alias]['attributes'], $args);

    $obj->setAttributes($attributes, false);
    return $obj;
  }

  protected static function getFactory($class)
  {
    return $self::$factoryPath . DIRECTORY_SEPARATOR . $class . self::$fileSuffix;
  }
}