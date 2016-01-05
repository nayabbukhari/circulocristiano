<?php include("sl-env.php"); extract(sl_ty(__FILE__)); ?>
<strong style='font-size:20px; text-align:justify; display:block; font-family:Georgia;'><?php print $thanks_heading; ?></strong>
<!--p>&nbsp;</p-->

<p <?php print $thanks_msg_style; ?>><?php print $thanks_msg; ?></p>
<!--p>&nbsp;</p-->

<?php if (!$is_included){ ?>
<form method="post" name="sl_ty_form">
<?php } ?>
   <!--div--><!--strong <?php print $action_call_style; ?>> <?php print $action_call; ?></strong-->
    <?php if ($is_included){ ?>
    <p <?php print $action_buttons_style; ?>>
	<!--br--><strong style='font-size:1.5em;'><!--br--><img src='<?php print SL_IMAGES_BASE_ORIGINAL."/sl_star.jpg"; ?>'></strong>&nbsp;<input type='button' class='button-primary star_button' style='/*font-size:16px;height:25px;*/ font-family: georgia' href="" value='Give My Review' />&nbsp;<!--img src='<?php print SL_IMAGES_BASE_ORIGINAL."/sl_star.jpg"; ?>'-->
	<br>
	<br><strong style='font-size:1.5em;'><img src='<?php print SL_IMAGES_BASE_ORIGINAL."/sl_twitter.jpg"; ?>'></strong>&nbsp;<input type='button' class='button-primary twitter_button' rel="<?php print $text; ?>+<?php print $url; ?>" value='Tell Others #1' />
	<input type='button' class='button-primary twitter_button' rel="<?php print $text2; ?>+<?php print $url; ?>" value='Tell Others #2' />
    </p>
    <?php } ?>
    <!--/div-->
	<?php if (!$is_included){ ?> <!--p>&nbsp;</p--><br><!--p>&nbsp;</p--><?php } ?>
    <div style="text-align: center;">
        <!--p-->
        	<input type="hidden" name="sl_thanks" value="show" />
        	<input type="hidden" name="sl_thanks_time_length" />
        	<input type="hidden" name="sl_thanks_num_locs" />
        	<?php if (!$is_included){ ?>
        	<a href='#' class='star_button' style='font-weight:bold; font-size:14px;'>Sure, you deserve it</a><br><br><br><br>
        	<a href='#' onclick="styf=document.forms['sl_ty_form'];styf.sl_thanks.value='true';styf.submit();return false;" style='font-weight:normal;'><?php print $done_msg; ?></a> <strong>OR</strong>  
        	<a href='#' onclick="styf=document.forms['sl_ty_form'];styf.sl_thanks.value='false';styf.sl_thanks_time_length.value='<?php print SL_THANKS_TIME_LENGTH; ?>';styf.sl_thanks_num_locs.value='<?php print SL_THANKS_NUM_LOCS; ?>';styf.submit();return false;"><?php print $noshow_msg; ?></a>
        	<?php } ?>
        <!--/p-->
    </div>
<?php if (!$is_included){ ?>
</form>
<?php } ?>