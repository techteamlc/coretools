<?php 
if (isset($_POST['replace']) && !empty($_POST['replace'])) :
    echo 'replace';
endif;
print_r($_POST);