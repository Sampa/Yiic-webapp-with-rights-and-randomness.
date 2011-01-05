<div class="view" style="float: left;">

<h2> <?php echo $data->role->title; ?> </h2>

	<?php
if($data->payment_date == 0) 
	echo Yum::t('Membership has not been payed yet');
	else {
		echo Yum::t('Membership payed at: {date}', array(
					'{date}' =>  date('d. m. Y', $data->payment_date)));
		echo Yum::t('Membership ends at: {date} ', array(
					'{date}' =>  date('d. m. Y', $data->end_date)));  
		echo Yum::t('This membership is still {days} days active', array(
					'{days}' => $data->daysLeft()));
	}

?>

<br /> Bestellt am: <?php echo date('d. m. Y', $data->order_date); ?> 
<br /> Zahlungsweise: <?php echo $data->payment->title; ?> 
</div>
