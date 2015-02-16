<div class="row">
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?=isset($sites['total']) && $sites['total'] > 0 ? $sites['total'] : 0?></h3>
                <p>Total Sites</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'sites','action'=>'index'),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?=isset($sites['enable']) && $sites['enable'] > 0 ? $sites['enable'] : 0?></h3>
                <p>Enable Sites</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'sites','action'=>'index','?'=>array('s'=>_encode(array('status'=>1)))),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?=isset($sites['disable']) && $sites['disable'] > 0 ? $sites['disable'] : 0?></h3>
                <p>Disable Sites</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'sites','action'=>'index','?'=>array('s'=>_encode(array('status'=>0)))),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-light-blue">
            <div class="inner">
                <h3 class="tot-reqest"><?=isset($requests['total']) && $requests['total'] > 0 ? $requests['total'] : 0?></h3>
                <p>Total Request</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'dashboard','action'=>'search'),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 class="tot-valid"><?=isset($requests['valid']) && $requests['valid'] > 0 ? $requests['valid'] : 0?></h3>
                <p>Valid Request</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'dashboard','action'=>'search','?'=>array('s'=>_encode(array('valid'=>1)))),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3 class="tot-invalid"><?=isset($requests['invalid']) && $requests['invalid'] > 0 ? $requests['invalid'] : 0?></h3>
                <p>In-Valid Request</p>
            </div>
        <div class="icon"><i class="ion ion-stats-bars"></i></div>
        <?php echo $this->Html->link('More info <i class="fa fa-arrow-circle-right"></i>',
                                     array('controller'=>'dashboard','action'=>'search','?'=>array('s'=>_encode(array('valid'=>0)))),
                                     array('class'=>'small-box-footer','escape'=>false)); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="pull-right box-tools">
                    <a href="javascript:void(0)" data-widget="collapse" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></a>
		</div>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="topip-chart" style="height:250px;"></div>
            </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="pull-right box-tools">
		    <a href="javascript:void(0)" data-widget="collapse" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></a>
		</div>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="topip-chart" style="height:250px;"></div>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <?php echo $this->Form->create('request'); ?>
            <div class="box-header">
                <h3 class="box-title">Request Chart</h3>
                <div class="pull-right box-tools">
                    <?php echo $this->Form->year('year', 2014, date('Y'),array('class'=>'form-control','name'=>'data[request][year]','empty'=>false,'value'=>date('Y'))); ?>
                </div>
                <div class="pull-right box-tools">
                    <?php echo $this->Form->month('month',array('class'=>'form-control','name'=>'data[request][month]','empty'=>false,'value'=>date('m'))); ?>
		</div>
                <div class="pull-right box-tools">
                    <?php echo $this->Form->input('site',array('class'=>'form-control','name'=>'data[request][site]','options' => $sites_array, 'value' => '','label'=>false)); ?>
		</div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="box-body chart-responsive">
                <div class="chart" id="request-chart" style="height: 300px;"></div>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>