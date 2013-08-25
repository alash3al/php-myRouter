php-myRouter
============

It's Convert Every Thing to /index.php/class/method/parm1/parm2/parm3/parm4/parm5/parm6/..... unlimited params


How to use ?
==============

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?php

  // load the library
  include_once('myRouter.php');
  
  // Set The Controllers(classes) Directory
  $C_Dir = 'controllers';
  
  // Set The Home Page (Home Controller)
  $H_Cont = 'home';
  
  // Set The Controllers Extension
  $C_Ext = 'php';
  
  // Construct it !
  $myRouter = new MyRouter($C_DIR, $H_Con, $C_Ext);

?>
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


How it works ?
==============
an example to show you how it works :

create folder called 'testRouter'

create folder 'testRouter/controllers/'

create file 'testRouter/controllers/home.php'

create file 'testRouter/index.php'

include the library file of 'myRouter.php' in 'index.php' and do the same we done before 'How to use ?'

in 'testRouter/controllers/home.php' do as following 

~~~~~~~~~~~~~~
<?php
  class home{
  
    // Yes The Class Name same as the file name without extension
    
    function say_hello()
    {
      echo 'hello!';
    }
    
    function say_something($something = '')
    {
      $s = func_get_args();
      foreach($s as $something) {
        echo $something . '<br />';
      }
    }
  
  }
?>
~~~~~~~~~~~~~~

then when you call 'yourHost/testRouter' it will automaticaly redirect to 'yourHost/testRout/index.php/'
and the home controller will be called as we set it in the 'How to use ?' step .

but if you go 'yourHost/testRout/index.php/home/' it will also call the home controller

but 'yourHost/testRout/index.php/home/say_hello' it will call the home controller and then call the
say_hello() method and show you 'hello!'

but 'yourHost/testRout/index.php/home/say_something/something1/some2/some3/some4/some5'
you will see 

~~~~~~
something1
some2
some3
some4
some5
~~~~~~

But How About removing '/index.php/' ;) ?
==========================================
Just set You htaccess to redirect all requests to index.php/

and add this line
~~~~~~~~~~~~~
setEnv HTACCESS_MOD_REWRITE on
~~~~~~~~~~~~~

That's all :)
