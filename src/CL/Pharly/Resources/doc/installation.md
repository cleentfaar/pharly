# Installation

## Step 1) Get the library

First you need to get a hold of the library. There are two ways of doing this:


### Method a) Using composer

Add the following to your ``composer.json`` (see http://getcomposer.org/)

    "require" :  {
        // ...
        "cleentfaar/pharly": "1.0.*@dev"
    }


### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/cleentfaar/pharly.git vendor/bundles/CL/Pharly
```

## Step 2) Register the namespaces

If you installed the library by composer, use the created autoload.php  (jump to step 3).
Add the following two namespace entries to the `registerNamespaces` call in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'CL\Pharly' => __DIR__.'/../vendor/cleentfaar/pharly/src',
    // ...
));
```


# Ready?

Check out the [usage documentation](usage.md)!
