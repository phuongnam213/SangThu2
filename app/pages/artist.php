<?php require page('includes/header')?>
	
	<center><div class="section-title">Artist Profile</div></center>

	<section class="content">
		
		<?php 
			$id = $URL[1] ?? null;
			$query = "select * from artists where id = :id limit 1";
			$row = db_query_one($query,['id'=>$id]);

		?>

		<?php if(!empty($row)):?>
			<?php include page('artist-full')?>
		<?php endif;?>

	</section>

<?php require page('includes/footer')?>