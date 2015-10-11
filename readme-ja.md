
# PHP Source Generator

[![Build Status](https://travis-ci.org/jumilla/php-source-generator.svg)](https://travis-ci.org/jumilla/php-source-generator)
[![Quality Score](https://scrutinizer-ci.com/g/jumilla/php-source-generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jumilla/php-source-generator)
[![Code Coverage](https://scrutinizer-ci.com/g/jumilla/php-source-generator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jumilla/php-source-generator/)
[![Latest Stable Version](https://poser.pugx.org/jumilla/source-generator/v/stable.svg)](https://packagist.org/packages/jumilla/source-generator)
[![Total Downloads](https://poser.pugx.org/jumilla/source-generator/d/total.svg)](https://packagist.org/packages/jumilla/source-generator)
[![Software License](https://poser.pugx.org/jumilla/source-generator/license.svg)](https://packagist.org/packages/jumilla/source-generator)

## 使い方例

```php
<?php

use Jumilla\Generators\FileGenerator;

$generator = FileGenerator::make('outdir-path', 'stubdir-path');

# copy
$generator->sourceFile('Controller.php');

# generate in directory
$name = 'notification';
$generator->directory('Services', function ($generator) use ($name) {
    $generator->file(ucfirst($name).'Service.php')->template('Service.php');
});

```

## API

### blank file

```php
$generator->file('Class1.php')->blank();
```

### source file from string

```php
$generator->file('Class1.php')->text('## read');
```

### source file from stub

```php
$generator->sourceFile('Class1.php');
```

### source file from string (with arguments)

```php
$generator->file('Class2.php')->text('<?php class {$class_name} {}', [
    'class_name' => 'Class2',
]);
```

### source file from stub (with arguments)

```php
$generator->templateFile('Class2.php');
```

### json file

```php
$generator->json('Class2.php')->json([
    'foo' => 'FOO',
    'bar' => 'BaR',
]);
```

### .gitkeep file

```php
$generator->gitKeepFile();
```

### PHP blank file

```php
$generator->phpBlankFile('functions.php');
```

### PHP config file

```php
$generator->phpConfigFile('config.php', [
    'theme' => 'snow',
    'database' => [
        'default' => 'mysql',
    ],
]);
```

### PHP source file

```php
$generator->phpSourceFile('Controller.php', 'class Controller {}', 'App\Http\Controllers');
```

### get directory walker

```php
$sub = $generator->directory('app/Views');
$sub->sourceFile('layout.twig');
// ...
```

### directory walk in Closure

```php
$generator->directory('app/Views', function ($generator) {
    $generator->sourceFile('layout.twig');
    // ...
});
```

### sources in directory

```php
$generator->sourceDirectory(app/Models');
```

### templates in directory

```php
$generator->templateDirectory('app/Controllers', [
    ''
]);
```

### keep directory (use .gitkeep)

```php
$generator->keepDirectory('app/Services');
```

## 著者

古川 文生 / Fumio Furukawa (fumio@jumilla.me)

## ライセンス

MIT
