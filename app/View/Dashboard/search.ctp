<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <?php echo $this->Form->create('search', array('url' => array('controller' => 'dashboard', 'action' => 'search'))); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('ip',array('class'=>'form-control','placeholder'=>'Ip','label'=>__('Ip'))); ?>
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('site',array('class'=>'form-control','options'=>$sites_array,'empty'=>'--Select Site--','label'=>__('Site'))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('country',array('id'=>'country','class'=>'form-control','options'=>$country,'empty'=>'--Select Country--','label'=>__('Country'))); ?>
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('state',array('id'=>'state','options'=>$state,'empty'=>'All','class'=>'form-control','placeholder'=>'State','label'=>__('State'))); ?>
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('city',array('id'=>'city','options'=>$city,'empty'=>'All','class'=>'form-control','placeholder'=>'City','label'=>__('City'))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('startdate',array('class'=>'form-control datepicker','placeholder'=>'Start Date','autocomplete'=>'off','label'=>__('Start Date'))); ?>
                        </div>
                        <div class="form-group col-lg-4">
                            <?php echo $this->Form->input('enddate',array('class'=>'form-control datepicker','placeholder'=>'End Date','autocomplete'=>'off','label'=>__('End Date'))); ?>
                        </div>
                    </div>
                    <?php /*
                    <div class="row">
                        <div class="form-group col-lg-2">
                            <label for="searchName">Valid&nbsp;&nbsp;</label>
                            <?php echo $this->Form->checkbox('valid',array('class'=>'form-control','label'=>false,'hiddenField' => false)); ?>
                        </div>
                        <div class="form-group col-lg-2">
                            <label for="searchName">In-Valid&nbsp;&nbsp;</label>
                            <?php echo $this->Form->checkbox('invalid',array('class'=>'form-control','label'=>false,'hiddenField' => false)); ?>
                        </div>
                    </div>
                    */ ?>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo __('Search') ?></button>
                </div>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.box -->
    </div><!--/.col (left) -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Ip</th>
                            <th><?php echo $this->Paginator->sort('country','Country'); ?></th>
                            <th><?php echo $this->Paginator->sort('state','State'); ?></th>
                            <th><?php echo $this->Paginator->sort('city','City'); ?></th>
                            <th><?php echo $this->Paginator->sort('site','Site'); ?></th>
                            <th>Referer</th>
                            <th>Site Referer</th>
                            <th>Proxy</th>
                            <th><?php echo $this->Paginator->sort('valid'); ?></th>
                            <th><?php echo $this->Paginator->sort('created','Request Time'); ?></th>
                        </tr>
                        <?php
                            $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
                            $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
                        ?>
                        <?php
                            foreach($data as $key=>$val) {
                                $referer = $val['Request']['referer'];
                                $tmp = parse_url($val['Request']['site_referer']);
                                $sReferer = isset($tmp['host']) ? $tmp['host'] : 'direct';
                        ?>
                        <tr>
                            <td><?php echo $val['Request']['ip'] ?></td>
                            <td><?php echo $val['ip']['country'] ?></td>
                            <td><?php echo $val['ip']['state'] ?></td>
                            <td><?php echo $val['ip']['city'] ?></td>
                            <td><?php echo $val['0']['site'] ?></td>
                            <td><a href="javascript:void(0)" title="<?php echo $val['Request']['referer'] ?>"><?php echo $val['Request']['referer']; ?></a></td>
                            <td><a href="javascript:void(0)" title="<?php echo $val['Request']['site_referer'] ?>"><?php echo $sReferer; ?></a></td>
                            <td><?php echo $val['Request']['proxy'] == 1 ? $true : $false ?></td>
                            <td><?php echo $val['Request']['valid'] == 1 ? $true : $false ?></td>
                            <td><?php echo date('d M Y h:i:s A',strtotime($val['Request']['created'])) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                    $pagination = $this->Paginator->params();
                    $this->Paginator->options(array('update'=>'#content','evalScripts'=>true,'data'=>http_build_query($this->request->data),'method'=>'POST'));
                    if($pagination['pageCount'] > 1){
                ?>
                <!-- Pagination start --->
                <div class="row pull-left">
                    <div id="example2_info" class="dataTables_info"><br>
                        &nbsp;&nbsp; <?php echo $this->Paginator->counter('Showing <b>{:page}</b> - <b>{:pages}</b>, of {:count} total'); ?>
                    </div>
                </div>
                <div class="row pull-right">
                    <div class="col-xs-12">
                        <div class="dataTables_paginate paging_bootstrap">
                            <ul class="pagination">
                                <?php
                                echo $this->Paginator->prev('← '.__('Previous'),array('tag' => 'li','escape' => false),'<a href="javascript:void(0)">← '.__('Previous').'</a>',array('class' => 'prev disabled','tag' => 'li','escape' => false)); 
                                echo $this->Paginator->numbers(array('currentClass'=>'active','separator'=>false,'currentTag'=>'a','tag' => 'li','escape' => false,'modulus'=>3));
                                echo $this->Paginator->next(__('Next').' →',array('tag' => 'li'),'<a href="javascript:void(0)">'.__('Next').' → </a>',array('class' => 'next disabled','tag' => 'li','escape' => false));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Pagination end -->
                <?php echo $this->Js->writeBuffer(); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>