<?php

class SequenceTest extends \PHPUnit_Framework_TestCase
{
  public function testGet()
  {
    FactoryGirl\Sequence::resetAll();
    $this->assertEquals('hoge', FactoryGirl\Sequence::get('hoge'));
    $this->assertEquals('test_0', FactoryGirl\Sequence::get('test_{{sequence}}'));
    $this->assertEquals('test_1', FactoryGirl\Sequence::get('test_{{sequence}}'));
    $this->assertEquals('test_0', FactoryGirl\Sequence::get('test_{{sequence(:hoge)}}'));
  }
}