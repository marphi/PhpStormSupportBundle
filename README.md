MarphiPhpStormSupportBundle
========================

The bundle provides support to complement the console for Symfony2 in PhpStorm.

**Instalation**

Add the following lines in your `deps` file:

```
[MarphiPhpStormSupportBundle]
    git=git://github.com/marphi/PhpStormSupportBundle.git
    target=bundles/Marphi/PhpStormSupportBundle
```

Run the vendors install script:

``` bash
$ php bin/vendors install
```


**Configuration**

Add the `Marphi` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Marphi' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Marphi\PhpStormSupportBundle\MarphiPhpStormSupportBundle(),
    );
}
```


**Generate config file**

In project directory run script:

``` bash
$ php app/console phpstorm:command:config > .idea/commandlinetools/Symfony2.xml
```

Now you must restart PhpStorm. When you add new bundles to your project you must execute again command to refresh config file.




**Usage**

Run Command Line Tools:  SHIF+CTRL+X or Tools->Run Command...

![Command Line Tools](http://marphi.net/img/empty_command_line_tools_phpstorm-2.png)


Start typing

![Complement console of Symfony2 in PHPStorm](http://marphi.net/img/symfony2_command_line_tool_phpstorm-3.png)






