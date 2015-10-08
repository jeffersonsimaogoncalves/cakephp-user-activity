[![Latest Stable Version](https://poser.pugx.org/crabstudio/ace/v/stable)](https://packagist.org/packages/crabstudio/ace) [![Total Downloads](https://poser.pugx.org/crabstudio/ace/downloads)](https://packagist.org/packages/crabstudio/ace) [![Latest Unstable Version](https://poser.pugx.org/crabstudio/ace/v/unstable)](https://packagist.org/packages/crabstudio/ace) [![License](https://poser.pugx.org/crabstudio/ace/license)](https://packagist.org/packages/crabstudio/ace)

# CakePHP 3 Ace bake template, integrated with [ACE theme](https://wrapbootstrap.com/theme/ace-responsive-admin-template-WB0B30DGR)

## Introduction
This plugin integrated with ACE responsive theme, breadcrumb automatic add to your view.


**I just hold single license, [you must buy ACE theme license for your project](https://wrapbootstrap.com/theme/ace-responsive-admin-template-WB0B30DGR)**

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require crabstudio/ace
```
Or add the following lines to your application's **composer.json**:

```
"require": {
    "crabstudio/ace": "dev-master"
}
```
followed by the command:

```
composer update
```

## Load plugin

Add this line to **Your_project\config\bootstrap.php**
```
Plugin::load('Crabstudio/Ace', ['bootstrap' => true]);
```
## Use backend layout

Your must copy all files, folders in **Crabstudio/Ace/webroot/** to **your_project/webroot**

Copy **Crabstudio/Ace/src/Template/Layout/backend.ctp** to **your_project/src/Template/Layout/**

Copy all files in folder **Crabstudio/Ace/src/Template/Element/** to **your_project/src/Template/Layout/Element/**

Then in your controller, set layout
```
$this->_viewBuilder->layout('backend');
```

## Bake template
Windows:

```
your_project>bin\cake bake template Users --theme Crabstudio/Ace
```

Linux, Unix:
```
your_project>bin/cake bake template Users --theme Crabstudio/Ace
```

## Bonus more offer
This plugin provide some global function and you can call from anywhere:

```
format_date($time, $timezone, $format);
str_slug($source);
SluggableBehavior
```
### Usage format_date function

```
/**
 * Format date use Cake\I18n\Time class with default timezone "Asia/Tokyo"
 * and default format "yyyy/MM/dd HH:mm:ss"
 * 
 * @param date $time
 * @param string $timezone
 * @param string $format
 * @return string
 */
Ex:
$formatedDate = format_date($user->created_at);
echo $formatedDate; //2015/09/24 03:00:00

$formatedDate = format_date($user->created_at, 'Asia/Bangkok');
echo $formatedDate; //2015/09/24 01:00:00

$formatedDate = format_date($user->created_at, 'Asia/Bangkok', 'HH:mm:ss dd/MM/yyyy');
echo $formatedDate; //01:00:00 24/09/2015

```

### Usage str_slug function

```
/**
 * Do unsigned utf-8 characters and make friendly-link-like-this
 * 
 * @param string $source
 * @return string
 */

echo str_slug('Nguyễn Anh Tuấn'); //nguyen-anh-tuan
```

### Usage SluggableBehavior

In your Model Table, insert this one into function **initialize**:

```
/**
 * Do unsigned utf-8 characters and make friendly-link-like-this
 * 
 * @param string $source name of field hold source string
 * @param string $replacement name of field will store slugged string
 * @return string
 */

$this->addBehavior('Crabstudio/Ace.Sluggable', [
    'field' => 'title',
    'slug' => 'slug',
]);
```

## Demo result
![Index page](http://i.imgur.com/ng4YbG0.png)
![Add page](http://i.imgur.com/KhR9ivc.png)
![Edit page](http://i.imgur.com/sZng73r.png)
![View page](http://i.imgur.com/lRUxuI9.png)