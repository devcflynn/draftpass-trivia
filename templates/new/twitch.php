<?php $this->layout('template', ['title' => 'Twitch Stream']) ?>

<div id="twitch-embed"></div>

<p>You can submit your answers to us on our <a href="/scorepage"  target="_blank">Scoring Page!</a></p>

<?php $this->push('scripts') ?>
<script src="https://embed.twitch.tv/embed/v1.js"></script>
    <script type="text/javascript">
    var channel = <?php echo json_encode($channel); ?>;
        new Twitch.Embed("twitch-embed", {
            width: 940,
            height: 480,
            channel: channel,
        });
    </script>
<?php $this->end() ?>