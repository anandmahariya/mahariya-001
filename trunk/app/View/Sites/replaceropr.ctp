<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($this->data['Replacer']['id']) && $this->data['Replacer']['id'] != '' ? 'Edit' : 'Add'; ?>&nbsp;Replacer</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',$back,array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('Replacer',array('url' => $formUrl)); ?>
            <?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
            <?php echo $this->Form->input('site_id',array('type'=>'hidden','value'=>$site_id)); ?>
            <?php echo $this->Form->input('owner',array('type'=>'hidden','value'=>$owner)); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('type',array('options'=>$type,'empty'=>'--Select Type--','class'=>'form-control','placeholder'=>'Type','label'=>__('Container Type'))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Container Name','label'=>'Container Name')); ?>
                        </div>
                    </div>
                     <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('content',array('class'=>'form-control','label'=>'Container Value')); ?>
                        </div>
                    </div>
                    <div class="row">    
                        <div class="form-group col-lg-2">
                            <label for="Status">Status</label>&nbsp;&nbsp;
                            <?php echo $this->Form->checkbox('status',array('class'=>'form-control','label'=>false)); ?>
                            
                        </div>
                    </div>
                    <div class="row">    
                        
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <?php echo $this->Html->link('Cancel',$back,array('class'=>'btn btn-default')); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>