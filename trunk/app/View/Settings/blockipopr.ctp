<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($this->data['Blockip']['id']) && $this->data['Blockip']['id'] != '' ? 'Edit' : 'Add'; ?>&nbsp;Range</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',array('controller'=>'settings','action'=>'blockip'),array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('Blockip',array('url' => array('controller' => 'settings', 'action' => 'blockipopr'))); ?>
            <?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Name','label'=>'Name')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('start',array('class'=>'form-control','placeholder'=>'Name','label'=>'Start Range','type'=>'text','maxlength'=>16)); ?>
                        </div>
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('end',array('class'=>'form-control','placeholder'=>'Name','label'=>'End Range','type'=>'text','maxlength'=>16)); ?>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <?php echo $this->Html->link('Cancel',array('controller'=>'settings','action'=>'blockip'),array('class'=>'btn btn-default')); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>