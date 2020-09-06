<?php

namespace Croogo\Users\Controller\Admin;

use Cake\Event\Event;

/**
 * Roles Controller
 *
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RolesController extends AppController
{
    public $modelClass = 'Croogo/Users.Roles';

    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Roles->displayFields()
        ]);
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            $event->getSubject()->query
                ->find('roleHierarchy')
                ->order(['ParentAro.lft' => 'DESC']);
        });

        return $this->Crud->execute();
    }
}
