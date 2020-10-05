<?php

session_start();
$id = $_SESSION['id_groups'];
require_once '../../class/schedule.php';
if(isset($_SESSION['id']))
{
	function li_format($pair, $hl = true)
	{
		global $cur_pair;
		$cur = $pair["pair"] == $cur_pair;

		?>
		<div class="row row-relative mx-0">
			<div class="col-4 my-auto p-0">
				<div class="col-border-padding text-center">
					<?php if ($cur and $hl) { ?><span class="pair-current"><?php } ?>
					<?php echo Schedule::pair_time($pair["pair"]); ?>
					<?php

					$parity = Schedule::parity();

					if ($pair['week'] == 'odd')
					{
						echo '<br>';
						if ($parity) echo ' <b>';
						echo 'н / н';
						if ($parity) echo '</b>';
					}
					else 
						if ($pair['week'] == 'even')
						{
							echo '<br>';
							if (!$parity) echo ' <b>';
							echo 'ч / н';
							if (!$parity) echo '</b>';
						}

						?>
					</div>
					<?php if ($cur) { ?><span><?php } ?>
				</div>
				<div class="col-4 p-0 col-border">
					<div class="col-border-padding text-center">
						<?php echo '<b>'.$pair['short_name'].'</b><br> <small>'.$pair['corps'].'-'.$pair['auditory'].'</small>'; ?>
					</div>
				</div>
				<div class="col-4 my-auto p-0 col-border">
					<div class="col-border-padding text-center">
						<a href="#">
							<?php echo $pair['group_number']; ?>
						</div>
					</a>
				</div>
			</div>
			<?php
		}
			
	if (isset($_GET['week']) and isset($_GET['day']))
	{
		global $id;
		$day = intval($_GET['day']);
		if ($day < 0 or $day >= 7) die('wrong day');
		$week = $_GET['week'];
		if (!in_array($week, ['odd', 'even', 'both'])) die('wrong week');
		$last_day = 0;
		if ($day == 0) $result = Schedule::weekly($week, $_SESSION['role'], $id);
		else  $result = Schedule::daily($day, $week, $_SESSION['role'], $id);
		$days = [];
		for ($i = 0; $i < count($result); $i++)
			$days[] = $result[$i]["day"];
		$days = array_unique($days);
		sort($days);
		
		if (count($result) != 0) { ?>
		<div class="row">
		
		<?php
		
			for ($i = 0; $i < count($days); $i++)
			{
				$cday = $days[$i];
		?>
		
		<div class="col-12 col-lg-6 my-2">
		<div class="card h-100">
	   <h6 class="card-header">
	   <?php echo Schedule::days()[$cday - 1]; ?>
	   </h6>
	   <div class="card-body my-auto">
	   
		
		<ul class="list-group list-group-flush">
		 <?php foreach ($result as $pair) {
				if ($pair['day'] != $cday) continue;
		 ?>
		  <li class="list-group-item px-0"><?php li_format($pair); ?></li>
		 <?php } ?>
		</ul>
		 </div>
		</div>
		</div>
		
		<?php }
			echo '</div>';
		} else { ?>
		 <h5 class="text-secondary text-center m-3">Нет занятий</h5>
		<?php }
		exit();
	}

	$today = Schedule::today($_SESSION['role'], $id);
	$pairs = count($today);
	$cur_pair = Schedule::current_pair();
	$cur_pair_time = Schedule::pair_time($cur_pair);
	$a = new DateTime(explode("-",$cur_pair_time)[1]);
	$b = new DateTime('NOW');
	$last_pair = $a->diff($b)->format("%H:%i:%s");

	
	?>

	<div class="row">
	 <div class="col">
	  <div class="card">
	   <div class="card-status bg-blue"></div>
	   <h6 class="card-header">
	   <?php echo Schedule::date_today(); ?>
	   </h6>
	   <div class="card-body">
	    <div class="row">
		 <div class="col-12 col-lg-6">
		  <div class="card h-100">
		   <div class="card-body">
		    <div class="row">
	         <div class="col-6 my-auto">
		      <i class="fa fa-clock-o"></i> &nbsp; 
			  <span id="timeBlock"><?echo date('H:i:s');?></span>
			 </div> <!-- col-6 -->
		     <div class="col-6 my-auto pl-0">
		      <i class="fa fa-list"></i> &nbsp; 
		      <span id="pairBlock"><?echo $cur_pair;?> пара: <?echo $cur_pair_time;?></span>
		     </div> <!-- col-6 -->
	        </div> <!-- row -->
			<div class="row">
		     <div class="col-6 my-auto">
		      <i class="fa fa-envelope"></i> &nbsp; 
		       Нет
		     </div> <!-- col-6 -->
		     <div class="col-6 my-auto pl-0">
		      <i class="fa fa-list"></i>&nbsp;&nbsp; Осталось: <span id="remainsBlock"><?echo $last_pair;?></span>
		     </div> <!-- col-6 -->
	        </div> <!-- row -->
		   </div> <!-- card body -->
		  </div> <!-- card -->
		 </div> <!-- col info -->
		 
		 <div class="w-100 col d-lg-none" style="height: 16px;">
		 </div>
		 
		 <div class="col-12 col-lg-6">
		  <div class="card h-100">
		  <h6 class="card-header">Cегодня</h6>
		   <div class="card-body px-0">
			<?php if ($pairs != 0) { ?>
		    <ul class="list-group list-group-flush">
			<?php foreach ($today as $pair) { ?>
			<li class="list-group-item px-0">
			<?php li_format($pair); ?></li>
			<?php } ?>
		    </ul>
		    <?php } else { ?>
		     <h5 class="text-secondary text-center m-3">
			 Нет занятий</h5>
		    <?php } ?>
		   </div> <!-- card body -->
		  </div> <!-- card -->
		 </div> <!-- col info -->
		</div> <!-- row inner -->
	   </div> <!-- card body -->
	  </div> <!-- card -->
	 </div> <!-- col -->
	</div> <!-- row main -->

	<!--- --------------------------------- -->


	<div class="row">


	<div class="col-12">
	<div class="card mt-3">
	  <div class="card-status bg-blue"></div>
	  <h6 class="card-header">Расписание</h6>
	  

	  <div class="card-body">
	  <div class="row">
	  	<div class="col mb-1">
	 <nav>
	  <ul style="float: left;" class="pagination pagination-sm my-0 justify-content-end">
	    <li class="page-item btnWeek" data-week="even"><a class="page-link" href="schedule.php#">ч / н</a></li>
	    <li class="page-item btnWeek" data-week="odd"><a class="page-link" href="schedule.php#">н / н</a></li>
	    <li class="page-item btnWeek active" data-week="both"><a class="page-link" href="schedule.php#">Все</a></li>
	  </ul>
	</nav>
	</div>
	<div class="col">
		 <nav>
	  <ul class="pagination pagination-sm my-0 justify-content-end">
	    <li class="page-item btnDay" data-day="1"><a  class="page-link" href="schedule.php#">Пн</a></li>
	    <li class="page-item btnDay" data-day="2"><a class="page-link" href="schedule.php#">Вт</a></li>
	    <li class="page-item btnDay" data-day="3"><a class="page-link" href="schedule.php#">Ср</a></li>
	    <li class="page-item btnDay" data-day="4"><a class="page-link" href="schedule.php#">Чт</a></li>
	    <li class="page-item btnDay" data-day="5"><a class="page-link" href="schedule.php#">Пт</a></li>
	    <li class="page-item btnDay active" data-day="0"><a class="page-link" href="schedule.php#">Неделя</a></li>
	  </ul>
	</nav>
	</div>
	</div>
		
		<div style="clear: both;"></div><hr class=" hr-grey">
		
	 <div id="containerSchedule"></div>
	  </div>
	</div>
	</div> <!-- col -->
	</div>
	<script>
	</script>
<?php
}
else
{	
  $err ='
	<div class="alert alert-warning" role="alert">
		У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
	</div>';
	echo $err;
}
?>