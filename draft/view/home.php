<?php

namespace Ziel\View;

class Home {

    public static function home_init()
    {
        global $aPage, $aRouter;
        $aPage = array();
        $aPage['content'] = $aPage['script'] = $aPage['projekt'] = '';
        $aPage['title'] = '🔆 Home';
        $aPage['projekt'] = '<br/>🩹Projekt: Ziel-IDE';
        $aPage['stylesheet'] = <<<CSSSTYLE
<style>
body {
	font-family: 'Calibri', sans-serif;
	display: flex;
	flex-direction: column;
	min-height: 100vh;
	margin: 0;
	padding: 0;
}

header {
	flex: 0 0 0;
	background-color: #C14F4F;
}

main {
	flex: 1;
	display: flex;
	background-color: #699EBD;
	height: 86%;
}

footer {
	flex: 0 0 40px;
	background-color: #C14F4F;
	text-align: center;
}

.left,
.right {
	flex: 0 2 25%;
	background-color: #C28282;
	height: 100%;
	overflow: hidden;
}

.middle {
	flex: 1 1 75%;
	padding-left: 50px;
}

.loader {
	border: 16px solid #f3f3f3;
	border-top: 16px solid #3498db;
	border-radius: 50%;
	width: 60px;
	height: 60px;
	animation: spin 2s linear infinite;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-left: -20px;
	margin-top: -20px;
}

@keyframes spin {
	0% {
		transform: rotate(0deg);
	}

	100% {
		transform: rotate(360deg);
	}
}

.hidden {
	display: none;
}


.menu {
	display: flex;
	background-color: #303030;
	color: white;
}

.menu ul {
	display: flex;
	justify-content: space-evenly;
	align-items: flex-start;
	list-style-type: none;
	padding: 7px;
	margin: 5px;
}

.menu ul li {}

.menu ul li a {
	padding: 7px 14px;
	text-decoration: none;
	text-align: center;
	color: #808080;
}

.menu ul li a:hover {
	color: white;
}

.menu ul li ul {
	display: none;
}

.menu ul li:hover ul {
	display: flex;
	position: absolute;
	flex-direction: column;
	background-color: #303030;
	padding-top: 7px;
}

.menu ul li:hover ul li {
	padding: 7px 14px;
}

</style>
CSSSTYLE;

        $aPage['javascript'] = <<<JSSCRIPT
<script>
</script>
JSSCRIPT;

        $aPage['content'] .= <<<CONTENT
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
    <div class="left">
        left
    </div>
    <div class="middle">
        <div id="content">
CONTENT;


// Set the limit to 5 MB.
$fiveMBs = 5 * 1024 * 1024;
$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');

fputs($fp, "hello\n");

// Read what we have written.
rewind($fp);
$aPage['content'] .= stream_get_contents($fp);


        $aPage['content'] .= <<<CONTENT
            content
        </div>
    </div>
</main>

CONTENT;



        return true;
    }

}

?>