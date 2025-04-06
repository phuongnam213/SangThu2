<!--start music card-->
<div class="music-card">
	<div style="overflow: hidden;">
		<a href="<?=ROOT?>/song/<?=$row['slug']?>"><img src="<?=ROOT?>/<?=$row['image']?>"></a>
	</div>
	<div class="card-content">
		<div class="card-title"><?=esc($row['title'])?></div>
		<div class="card-subtitle"><?=esc(get_artist($row['artist_id']))?></div>
		<div class="card-subtitle" style="font-size: 12px;">Category: <?=esc(get_category($row['category_id']))?></div>
	</div>
</div>
<!--end music card-->