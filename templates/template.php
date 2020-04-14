<?php $this->insert('partials/header', compact('title')) ?>

<body class="page-fan-loyalty layout-default">
    <div class="universe">
        <div id="header" style="background-image: url('https://draftpass.club/themes/draftpass/assets/images/bg/header_bg_1.jpg')">
            <div class="container">
                <div class="header__logo">
                    <a href="/">
                        <img src="https://draftpass.club/themes/draftpass/assets/images/logo-dark.svg" alt="">
                    </a>
                </div>
                <?php $this->insert('partials/nav') ?>
                
            </div>
            <div class="skewed-shape">
                <svg viewBox="0 0 1440 160">
                    <g>
                        <path fill="#fff" d="M0,160 L0,0 C548.949848,3.38469e-14 823.425766,157.977 1440,157.977 L1440,160 L0,160 Z" transform="matrix(-1 0 0 1 1440 0)"></path>
                    </g>
                </svg>     
            </div>
        </div>
        <div class="content">
        <?php $this->insert('partials/trivia-nav'); ?>
            <h1><?=$this->e($title)?></h1>
            <?php $this->insert('partials/alerts', compact('alerts')); ?>

            <?=$this->section('content')?>
        </div>
    </div>
    
    <?php $this->insert('partials/footer') ?>
    
    <?php $this->insert('partials/mobile-nav') ?>
    
    <script src="https://draftpass.club/combine/ac7e33fe6901c0a3dc50044a91b47139-1565450881">  </script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <?php $this->section('scripts'); ?>
    <script>
    if (!window.jQuery) {
        document.write('<script src="jquery-3.4.1.min.js"><\/script>');
    }
    </script>

  
</body>
</html>