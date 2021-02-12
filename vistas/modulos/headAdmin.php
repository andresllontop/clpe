<head>
        <title><?php echo COMPANY; ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="Shortcut Icon" type="image/x-icon" href="<?php echo SERVERURL; ?>adjuntos/logos.png" />
        <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/normalize.css">
        <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/bootstrap.min.css">
        <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"
        />
        <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/style.css">
        <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/home.css"/>
        <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/recorder.css"/>
        <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/editor.css"/>
        
        <link
        rel="stylesheet" href="https://releases.flowplayer.org/7.2.7/skin/skin.css" />
        <style>
          /* mixed playlist player */
          #mixed {
            background-color: #036;
          }
          /* make cover image fill container width */
          #mixed.is-audio .fp-player {
            background-size: cover; /* default: contain */
            background-position: top center; /* default: center */
          }
          
          /* icecast player */
          .flowplayer.is-audio-only {
            max-width: 400px;
            background-color: #eee;
          }
          /* keep this controlbar-only player always at same height */
          .flowplayer.is-audio-only.is-small,
          .flowplayer.is-audio-only.is-tiny {
            font-size: 16px;
          }
          </style>
     <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/jquery.mCustomScrollbar.css">
     <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/sweet-alert.css">
        <?php include 'vistas/modulos/scriptAdmin.php';?>
</head>
