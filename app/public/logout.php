<?php
setcookie("SID","",time()-3600,"/");
header('Location: login.php'); 
