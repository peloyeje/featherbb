<?php

/**
 * Copyright (C) 2015 FeatherBB
 * based on code by (C) 2008-2012 FluxBB
 * and Rickard Andersson (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

namespace controller;

class viewforum
{
    public function __construct()
    {
        $this->feather = \Slim\Slim::getInstance();
        $this->start = $this->feather->start;
        $this->user = $this->feather->user;
        $this->request = $this->feather->request;
        $this->header = new \controller\header();
        $this->footer = new \controller\footer();
        $this->model = new \model\viewforum();
        load_textdomain('featherbb', FEATHER_ROOT.'lang/'.$this->user->language.'/forum.mo');
    }

    public function __autoload($class_name)
    {
        require FEATHER_ROOT . $class_name . '.php';
    }

    public function display($id, $name = null, $page = null)
    {
        if ($this->user->g_read_board == '0') {
            message(__('No view'), '403');
        }

        if ($id < 1) {
            message(__('Bad request'), '404');
        }

        // Fetch some informations about the forum
        $cur_forum = $this->model->get_info_forum($id);

        // Is this a redirect forum? In that case, redirect!
        if ($cur_forum['redirect_url'] != '') {
            header('Location: '.$cur_forum['redirect_url']);
            exit;
        }

        // Sort out who the moderators are and if we are currently a moderator (or an admin)
        $mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
        $is_admmod = ($this->user->g_id == FEATHER_ADMIN || ($this->user->g_moderator == '1' && array_key_exists($this->user->username, $mods_array))) ? true : false;

        $sort_by = $this->model->sort_forum_by($cur_forum['sort_by']);

        // Can we or can we not post new topics?
        if (($cur_forum['post_topics'] == '' && $this->user->g_post_topics == '1') || $cur_forum['post_topics'] == '1' || $is_admmod) {
            $post_link = "\t\t\t".'<p class="postlink conr"><a href="'.get_link('post/new-topic/'.$id.'/').'">'.__('Post topic').'</a></p>'."\n";
        } else {
            $post_link = '';
        }

        // Determine the topic offset (based on $page)
        $num_pages = ceil($cur_forum['num_topics'] / $this->user->disp_topics);

        $p = (!isset($page) || $page <= 1 || $page > $num_pages) ? 1 : intval($page);
        $start_from = $this->user->disp_topics * ($p - 1);
        $url_forum = url_friendly($cur_forum['forum_name']);

        // Generate paging links
        $paging_links = '<span class="pages-label">'.__('Pages').' </span>'.paginate($num_pages, $p, 'forum/'.$id.'/'.$url_forum.'/#');

        $forum_actions = $this->model->get_forum_actions($id, $this->feather->forum_settings['o_forum_subscriptions'], $cur_forum['is_subscribed']);

        $page_head = array();
        $page_head['canonical'] = "\t".'<link href="'.get_link('forum/'.$id.'/'.$url_forum.'/').'" rel="canonical" />';

        if ($num_pages > 1) {
            if ($p > 1) {
                $page_head['prev'] = "\t".'<link href="'.get_link('forum/'.$id.'/'.$url_forum.'/page/'.($p - 1).'/').'" rel="prev" />';
            }
            if ($p < $num_pages) {
                $page_head['next'] = "\t".'<link href="'.get_link('forum/'.$id.'/'.$url_forum.'/page/'.($p + 1).'/').'" rel="next" />';
            }
        }

        if ($this->feather->forum_settings['o_feed_type'] == '1') {
            $page_head['feed'] = '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=rss" title="'.__('RSS forum feed').'" />';
        } elseif ($this->feather->forum_settings['o_feed_type'] == '2') {
            $page_head['feed'] = '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=atom" title="'.__('Atom forum feed').'" />';
        }

        $this->feather->view2->setPageInfo(array(
            'title' => array(feather_escape($this->feather->forum_settings['o_board_title']), feather_escape($cur_forum['forum_name'])),
            'active_page' => 'viewforum',
            'page_number'  =>  $p,
            'paging_links'  =>  $paging_links,
            'is_indexed' => true,
            'id' => $id,
            'forum_data' => $this->model->print_topics($id, $sort_by, $start_from),
            'cur_forum' => $cur_forum,
            'page_head' => $page_head,
            'post_link' => $post_link,
            'start_from' => $start_from,
            'url_forum' => $url_forum,
            'forum_actions' => $forum_actions,
        ))->addTemplate('viewforum.php')->display();
    }
}
