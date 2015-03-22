<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <?php echo $this->Form->create('search', array('url' => array('controller' => 'settings', 'action' => 'blockas'))); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('as',array('class'=>'form-control','placeholder'=>'Autonomous System','label'=>__('Origin Autonomous system'))); ?>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo __('Search') ?></button>
                    <div class="box-tools pull-right">
                        <?php echo $this->Html->link('Add New AS',array('controller'=>'settings','action'=>'blockasopr'),array('class'=>'btn btn-success btn-sm')); ?>
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
                            <th><?php echo $this->Paginator->sort('as','Autonomous System Number'); ?></th>
                            <th width="22%">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($data) > 0){
                            $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
                            $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
                            
                            foreach($data as $key=>$val){
                                $delete = _encode(array('id'=>$val['Blockas']['id'],'opr'=>'delete'));
                        ?>
                        <tr>
                            <td><?php echo $val['Blockas']['name']; ?></td>
                            <td><?php echo $val['Blockas']['as']; ?></td>
                            <td>
                                <?php
                                    echo $this->Html->link('Delete',array('controller'=>'settings','action'=>'blockasopr','?'=>array('action'=>$delete)),array('class'=>'btn btn-danger btn-sm confirm','message'=>'Are you sure to delete'));
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
                    $this->Paginator->options(array('update'=>'#content',
                                                    'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
                                                    'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
                                                    'evalScripts'=>true,
                                                    'data'=>http_build_query($this->request->data),
                                                    'method'=>'POST'));
                    if($pagination['pageCount'] > 1){
                ?>
                <!-- Pagination start --->
                <div class="row pull-left">
                    <div id="example2_info" class="dataTables_info"><br>
                        &nbsp;&nbsp; <?php echo $this->Paginator->counter('Showing <b>{:page}</b> - <b>{:pages}</b>, of {:count} total'); ?>
                        &nbsp;<div id="spinner" style="display:none;float:right;"><?php echo $this->Html->image('spinner.gif');?></div>
                    </div>
                </div>
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