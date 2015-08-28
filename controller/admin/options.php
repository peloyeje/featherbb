<?php

/**
 * Copyright (C) 2015 FeatherBB
 * based on code by (C) 2008-2012 FluxBB
 * and Rickard Andersson (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

namespace controller\admin;

class options
{
    public function __construct()
    {
        $this->feather = \Slim\Slim::getInstance();
        $this->start = $this->feather->start;
        $this->config = $this->feather->config;
        $this->user = $this->feather->user;
        $this->request = $this->feather->request;
        $this->model = new \model\admin\options();
        load_textdomain('featherbb', $this->feather->forum_env['FEATHER_ROOT'].'lang/'.$this->user->language.'/admin/options.mo');
    }

    public function __autoload($class_name)
    {
        require FEATHER_ROOT . $class_name . '.php';
    }

    public function display()
    {
        if ($this->feather->request->isPost()) {
            $this->model->update_options();
        }

<<<<<<< HEAD
        generate_admin_menu('options');
=======
        \FeatherBB\AdminUtils::generateAdminMenu('options');
>>>>>>> development

        $this->feather->view2->setPageInfo(array(
                'title' => array(feather_escape($this->config['o_board_title']), __('Admin'), __('Options')),
                'active_page' => 'admin',
                'admin_console' => true,
                'languages' => forum_list_langs(),
                'styles' => $this->model->get_styles(),
                'times' => $this->model->get_times(),
            )
        )->addTemplate('admin/options.php')->display();
    }
}
