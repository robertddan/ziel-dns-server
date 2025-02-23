<?php

namespace Ziel\View;

class Error {

    public static function error_init()
    {
        global $aPage, $aRouter;
        $aPage = array();
        $aPage['content'] = $aPage['script'] = $aPage['projekt'] = '';
        $aPage['title'] = '🔆 Error';
        $aPage['projekt'] = '<br/>🩹Projekt: Ziel-IDE';
        $aPage['content'] .= <<<EOD
<style>
body { font-family: 'Calibri', sans-serif; display: flex; flex-direction: column; min-height: 100vh; margin: 0; padding: 0; } header { flex: 0 0 0; background-color: #C14F4F; } main { flex: 1; display: flex; background-color: #699EBD; height: 86%; } footer { flex: 0 0 40px; background-color: #C14F4F; text-align: center; } .left, .right { flex: 0 2 25%; background-color: #C28282; height: 100%; overflow: hidden; } .middle { flex: 1 1 75%; padding-left: 50px; } .loader { border: 16px solid #f3f3f3; border-top: 16px solid #3498db; border-radius: 50%; width: 60px; height: 60px; animation: spin 2s linear infinite; position: absolute; top: 50%; left: 50%; margin-left: -20px; margin-top: -20px; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .hidden { display: none; } @font-face { font-family: "Fira Code"; src: url("/fonts/FiraCode-Regular.ttf"); } textarea { font-family: "Fira Code"; font-size: 16px; height: 100%; width: 100%; resize: none; border: none; } textarea:focus { outline: none; border: none; }
.menu { display: flex; background-color: #303030; color: white; } .menu ul { display: flex; justify-content: space-evenly; align-items: flex-start; list-style-type: none; padding: 7px; margin: 5px; } .menu ul li { } .menu ul li a { padding: 7px 14px; text-decoration: none; text-align: center; color: #808080; } .menu ul li a:hover { color: white; } .menu ul li ul { display: none; } .menu ul li:hover ul { display: flex; position: absolute; flex-direction: column; background-color: #303030; padding-top: 7px; } .menu ul li:hover ul li{ padding: 7px 14px; }
#file-explorer > div { padding-bottom: 220px; } #file-explorer { overflow: scroll; height: 100%; scrollbar-width: thin; background-color: lightyellow; } #file-explorer div { display: flex; flex-direction: column; } #file-explorer li { list-style-type: none; } #file-explorer a { text-decoration: none; color: #303030; } #file-explorer a:hover { } #file-explorer span { background-color: lightyellow; cursor: pointer; border-top: 1px solid gray; } #file-explorer span:hover { background-color: aliceblue; } .file-explorer-directory { padding: 7px; } .file-explorer-directory:hover { } .file-explorer-file { padding: 7px; } .file-explorer-directory + div { margin-left: 14px; border-left: 1px solid gray; } .file-explorer-directory:hover { } .file-explorer div span:nth-last-child(){ border-bottom: 1px solid gray; }
nav { height: 40px; background-color: darkslategrey; }

</style>

<header>
<div class="menu">
    <ul>
        <li>
            <a href="#">Files</a>
            <ul>
                <li><a data-menu-files="new" href="#new">New</a></li>
                <li><a data-menu-files="save" href="#save">Save</a></li>
                <li><a data-menu-files="saveAll" href="#">Save all</a></li>
                <li><a data-menu-files="openFile" href="#">Open file</a></li>
                <li><a data-menu-files="openProject" href="#">Open project</a></li>
                <li><a data-menu-files="readOnly" href="#">Toggle read-only</a></li>
                <li><a data-menu-files="readOnlyAll" href="#">Toggle read-only all</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Edit</a>
        </li>
        <li>
            <a href="#">View</a>
        </li>
        <li>
            <a href="#">Editor</a>
        </li>
        <li>
            <a href="#">Help</a>
        </li>
    </ul>
</div>
</header>
<nav></nav>
<main>
<h2>Error</h2>
</main>

EOD;

        return true;
    }

}

?>