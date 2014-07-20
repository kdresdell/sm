<form method="get" id="searchform" action="<?php echo home_url(); ?>">
	<fieldset>
	<input type="text" value="<?php _e('Search', 'themeva' ); ?>" name="s" id="s" onfocus="if(this.value == '<?php _e('Search', 'themeva' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search', 'themeva' ); ?>';}" />
	<input type="submit" id="searchsubmit" value="&#xf002;" />
	</fieldset>
</form>