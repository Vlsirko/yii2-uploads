<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<ul>
<?php
foreach($uploads as $file){
	
	?>
	<li><?= (string) $file?></li>
	<?php
}
?>
</ul>