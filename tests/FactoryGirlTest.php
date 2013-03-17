<?php

class FactoryGirlTest extends PHPUnit_Framework_TestCase
{
  public function setup()
  {
    FactorySequence::resetAll();
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
   * @expectedException FactoryException
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
    $fooModel = $this -> getMock("Model", array("deleteAll")); 
    $bazModel = $this -> getMock("Model", array("deleteAll")); 
    Foo::$_model = $fooModel;
    Baz::$_model = $bazModel;

    $fooModel -> expects($this -> once())
      -> method("deleteAll");

    $foo = FactoryGirl::create("Foo");
    FactoryGirl::flush();

    // when flushing the second time, the previous classes should be removed
    $fooModel -> expects($this -> never())
      -> method("deleteAll");
    $bazModel -> expects($this -> once())
      -> method("deleteAll");

    $baz = FactoryGirl::create("Baz");
    FactoryGirl::flush();
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

}

class Baz {

  public static $_model;

  public function save()
  {
    return true;
  }

  public static function model()
  {
    return self::$_model;
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

