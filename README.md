# FactoryGirlPhp

FactoryGirl is a fixtures replacement tool for Yii framework

Like Ruby gem `factory_girl`

## Install

Download FacotyGirl.tgz or `git clone git://github.com/kengos/FactoryGirl.git protected/extensions/`

## Usage

````
FactoryGirl::build('User')

FactoryGirl::create('User')

FactoryGirl::attributes('User')
````

## Factory file format

````
<?php
// FileName UserFactory.php
return array(
  'class' => 'User', // -> new User
  'attributes' => array(
    'name' => 'xxxx', // $user->name = 'xxxx'
    'permission' => 'default', // $user->permission = 'default'
  ),
  'admin' => array(
    'name' => 'admin',
    'permission' => 'admin' // $user->permission = 'admin'
  )
);

?>

// In Your tests
$user = FactoryGirl::create('User')
$user->permission; // -> 'default'

$user = FactoryGirl::create('User', array('permission'->'admin'));
$user->permission; // -> 'admin'

$admin = FactoryGirl::create('User', array(), 'admin');
$admin->permission; // -> 'admin'
````

## FactoryGirl Sequence

```
<?php

return array(
  'class' => 'Foo',
  'attributes' => array(
    'name' => 'bar_{{sequence}}',
  ),
);
```

```
FactoryGirl::build('Foo')->name // -> bar_0
FactoryGirl::build('Foo')->name // -> bar_1
```

## Features

* Support association
