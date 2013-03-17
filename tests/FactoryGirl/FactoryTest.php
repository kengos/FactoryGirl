<?php

use FactoryGirl\Factory as FactoryGirl;

class FactoryTest extends PHPUnit_Framework_TestCase
{
  public function setup()
  {
    FactoryGirl::setup(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'factories');
    FactoryGirl::resetAll();
  }

  public function testCreate()
  {
    $foo = FactoryGirl::create('Foo');
    $this->assertEquals('name_0', $foo->name);
    $this->assertEquals(100, $foo->code);

    $foo = FactoryGirl::create('Foo', array('name' => 'bar'));
    $this->assertEquals('bar', $foo->name);
  }

  /**
   * save method returns false, throw FactoryException
   * @expectedException FactoryGirl\FactoryException
   */
  public function testCreateFailure()
  {
    FactoryGirl::create('Bar');
  }

  public function testBuild()
  {
    $foo = FactoryGirl::build('Foo');
    $this->assertEquals('name_0', $foo->name);
    $this->assertEquals(100, $foo->code);
  }

  public function testAttributes()
  {
    $foo = FactoryGirl::attributes('Foo');
    $this->assertEquals('name_0', $foo['name']);
    $this->assertEquals(100, $foo['code']);
  }

  public function testAlias()
  {
    $foo = FactoryGirl::build('Foo', array(), 'baz');
    $this->assertEquals('foo_bar_baz', $foo->name);
    $this->assertEquals(200, $foo->code);
  }

  public function testFlush()
  {
    $foo = FactoryGirl::create("Foo");
    $model = $this -> getMock("Model", array("deleteAll"));
    $model -> expects($this -> once())
      -> method("deleteAll");
    Foo::$_model = $model;

    FactoryGirl::flush();
  }

  /**
   * test defineFactory callback & another save method
   */
  public function testCanUseAnotherSaveMethod()
  {
    $callback = function($attrs) {
      $attrs['save'] = array('generate', 'aaaa', 'bbbb');
      return $attrs;
    };
    FactoryGirl::defineFactory('Foobar', 'Foo', ['code' => 'defineCode'], $callback);
    $foobar = FactoryGirl::create('Foobar');
    $this->assertEquals('aaaa', $foobar->name);
    $this->assertEquals('bbbb', $foobar->code);
  }

  public function testDefineFactory()
  {
    FactoryGirl::defineFactory('Foobar', 'Foo', ['code' => 'defineCode', 'name' => 'define_{{sequence}}']);
    $foobar = FactoryGirl::create('Foobar');
    $this->assertTrue($foobar instanceof Foo);
    $this->assertEquals('defineCode', $foobar->code);
    $this->assertEquals('define_0', $foobar->name);
  }
}

class Foo
{
  public $name;
  public $code;

  public static $_model;

  public function save()
  {
    return true;
  }

  public static function model()
  {
    return self::$_model;
  }

  public function generate($name, $code)
  {
    $this->name = $name;
    $this->code = $code;
    return $this->save();
  }
}

class Model {
  function deleteAll(){}
}

class Bar
{
  public $name;

  public function save()
  {
    return false;
  }
}