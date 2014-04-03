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
//        $oParse = new \ParseCloud\parseQuery('News');
        
       # print "<pre> -------------- ";
      #  print_r($oNumeroRand->find());
      #  print "--------------  </pre>";
         
        $parseCat = new \ParseCloud\parseObject('Categoria');
        $parseCat->title = "PHP";
        
        $parse = new \ParseCloud\parseObject('Noticia');
        $parse->title = "Noticia 1";
        $parse->description = "Descrição";
        $parse->parent = $parseCat->save()->objectId;
        $parse->save();
        
        return new ViewModel();
         
    }
     public function newsAction()
    {
        $oNumeroRand = new \ParseCloud\parseQuery('NumeroRand');
       
        return new ViewModel();
         
    }
}
