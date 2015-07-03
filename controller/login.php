<?php

/**
 * Copyright (C) 2015 FeatherBB
 * based on code by (C) 2008-2012 FluxBB
 * and Rickard Andersson (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

namespace controller;

class login
{
    public function __construct()
    {
        $this->feather = \Slim\Slim::getInstance();
    }
    
    public function display()
    {
        global $lang_common, $feather_config, $feather_user, $feather_start, $db;

        if (!$feather_user['is_guest']) {
            header('Location: '.get_base_url());
            exit;
        }

        // Load the login.php language file
        require FEATHER_ROOT.'lang/'.$feather_user['language'].'/login.php';

        // Load the login.php model file
        require FEATHER_ROOT.'model/login.php';

        // TODO?: Try to determine if the data in HTTP_REFERER is valid (if not, we redirect to index.php after login)
        $redirect_url = get_redirect_url($_SERVER);

        $page_title = array(pun_htmlspecialchars($feather_config['o_board_title']), $lang_common['Login']);
        $required_fields = array('req_username' => $lang_common['Username'], 'req_password' => $lang_common['Password']);
        $focus_element = array('login', 'req_username');

        if (!defined('PUN_ACTIVE_PAGE')) {
            define('PUN_ACTIVE_PAGE', 'login');
        }

        require FEATHER_ROOT.'include/header.php';

        $this->feather->render('header.php', array(
                            'lang_common' => $lang_common,
                            'page_title' => $page_title,
                            'feather_user' => $feather_user,
                            'feather_config' => $feather_config,
                            '_SERVER'    =>    $_SERVER,
                            'navlinks'        =>    $navlinks,
                            'page_info'        =>    $page_info,
                            'db'        =>    $db,
                            'required_fields'    =>    $required_fields,
                            'p'        =>    '',
                            )
                    );

        $this->feather->render('login/form.php', array(
                            'lang_common' => $lang_common,
                            'lang_login' => $lang_login,
                            'redirect_url'    =>    $redirect_url,
                            )
                    );

        $this->feather->render('footer.php', array(
                            'lang_common' => $lang_common,
                            'feather_user' => $feather_user,
                            'feather_config' => $feather_config,
                            'feather_start' => $feather_start,
                            'footer_style' => 'index',
                            )
                    );

        require FEATHER_ROOT.'include/footer.php';
    }

    public function logmein()
    {
        global $lang_common, $lang_login, $feather_config, $feather_user, $feather_start, $db;

        define('PUN_QUIET_VISIT', 1);

        if (!$feather_user['is_guest']) {
            header('Location: index.php');
            exit;
        }

        // Load the login.php language file
        require FEATHER_ROOT.'lang/'.$feather_user['language'].'/login.php';

        // Load the login.php model file
        require FEATHER_ROOT.'model/login.php';

        login($this->feather);
    }

    public function logmeout($id, $token)
    {
        global $lang_common, $feather_config, $feather_user, $feather_start, $db;

        define('PUN_QUIET_VISIT', 1);

        // Load the login.php language file
        require FEATHER_ROOT.'lang/'.$feather_user['language'].'/login.php';

        // Load the login.php model file
        require FEATHER_ROOT.'model/login.php';

        logout($id, $token);
    }

    public function forget()
    {
        global $lang_common, $lang_login, $feather_config, $feather_user, $feather_start, $db;
        
        // Get current instance
        $this->feather = \Slim\Slim::getInstance();

        define('PUN_QUIET_VISIT', 1);

        if (!$feather_user['is_guest']) {
            header('Location: index.php');
            exit;
        }

        // Load the login.php language file
        require FEATHER_ROOT.'lang/'.$feather_user['language'].'/login.php';

        // Load the login.php model file
        require FEATHER_ROOT.'model/login.php';

        $errors = password_forgotten($this->feather);

        $page_title = array(pun_htmlspecialchars($feather_config['o_board_title']), $lang_login['Request pass']);
        $required_fields = array('req_email' => $lang_common['Email']);
        $focus_element = array('request_pass', 'req_email');

        if (!defined('PUN_ACTIVE_PAGE')) {
            define('PUN_ACTIVE_PAGE', 'login');
        }
        require FEATHER_ROOT.'include/header.php';

        $this->feather->render('header.php', array(
                            'lang_common' => $lang_common,
                            'page_title' => $page_title,
                            'p' => $p,
                            'feather_user' => $feather_user,
                            'feather_config' => $feather_config,
                            '_SERVER'    =>    $_SERVER,
                            'required_fields'    =>    $required_fields,
                            'page_head'        =>    '',
                            'navlinks'        =>    $navlinks,
                            'page_info'        =>    $page_info,
                            'db'        =>    $db,
                            )
                    );

        $this->feather->render('login/password_forgotten.php', array(
                            'errors'    =>    $errors,
                            'lang_login'    =>    $lang_login,
                            'lang_common'    =>    $lang_common,
                            )
                    );

        $this->feather->render('footer.php', array(
                            'lang_common' => $lang_common,
                            'feather_user' => $feather_user,
                            'feather_config' => $feather_config,
                            'feather_start' => $feather_start,
                            'footer_style' => '',
                            )
                    );

        require FEATHER_ROOT.'include/footer.php';
    }
}
