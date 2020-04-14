<?php $this->layout('template', ['title' => 'Twitch']); ?>
<div id="twitch-embed"></div>
<script src="https://embed.twitch.tv/embed/v1.js"></script>
<script type="text/javascript">
var channel = <?php echo json_encode($channel); ?>;
      new Twitch.Embed("twitch-embed", {
        width: 940,
        height: 480,
        channel: channel,
      });
</script>
	
You can submit your answers to us on our <a href=scorepage.php  target='_blank'>Scoring Page!</a>