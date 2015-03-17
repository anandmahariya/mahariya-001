<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <?php echo $this->Form->create('options', array('url' => array('controller' => 'settings', 'action' => 'conditions'))); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-2">
                            Bypass IP
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('bypass_ip',array('class'=>'form-control','label'=>false)); ?>
                        </div>
                        <div class="form-group col-lg-6">
                            <p class="text-green">Your Ip : <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-2">
                            User Valid Hits
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('valid_hits',array('class'=>'form-control','options'=>array_combine(range(1,10), range(1,10)),'label'=>false)); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-2">
                            Site Referer
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('site_referer',array('class'=>'form-control','type' => 'textarea','label'=>false)); ?>
                        </div>
                        <div class="form-group col-lg-6">
                            <p>Note : Insert all the site referer url you want to allow</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-2">
                            Zones
                        </div>
                        <div class="form-group col-lg-4">
                            <?php
                                echo $this->Form->radio('zone',
                                                        array('valid'=>'&nbsp;&nbsp;Valid Zone&nbsp;&nbsp;&nbsp;&nbsp;',
                                                              'restricted'=>'&nbsp;&nbsp;Restricted Zone'),
                                                        array('legend' => false)
                                                        );
                            ?>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo __('Save') ?></button>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>