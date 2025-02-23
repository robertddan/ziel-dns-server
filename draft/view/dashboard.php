<?php

namespace Ziel\View;

class Dashboard {

    public static function dashboard_init()
    {
        global $aPage, $aRouter;
        $aPage = array();
        $aPage['content'] = $aPage['script'] = $aPage['projekt'] = '';
        $aPage['title'] = '🔆 Dashboard';
        $aPage['projekt'] = '<br/>🩹Projekt: Ziel-IDE';
        $aPage['content'] .= '
            <label for="project">'. $aPage['projekt'] .'</label>
            <div id="sidebar">
            </div>
            <div id="content">
                <h3>🔆 Dashboard</h3>
                <hr></br>
                '. $aPage['projekt'] .'
            </div>
        ';
        
        return true;
    }
}







?>