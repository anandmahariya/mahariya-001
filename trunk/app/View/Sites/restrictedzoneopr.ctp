<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Add&nbsp;Restricted Zone</h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('Back',$back,array('class'=>'btn btn-default btn-sm')); ?>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Form->create('RestrictedZone',array('url' => $formUrl)); ?>
            <?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('country',array('id'=>'country','options'=>$country,'empty'=>'--Select Country--','class'=>'form-control','placeholder'=>'Country','label'=>__('Country'))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('state',array('id'=>'state','options'=>array(),'empty'=>'All','class'=>'form-control','placeholder'=>'State','label'=>__('State'))); ?>
                        </div>
                    </div>
                     <div class="row">
                        <div class="form-group col-lg-5">
                            <?php echo $this->Form->input('city',array('id'=>'city','options'=>array(),'empty'=>'All','class'=>'form-control','placeholder'=>'City','label'=>__('City'))); ?>
                        </div>
                    </div>
                    <div class="row">    
                        <div class="form-group col-lg-2">
                            <label for="Status">Status</label>&nbsp;&nbsp;
                            <?php echo $this->Form->checkbox('status',array('class'=>'form-control','label'=>false)); ?>
                        </div>
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