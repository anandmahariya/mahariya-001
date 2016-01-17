<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <?php echo $this->Form->create('analytics'); ?>
            <div class="box-header">
                <h3 class="box-title">Shorten Analytics</h3>
                <div class="pull-right box-tools">
                    <button title="" data-toggle="tooltip" class="btn btn-danger btn-sm refresh-btn" data-original-title="Reload"><i class="fa fa-refresh"></i></button>
                </div>
                <div class="pull-right box-tools">
                    <?php echo $this->Form->input('date',array('class'=>'form-control','empty'=>false,'value'=>date('d/m/Y'),'label'=>false)); ?>
                </div>
                <div class="pull-right box-tools">
                    <?php echo $this->Form->input('alias',array('class'=>'form-control','value'=>$this->data['analytics']['key'],'label'=>false)); ?>
		        </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="box-body chart-responsive">
                <div class="row">
                        <div class="col-md-12">
                            <!-- DONUT CHART -->
                            <div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title">Request Chart (Hour wise)</h3>
                                </div>
                                <div id="request-analytics-chart-hour-wise" class="box-body chart-responsive" style="height: 270px;"></div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col (LEFT) -->
			
                        <div class="col-md-6">
                            <!-- AREA CHART -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Request Chart (Request type wise)</h3>
                                </div>
                                <div id="request-analytics-chart-vip" class="box-body chart-responsive" style="height: 300px;"></div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col (LEFT) -->
			
			<div class="col-md-6">
                            <!-- AREA CHART -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Operating System</h3>
                                </div>
                                <div id="request-analytics-chart-os" class="box-body chart-responsive" style="height: 300px;"></div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col (LEFT) -->
			
			<div class="col-md-12">
                            <!-- AREA CHART -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Unique Request</h3>
                                </div>
                                <div id="request-analytics-unique-request-chart" class="box-body chart-responsive" style="height: 200px;"></div>
                            </div><!-- /.box -->
                        </div><!-- /.col (LEFT) -->
			
                        <div class="col-md-12">
                            <!-- LINE CHART -->
                            <div class="box box-warning">
                                <div class="box-header">
                                    <h3 class="box-title">Location Chart</h3>
                                </div>
                                <div id="request-analytics-chart-location" class="box-body table-responsive"></div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col (RIGHT) -->
                        
                    </div>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>