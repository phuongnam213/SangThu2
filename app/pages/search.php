<?php require page('includes/header')?>
	
	<div class="section-title">Search for: <?=$_GET['find']?></div>

	<section class="content">
		
		<?php 
			$title = $_GET['find'] ?? null;
			if(!empty($title)){

				$title = "%$title%";
				$query = "select * from songs where title like :title order by views desc limit 24";
				$rows = db_query($query,['title'=>$title]);
			}
			

		?>

		<?php if(!empty($rows)):?>
			<?php foreach($rows as $row):?>
				<?php include page('includes/song')?>
			<?php endforeach;?>
		<?php else:?>
			<div class="m-2">No songs found</div>
		<?php endif;?>

	</section>

<?php require page('includes/footer')?>