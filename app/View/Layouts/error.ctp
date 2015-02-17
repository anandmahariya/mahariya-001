<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Error Page</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="<?php echo Router::url('/', true); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo Router::url('/', true); ?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo Router::url('/', true); ?>css/AdminLTE.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="skin-blue">
        <div class="wrapper row-offcanvas row-offcanvas-left">
                <section class="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
                </section><!-- /.content -->
        </div>
    </body>
</html>