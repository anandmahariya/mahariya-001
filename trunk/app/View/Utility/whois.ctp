<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <?php echo $this->Form->create('search', array('url' => array('controller' => 'utility', 'action' => 'whois'))); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('domain',array('class'=>'form-control','placeholder'=>'Domain','label'=>false)); ?>
                        </div>
                        <div class="form-group col-lg-4">
                            <button type="submit" class="btn btn-primary btn-sm"><?php echo __('Search') ?></button>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header">
                <i class="fa fa-wrench"></i>
                <h3 class="box-title">Origin Autonomous System</h3>
                <div class="pull-right box-tools loading"></div>
            </div>
            <div class="box-body" id="whoiscontainer">
                
            </div>
        </div>
    </div>
</div>