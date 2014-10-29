<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Result;
class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /**
         * @var $authAdapter AuthenticationServiceInterface
         */
        $authAdapter = $this->getServiceLocator()->get('auth');
        /**
         * @var $result Result
         */
        $result = $authAdapter->authenticate();
        if (! $result->isValid()) {
            $this->redirect('/');
        }
        // @todo set session key to identify user to our restriced controllers

        $this->redirect('/accountArea');

    }
}
