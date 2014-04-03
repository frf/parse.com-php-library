PHP parse.com API library - Zend Framework 2
===========================
More on the parse.com api here: https://www.parse.com/docs/rest

### V1 beta ###
https://github.com/frf/parse.com-php-library/vendor

### Feedback Wanted ###

Se tiver idéias ou dúvidas entre em contato


SETUP
=========================

### Arquivos em example Module.php e IndexController.php ###


Alterar as configurações na class **parseRestClient.php**

### sample of parseRestClient.php ###

```
<?php

namespace ParseCloud;

class parseRestClient{

        const APPID = '';
        const MASTERKEY = '';
        const RESTKEY = '';
        const PARSEURL = 'https://api.parse.com/1/';
    


```

### Após configurar suas chaves adicionar no módulo da sua aplicação ###


### Mover este arquivo Module.php para o módulo da sua aplicação ./module/Application/Module.php, ou copiar apenas a linha do ParseCloud ### 

```

 public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'ParseCloud' => __DIR__ . '/../../vendor/parse',
                ),
            ),
        );
    }

    
```


EXAMPLE DE UMA ACTION
=========================
### Mover este arquivo Module.php para o módulo da sua aplicação ./module/Application/Module.php ### 
### sample of IndexController.php ###

```

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;
use \Zend\Http\Client;
use \Zend\Http\Client\Adapter\Curl;
use ParseCloud\parseRestClient;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        $parseCat = new \ParseCloud\parseObject('Categoria');
        $parseCat->title = "PHP";
        
        $parse = new \ParseCloud\parseObject('Noticia');
        $parse->title = "Noticia 1";
        $parse->description = "Descrição";
        $parse->parent = $parseCat->save()->objectId;
        $parse->save();
        
        return new ViewModel();
         
    }


```

