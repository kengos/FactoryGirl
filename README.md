# FactoryGirlPhp

FactoryGirl is a fixtures replacement tool for Yii framework

Like Ruby gem `factory_girl`

## Install

Download releases/factory_girl_0.1.0.phar

## Setup

In Your bootstrap.php

```
require_once(DOWNLOAD_PATH . '/factory_girl_0.1.0.phar');
use FactoryGirl\Factory as FactoryGirl;
$factoryPaths = ['foo/bar/factories', 'bar/baz/factories'];
FactoryGirl::setup($factoryPaths);
```

## Usage

```
FactoryGirl::build('User')

FactoryGirl::create('User')

FactoryGirl::attributes('User')
```

## Factory file format

```
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

// after each test case
FactoryGirl::flush(); // remove created records
```

more details see `tests/FactoryGirl/FactoryTest.php`

## FactoryGirl Sequence

```
<?php

return array(
  'class' => 'Foo',
  'attributes' => array(
    'name' => 'bar_{{sequence}}',
  ),
);
?>
```

```
FactoryGirl::build('Foo')->name // -> bar_0
FactoryGirl::build('Foo')->name // -> bar_1
```

more details see `tests/FactorySequenceTest.php`

## Development



## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Added some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
