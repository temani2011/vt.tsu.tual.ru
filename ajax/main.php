<?php
session_start();
require_once '../class/schedule.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
$news = $dal->get_n_news(5);
?>
<div class="row mb-4">
	<div class="col">
		<div class="card">
			<div class="card-status bg-blue"></div>
			<div class="card-header " style="border-bottom: 0px">
				<div class="recent_heading">
					<h4>Главная</h4>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mb-4">
	<div class="col-sm-6 col-12">
		<div class="row">
			<div class="col-6">
				<div class="card h-100">
					<div class="titlebox">
						<span><a href="#news.php" style="color: black;">Новости</a></span>
					</div>
					<div class="card-body">
					<?php foreach($news as $key){ ?>
						<div class="news">
	        	        	<div class="newsdate"><?php $date = new DateTime($key["date"]); $key["date"] = $date->format('d.m.Y'); echo $key["date"]; ?></div>
	                        <a class="coloured" href="#news?id=<?php echo $key['id']; ?>"><?php echo $key['name']; ?></a>
	                	</div>
					<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="card h-100">
					<?php 
					$news_date;
					$news_text;
					$news_title;
					foreach($news as $key) 
					{ 
						preg_match("/\<img.+src=[\"|\'](?!https?:\/\/)([^\/].+?)[\"|\']/", $key['text'], $match);
						if(count($match)>0) 
						{ 
							$news_text=$key['text']; 
							$news_title=$key['name'];
							$date = new DateTime($key["date"]); 
							$news_date = $date->format('d.m.Y');
							break; 
						} 
					} 
					?>
					<img class="card-img-top" <?php echo $match[0];?>/>					
					<div class="card-body">					
						<h2 class="card-title"><a href="#news?id=<?php echo $key['id'];?>"><b><?php echo $news_title;?></b></a></h2>
						<div class="block-with-text">
							<?php echo strip_tags(str_replace($match[0], "", $news_text));?>
						</div>
						<div class="newsdate"><?php echo $news_date; ?></div>
					</div>
				</div>			
			</div>
			<div class="col-12 mt-4">
				<div class="card">
					<div class="titlebox">
						<span><a href="#docs.php" style="color: black;">Полезная информация</a></span>
					</div>
					<div class="card-body">
						<div><a href="http://tsu.tula.ru/education/info/" class="big black">Информационно-образовательные интернет-ресурсы</a></div>
						<div><a href="http://tsu.tula.ru/information/documents/anticorruption/" class="big black">Противодействие коррупции в ТулГУ</a></div>
						<div ><a href="http://tsu.tula.ru/antiterror/" class="big black">Противодействие терроризму в ТулГУ</a></div>
						<div ><a href="http://tsu.tula.ru/invalid/" class="big black">Информация для инвалидов и лиц с ОВЗ</a></div>
					</div>
				</div>
			</div>
			<div class="col-12 mt-4 mb-4">
				<div class="card">
					<div class="titlebox">
						<span><a href="#docs.php" style="color: black;">Последние обновления</a></span>
					</div>
					<div class="card-body">
						<div class="card-text">
							Документы:
							<?php $docs=$dal->get_last_docs(); 
							foreach($docs as $doc) { ?>
							<ul style="padding-left: 20px; margin-bottom: 5px;">
								<li><a href="<?php echo ($doc['cat_path'].'/'.$doc['name'])?>" ><?php echo $doc['name']; ?></a> в каталог <a href="#"><?php echo $doc['title']; ?></a></li>
							</ul>
							<?php } ?>
							<!--
							Пользователи:
							<ul>
								<li style="padding-left: 20px"><a></a></li>
							</ul>
							-->
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col"></div>
			<div class="col"></div>
		</div>
	</div>
	<div class="col-sm-6 col-12">
		<div class="col-12 p-0 mb-4"><div class="card py-2" style="text-align: center;"><?php echo Schedule::date_today(); ?></div></div>
		<div class="col-12 p-0 mb-4">
			<div id="accordion p-0">
				<div class="card">
					<div class="card-header" id="headingOne">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								Студенту
							</button>
						</h5>
					</div>

					<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
						<div class="card-body">
							<div><a class="coloured" href="#schedule.php">Расписание занятий</a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/information/documents/stud-docs/">Полезные документы </a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/science/contests/">Конкурсы</a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/news/info/hotline">Горячая линия Минобрнауки РФ</a></div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingTwo">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								Магистранту
							</button>
						</h5>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
						<div class="card-body">
							<div><a class="coloured" href="#schedule.php">Расписание занятий</a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/information/documents/stud-docs/">Полезные документы </a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/science/contests/">Конкурсы</a></div>
							Раздел будет дополняться
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingThree">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
								Аспиранту
							</button>
						</h5>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
						<div class="card-body">
							<div><a class="coloured" href="#schedule.php">Расписание занятий</a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/information/documents/stud-docs/">Полезные документы </a></div>
							<div><a class="coloured" href="http://tsu.tula.ru/science/contests/">Конкурсы</a></div>
							Раздел будет дополняться
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mb-4">
			<div class="col-6">
				<div class="card h-100">
					<img class="card-img-top" src="/img/inteh.png"/>					
					<div class="card-body">			
						<h4 class="card-title main-repr"><a href="http://tsu.tula.ru/structure/itc/"><b>Инновационно-технологический центр</b></a></h4>
					</div>
				</div>			
			</div>
			<div class="col-6">
				<div class="card h-100">
					<img class="card-img-top" src="/img/intuit.PNG"/>					
					<div class="card-body">					
						<h4 class="card-title main-repr"><a href="http://www.intuit.ru/"><b>Образовательная площадка ИНТУИТ</b></a></h4>
					</div>
				</div>			
			</div>
		</div>
		<div class="row mb-4">
			<div class="col-6">
				<div class="card h-100">
					<img class="card-img-top" src="/img/intTulg.png" />					
					<div class="card-body">			
						<h4 class="card-title main-repr"><a href="http://www.i-institute.org/"><b>Дистанционные образовательные технологии</b></a></h4>
					</div>
				</div>			
			</div>
			<div class="col-6">
				<div class="card h-100">
					<img class="card-img-top" src="/img/bibltulgu.png" />					
					<div class="card-body">					
						<h4 class="card-title main-repr"><a href="http://library.tsu.tula.ru/news/news.htm"><b>Научная библиотека ТулГУ</b></a></h4>
					</div>
				</div>			
			</div>
		</div>
		<div class="col-12 p-0 mb-4">
			<div class="card">
				<div class="titlebox">
					<span><a href="#docs.php" style="color: black;">Социальные сети</a></span>
				</div>			
				<div class="card-body">					

				</div>
			</div>		
		</div>
	</div>
</div>