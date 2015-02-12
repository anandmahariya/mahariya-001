<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title_for_layout; ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
	?>
	<!-- bootstrap 3.0.2 -->
        <link href="<?php echo Router::url('/', true); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?php echo Router::url('/', true); ?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="<?php echo Router::url('/', true); ?>css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="<?php echo Router::url('/', true); ?>css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">
        <?php echo $this->fetch('content'); ?>
	<!-- jQuery 2.0.2 -->
        <script src="<?php echo Router::url('/', true); ?>js/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?php echo Router::url('/', true); ?>js/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo Router::url('/', true); ?>js/AdminLTE/app.js" type="text/javascript"></script>
	<!-- News Nation JS file -->
	<script src="<?php echo Router::url('/', true); ?>js/common.js" type="text/javascript"></script>
	<?php echo $this->element('sql_dump'); ?>
    </body>
</html>