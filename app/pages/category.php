<?php require page('includes/header')?>
	
	<div class="section-title">Category</div>

	<section class="content">
		
		<?php 
			$category = $URL[1] ?? null;
			$query = "select * from songs where category_id in (select id from categories where category = :category) order by views desc limit 24";
			
			$rows = db_query($query,['category'=>$category]);

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