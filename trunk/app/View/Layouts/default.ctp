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
        <link href="<?php echo Router::url('/', true); ?>css/jQueryUI/jquery-ui.css" rel="stylesheet" type="text/css" />
	<!-- bootstrap 3.0.2 -->
        <link href="<?php echo Router::url('/', true); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?php echo Router::url('/', true); ?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="<?php echo Router::url('/', true); ?>css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="<?php echo Router::url('/', true); ?>css/AdminLTE.css" rel="stylesheet" type="text/css" />
	
	<link href="<?php echo Router::url('/', true); ?>css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
	<!-- declearation -->
	<script>
	    var base_url = '<?php echo Router::url('/', true); ?>';
	    var time_stamp = '<?php echo time(); ?>';
	    var token = '<?php echo md5('unique_salt' . time());?>';
	</script>
	<!-- jQuery 2.0.2 -->
        <script src="<?php echo Router::url('/', true); ?>js/jquery.min.js"></script>
    </head>
    <body class="skin-blue fixed">
	<?php echo $this->element('header'); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">                
                <!-- sidebar: style can be found in sidebar.less -->
		<?php echo $this->element('sidebar'); ?> 	
	    </aside>
	    <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">                
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
			<?php echo isset($title) ? __($title) : '' ?>
                        <small><?php echo isset($subtitle) ? __($subtitle) : '' ?></small>
                    </h1>
                    <?php //echo $this->Html->getCrumbs(' > ', 'Home'); ?>
		    <ol class="breadcrumb">
                        <li><a href="<?php echo Router::url('/', true); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                
		<?php echo $this->Session->flash(); ?>
		<div id="content">
		<?php echo $this->fetch('content'); ?>
		</div>
		<?php echo $this->element('sql_dump'); ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
	
	<!-- Modelbox start -->
	<div class="modal fade" id="modelbox">
	<div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	    </button>
	    <h4 id="modelbox_title">Modal title</h4>
	  </div>
	  <div class="modal-body"><div id="modelbox_body"></div></div>
	</div>
	</div>
	</div>
	<!-- Modelbox end -->
	
	<!-- Jquery UI -->
	<script src="<?php echo Router::url('/', true); ?>js/jquery-ui.js" type="text/javascript"></script>
	<!-- Bootstrap -->
        <script src="<?php echo Router::url('/', true); ?>js/bootstrap.min.js" type="text/javascript"></script>
        <!-- Validate JS -->
        <script src="<?php echo Router::url('/', true); ?>js/validate.js" type="text/javascript"></script>
	<!-- Form JS -->
	<script src="<?php echo Router::url('/', true); ?>js/form.js" type="text/javascript"></script>
	<!-- AdminLTE App -->
        <script src="<?php echo Router::url('/', true); ?>js/AdminLTE/app.js" type="text/javascript"></script>
	<!-- bootbox script -->
	<script src="<?php echo Router::url('/', true); ?>js/bootbox.min.js" type="text/javascript"></script>
	<!-- common script -->
	<script src="<?php echo Router::url('/', true); ?>js/common.js" type="text/javascript"></script>
    </body>
</html>