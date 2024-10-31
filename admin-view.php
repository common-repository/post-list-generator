<div class="wrap">
	<div class="icon32" id="icon-options-general">
		<br>
	</div>
	<h2>Post List 設定</h2>
	<h3>ショートコード</h3>
	<p>以下のコードをコピーして、Post Listを表示する固定ページや投稿の本文内に貼り付けてください。</p>
	<p>
		<input type="text" value=<?php echo '['. PLG::SHORTCODE .']';?> readonly></input>
	</p>
	<form action="options.php" method="post">
		<?php settings_fields( $option_name ); ?>
		<?php do_settings_sections( $file ); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary"
				value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
	</form>
</div>
