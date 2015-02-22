<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Get Script</h3>
                <div class="box-tools pull-right">
                    <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-default btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <p id="flash-loaded" style="display:none">Flash player is loaded.</p>
            <div class="box-body" id="clipboard-text">
<?php
$tmp = sprintf('<?php
    $referer = isset($_SERVER[\'HTTP_REFERER\']) ? $_SERVER[\'HTTP_REFERER\'] : \'Direct\';
    echo sprintf("<script src=\'%sjs/jquery.min.js?v=%%s\'></script>",base64_encode($referer));
?>',Router::url('/', true));
highlight_string($tmp);
?>    
                </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>