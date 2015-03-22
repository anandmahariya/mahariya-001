<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($this->data['Blockas']['id']) && $this->data['Blockas']['id'] != '' ? 'Edit' : 'Add'; ?>&nbsp;Autonomous System</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',array('controller'=>'settings','action'=>'blockas'),array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('Blockas',array('url' => array('controller' => 'settings', 'action' => 'blockasopr'))); ?>
            <?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Name','label'=>'Name')); ?>
                        </div>
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('as',array('class'=>'form-control','placeholder'=>'Autonomous System Number','label'=>'Autonomous System Number','type'=>'text')); ?>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <?php echo $this->Html->link('Cancel',array('controller'=>'settings','action'=>'blockas'),array('class'=>'btn btn-default')); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>