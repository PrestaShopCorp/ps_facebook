<?php

class AdminPsfacebookModuleController extends ModuleAdminController
{
    public function postProcess()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink(
                'AdminModules',
                true,
                [],
                [
                    'configure' => 'ps_facebook',
                ]
            )
        );
    }
}
