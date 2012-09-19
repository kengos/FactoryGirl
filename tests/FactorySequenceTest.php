<?php

class FactorySequenceTest extends PHPUnit_Framework_TestCase
{
  public function setup()
  {
    FactorySequence::resetAll();
  }

  public function testGet()
  {
    $this->assertEquals('hoge', FactorySequence::get('hoge'));
    $this->assertEquals('test_0', FactorySequence::get('test_{{sequence}}'));
    $this->assertEquals('test_1', FactorySequence::get('test_{{sequence}}'));
    $this->assertEquals('test_0', FactorySequence::get('test_{{sequence(:hoge)}}'));
  }
}