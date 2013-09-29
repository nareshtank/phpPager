<?php
require_once('Pager.php');

$DB_SERVER = 'localhost';
$DB_USER = 'root';
$DB_PASSWORD = '';

$dbConn = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASSWORD);

$webroot="";
$file='demo.php';
$option=array();

$sql='SELECT * from tableName';
$total=executeJoinQuery('SELECT count("id") as total from tableName');
$total=$total['0']['total'];

$options=array('perPage'=>'10',
						'delta'=>'7',
						'separator'=>' - ',
						'urlVar'=>'p',
						'sessionVar'=>'perpage',
						'linkClass'=>'linkPage',
						'curPageLinkClassName'=>"currentPage",
						'nextName'=>'<img src="'.$webroot.'images/arrowright.jpg'.'" border="0" alt="Next" title="Next">',
						'prevName'=>'<img src="'.$webroot.'images/arrowleft.jpg'.'" border="0" alt="Previous" title="Previous">',
					);

$npager=new PagerClass($dbConn, $sql,$total,$webroot,'pager_call.php',$options);

echo 'Currrent Page: '.$npager->getCurrentPage().' out of Total Pages: '.$npager->getTotalPages();
echo '<br />==========================';
echo '<br />'.$npager->getFirst().$npager->getPreviousLink().$npager->getLinks().$npager->getNextLink().$npager->getLast();
echo '<br />==========================';
echo '<br />First Page Page: '.$npager->getFirst(true);
echo '<br />Last Page: '.$npager->getLast(true);
echo '<br />==========================';
echo '<br />'.$npager->getLinks('all');
?>
<form action="<?php echo $webroot.$file ?>" method="post" >
<?php
echo '<br />Select Per Page: '.$npager->getDropDown(10,100,10,true,array('id'=>'myDropDownId','class'=>'myDropDownClass','onchange'=>'this.form.submit()'));
?>
</form>
<?php
echo '<pre>';
print_r($npager->getDataArray());
echo '</pre>';
?>
