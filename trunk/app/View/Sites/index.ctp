<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <?php echo $this->Form->create('search', array('url' => array('controller' => 'sites', 'action' => 'index'))); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Domain Name','label'=>__('Domain Name'))); ?>
                        </div>
                        
                    </div>
                    <div class="row">
                         <div class="form-group col-lg-4">
                            <label for="searchName">Status&nbsp;&nbsp;</label>
                            <?php echo $this->Form->checkbox('status',array('class'=>'form-control','label'=>false,'hiddenField' => false)); ?>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo __('Search') ?></button>
                    <div class="box-tools pull-right">
                        <?php echo $this->Html->link('Add Site',array('controller'=>'sites','action'=>'siteopr'),array('class'=>'btn btn-success btn-sm')); ?>
                    </div>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $this->Paginator->sort('name',__('Name')); ?></th>
                            <th><?php echo $this->Paginator->sort('status','Status'); ?></th>
                            <th width="18%">Replace Area</th>
                            <th width="18%">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($data) > 0){
                            $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
                            $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
                            $skey = Configure::read('Security.salt');
                            
                            foreach($data as $key=>$val){
                                $title = strlen($val['Site']['name']) > Configure::read('title_length') ? substr($val['Site']['name'],0,Configure::read('title_length')).'...' : $val['Site']['name'];
                                $detail = _encode(array('id'=>$val['Site']['id']));
                                $edit = _encode(array('id'=>$val['Site']['id'],'opr'=>'edit'));
                                $delete = _encode(array('id'=>$val['Site']['id'],'opr'=>'delete'));
                        ?>
                        <tr>
                            <td><?php echo $title; ?></td>
                            <?php
                            echo sprintf('<td><a href="javascript:void(0)" id="%d" value="%d" type="site" class="changestatus">%s</a></td>',
                                                 $val['Site']['id'],
                                                 $val['Site']['status'],
                                                 $val['Site']['status'] != 1 ? $false : $true );
                            ?>
                            <td><?php echo $this->Html->link('Replace Area',array('controller'=>'sites','action'=>'replacearea','?'=>array('action'=>$detail)),array('class'=>''));  ?></td>
                            <td>
                                <?php
                                    echo $this->Html->link('Edit',array('controller'=>'sites','action'=>'siteopr','?'=>array('action'=>$edit)),array('class'=>'btn btn-info btn-sm')); 
                                    echo '&nbsp;';
                                    echo $this->Html->link('Delete',array('controller'=>'sites','action'=>'siteopr','?'=>array('action'=>$delete)),array('class'=>'btn btn-danger btn-sm confirm','message'=>'Are you sure to delete'));
                                ?>
                            </td>
                        </tr>
                        <?php }} else { ?>
                        <tr><td colspan="10">No Record Found</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <?php
                    $pagination = $this->Paginator->params();
                    $this->Paginator->options(array('update'=>'#content','evalScripts'=>true,'data'=>http_build_query($this->request->data),'method'=>'POST'));
                    if($pagination['pageCount'] > 1){
                ?>
                <!-- Pagination start --->
                <div class="row pull-right">
                    <div class="col-xs-12">
                        <div class="dataTables_paginate paging_bootstrap">
                            <ul class="pagination">
                                <?php
                                echo $this->Paginator->prev('← '.__('Previous'),array('tag' => 'li','escape' => false),'<a href="javascript:void(0)">← '.__('Previous').'</a>',array('class' => 'prev disabled','tag' => 'li','escape' => false)); 
                                echo $this->Paginator->numbers(array('currentClass'=>'active','separator'=>false,'currentTag'=>'a','tag' => 'li','escape' => false,'modulus'=>3));
                                echo $this->Paginator->next(__('Next').' →',array('tag' => 'li'),'<a href="javascript:void(0)">'.__('Next').' → </a>',array('class' => 'next disabled','tag' => 'li','escape' => false));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Pagination end -->
                <?php echo $this->Js->writeBuffer(); ?>
                <?php } ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>