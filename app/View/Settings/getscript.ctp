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
    $url = sprintf("%sgetscript");
    if(extension_loaded(\'curl\')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, \'h=\'.base64_encode(json_encode($_SERVER)));
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER[\'REQUEST_SCHEME\'].\'://\'.$_SERVER[\'SERVER_NAME\'].$_SERVER[\'REQUEST_URI\']);
        $content = curl_exec($ch);
        curl_close($ch);
        echo $content;
    }
?>',Router::url('/', true));
highlight_string($tmp);
?>    
                </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>