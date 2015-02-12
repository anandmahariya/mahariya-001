<?php
    $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
    $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
?>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($data) > 0){
                                foreach($data as $key=>$val){
                                   $delete = $this->html->url(array('controller'=>'sites',
                                                                   'action'=>'adminzoneopr',
                                                                   '?'=>array('action'=>_encode(array('id'=>$val['AdminZone']['id'],'opr'=>'delete','site_id'=>$site_id)))));
                                    
                                    echo '<tr>';
                                    echo sprintf('<td>%s</td>',$val['AdminZone']['country']);
                                    echo sprintf('<td>%s</td>',$val['AdminZone']['state']);
                                    echo sprintf('<td>%s</td>',$val['AdminZone']['city']);
                                    echo sprintf('<td>%s</td>',$val['AdminZone']['status'] != 1 ? $false : $true );
                                    echo sprintf('<td width="75px"><a class="btn btn-danger btn-sm confirm" href="%s">delete</a></td>',$delete);
                                    echo '</tr>';
                                }
                            }else{
                                echo '<tr><td colspan="5">No Record Found</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <div class="row pull-right">
                    <div class="col-xs-12">
                    <?php echo $this->Html->link('Back',$back,array('class'=>'btn btn-default btn-sm')); ?>
                    <?php
                    echo $this->Html->link('Add Admin Zone',array('controller'=>'sites','action'=>'adminzoneopr','?'=>array('action'=>_encode(array('site_id'=>$site_id,'opr'=>'add')))),array('class'=>'btn btn-success btn-sm'));
                    ?>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>