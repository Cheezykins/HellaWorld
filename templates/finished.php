<?php ob_start(); ?>
<div id="finishedbox">
	<form action="<?php echo $self; ?>" method="post">
		<div id="finishedcontrols">
			<span id="finishedcount"><?php printf(_('Finished items: %s'), count($x->item)); ?></span>
			<?php if (count($x->item) > 0): ?>
			<input type="submit" name="clearnzbs" value="<?php echo _('Clear Finished NZBs'); ?>" />
			<?php endif; ?>
		</div>
	</form>
	<form action="<?php echo $self; ?>" method="post">
	<ul id="finished">
		<?php if (count($x->item) == 0): ?>
			<li class="queuebox" style="text-align: center; font-weight: bold;"><?php echo _('No Finished Items'); ?></li>
		<?php else: ?>
			<?php $i = 0; foreach($x->item as $item):
				$i++; $durl = '';
				if (!strncmp($config['destpath'], (string)$item->destDir, strlen($config['destpath'])) && is_dir((string)$item->destDir)):
					$durl = $baseurl.substr((string)$item->destDir, strlen($config['destpath'])+1);
				endif
			?>
			<li class="queuebox <?php echo (((string)$item->type == 'SUCCESS') ? 'good' : 'bad'); ?>">
				<div>
					<?php if ($config['showlinks'] && !empty($durl)): ?>
					<div><a class="queuetitle" href="<?php echo $durl; ?>"><?php echo (string)$item->archiveName; ?></a></div>
					<?php else: ?>
					<div class="queuetitle"><?php echo (string)$item->archiveName; ?></div>
					<?php endif ?>
				</div>
				<div class="removecontrols">
					<a href="<?php echo $self; ?>?removefinished=<?php echo $i; ?>"><?php echo _('Remove'); ?></a>
					<input type="checkbox" name="removefinished[<?php echo $i; ?>]" />
				</div>
				<div class="queuestats"><?php printf(_('Finished on: %s Processing Time: %s'), date('M dS - H:i:s', (int)$item->finishedTime), (string)$item->elapsedTime); if (trim((string)$item->parMessage) != ''): printf(_(' Par message: %s'), (string)$item->parMessage); endif; ?></div>
			</li>
			<?php endforeach; ?>
			<li class="queuebox">
				<input type="submit" value="Remove Selected" />
			</li>
		<?php endif; ?>
	</ul>
	</form>
</div>
<?php $finished_view = ob_get_clean(); ?>
