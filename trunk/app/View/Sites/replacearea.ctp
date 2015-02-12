<?php
    $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
    $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
?>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Client Zone</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',$back,array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
             <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($client) > 0){
                                foreach($client as $key=>$val){
                                    
                                    $edit = $this->html->url(array('controller'=>'sites',
                                                                   'action'=>'replaceropr',
                                                                   '?'=>array('action'=>_encode(array('id'=>$val['Replacer']['id'],'site_id'=>$site_id,'owner'=>0,'opr'=>'edit')))));
                                    
                                    $delete = $this->html->url(array('controller'=>'sites',
                                                                   'action'=>'replaceropr',
                                                                   '?'=>array('action'=>_encode(array('id'=>$val['Replacer']['id'],'site_id'=>$site_id,'owner'=>0,'opr'=>'delete')))));
                                    
                                    echo '<tr>';
                                    echo sprintf('<td>%s</td>',$val['Replacer']['type']);
                                    echo sprintf('<td>%s</td>',$val['Replacer']['name']);
                                    echo sprintf('<td width="250px">%s</td>',$val['Replacer']['content']);
                                    echo sprintf('<td><a href="javascript:void(0)" id="%d" value="%d" type="replacer" class="changestatus">%s</a></td>',
                                                 $val['Replacer']['id'],
                                                 $val['Replacer']['status'],
                                                 $val['Replacer']['status'] != 1 ? $false : $true );
                                    
                                    
                                    echo sprintf('<td width="150px"><a class="btn btn-info btn-sm" href="%s">Edit</a>&nbsp;
                                                 <a class="btn btn-danger btn-sm confirm" href="%s">delete</a></td>',$edit,$delete);
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
                    <?php
                    echo $this->Html->link('Add Replacer',array('controller'=>'sites','action'=>'replaceropr','?'=>array('action'=>_encode(array('site_id'=>$site_id,'owner'=>0)))),array('class'=>'btn btn-success btn-sm'));
                    ?>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>

<?php /*
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Admin Zone</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',$back,array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
             <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($owner) > 0){
                                foreach($owner as $key=>$val){
                                    
                                    $edit = $this->html->url(array('controller'=>'sites',
                                                                   'action'=>'replaceropr',
                                                                   '?'=>array('action'=>_encode(array('id'=>$val['Replacer']['id'],'site_id'=>$site_id,'owner'=>1,'opr'=>'edit')))));
                                    
                                    $delete = $this->html->url(array('controller'=>'sites',
                                                                   'action'=>'replaceropr',
                                                                   '?'=>array('action'=>_encode(array('id'=>$val['Replacer']['id'],'site_id'=>$site_id,'owner'=>1,'opr'=>'delete')))));
                                    
                                    echo '<tr>';
                                    echo sprintf('<td>%s</td>',$val['Replacer']['type']);
                                    echo sprintf('<td>%s</td>',$val['Replacer']['name']);
                                    echo sprintf('<td width="250px">%s</td>',$val['Replacer']['content']);
                                    echo sprintf('<td>%s</td>',$val['Replacer']['status'] != 1 ? $false : $true );
                                    echo sprintf('<td width="150px"><a class="btn btn-info btn-sm" href="%s">Edit</a>&nbsp;
                                                 <a class="btn btn-danger btn-sm confirm" href="%s">delete</a></td>',$edit,$delete);
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
                    <?php
                    echo $this->Html->link('Add Replacer',array('controller'=>'sites','action'=>'replaceropr','?'=>array('action'=>_encode(array('site_id'=>$site_id,'owner'=>1)))),array('class'=>'btn btn-success btn-sm'));
                    ?>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>
*/ ?>