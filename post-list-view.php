<div class='post-list'>
	<table class="post-table">
		<?php foreach($info->items as $item): ?>
		<tr>
			<td class="postdate"><?php echo $item->date; ?>
			</td>
			<td ><a href="<?php echo $item->url; ?> "<?php if ( $info->window_open ){ echo 'target="_blank" '; } ?> > <?php echo $item->title; ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php if ( $info->has_next ): ?>
		<p id='next-post-btn' >続きを見る</p>
		<div id='loader'><img src='<?php echo( plugins_url( "image/loader.gif", __FILE__ ));?>' /></div>
	<?php endif; ?>
</div>
