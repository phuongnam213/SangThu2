<?php require page('includes/header')?>
	
	<div class="section-title">Music</div>

	<section class="content">
		
		<?php 
			$limit = 20;
			$offset = ($page - 1) * $limit;

			$rows = db_query("select * from songs order by views desc limit $limit offset $offset");

		?>

		<?php if(!empty($rows)):?>
			<?php foreach($rows as $row):?>
				<?php include page('includes/song')?>
			<?php endforeach;?>
		<?php endif;?>

	</section>

	<div class="mx-2">
		<a href="<?=ROOT?>/music?page=<?=$prev_page?>">
			<button class="btn bg-orange">Prev</button>
		</a>
		<a href="<?=ROOT?>/music?page=<?=$next_page?>">
			<button class="float-end btn bg-orange">Next</button>
		</a>
	</div>
<?php require page('includes/footer')?>