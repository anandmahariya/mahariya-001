<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($this->data['Site']['id']) && $this->data['Site']['id'] != '' ? 'Edit' : 'Add'; ?>&nbsp;Site</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',array('controller'=>'sites','action'=>'index'),array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('Site',array('url' => array('controller' => 'sites', 'action' => 'siteopr'))); ?>
            <?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Site Name','label'=>'Site Name')); ?>
                        </div>
                    </div>
                    <div class="row">    
                        <div class="form-group col-lg-5">
                            <label for="Status">Status</label>&nbsp;&nbsp;
                            <?php echo $this->Form->checkbox('status',array('class'=>'form-control','label'=>false)); ?>
                            
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <?php echo $this->Html->link('Cancel',array('controller'=>'sites','action'=>'index'),array('class'=>'btn btn-default')); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>