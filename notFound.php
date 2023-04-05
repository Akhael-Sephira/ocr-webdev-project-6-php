<h1>Not found</h1>
<?php 
if (!empty($_REQUEST['uri'])) echo "/{$_REQUEST['uri']}";
else echo '/';
?>