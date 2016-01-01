<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($this->data['Domain']['id']) && $this->data['Domain']['id'] != '' ? 'Edit' : 'Add'; ?>&nbsp;Shortern</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',array('controller'=>'shortern','action'=>'index'),array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('Shortern',array('url' => array('controller' => 'shortern', 'action' => 'shorternopr'))); ?>
            <?php echo $this->Form->input('_id',array('type'=>'hidden')); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <?php echo $this->Form->input('url',array('class'=>'form-control','placeholder'=>'Url','label'=>'Url')); ?>
                        </div>
                    </div>
                    <div class="row">   
                        <div class="form-group col-lg-3">
                            <?php echo $this->Form->input('alias',array('class'=>'form-control','placeholder'=>'Alias','label'=>'Alias')); ?>
                        </div> 
                        <div class="form-group col-lg-3">
                            <?php echo $this->Form->input('password',array('class'=>'form-control','placeholder'=>'Password','label'=>'Password')); ?>
                        </div> 
                        <div class="form-group col-lg-3">
                            <?php echo $this->Form->input('redirect',array('class'=>'form-control','options'=>$redirect)); ?>
                        </div> 
                        <div class="form-group col-lg-3">
                            <?php echo $this->Form->input('domain',array('class'=>'form-control','options'=>$domains)); ?>
                        </div>
                    </div>
                    <div class="row">   
                        <div class="form-group col-lg-12">
                            <label for="Status">Description</label>
                            <?php echo $this->Form->textarea('description',array('class'=>'form-control','placeholder'=>'Description','label'=>false)); ?>
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
                    <?php echo $this->Html->link('Cancel',array('controller'=>'shortern','action'=>'index'),array('class'=>'btn btn-default')); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>