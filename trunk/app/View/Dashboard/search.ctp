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
                            <th>Page Url</th>
                            <th>Site Referer</th>
                            <th>Proxy</th>
                            <th>Mobile</th>
                            <th><?php echo $this->Paginator->sort('valid'); ?></th>
                            <th><?php echo $this->Paginator->sort('created','Request Time'); ?></th>
                        </tr>
                        <?php
                            $true = $this->Html->image('test-pass-icon.png',array('alt'=>'Active'));
                            $false = $this->Html->image('test-fail-icon.png',array('alt'=>'De-active'));
                        ?>
                        <?php
                        
                            
                            App::import('Controller', 'Validate');
                            $tmp = new ValidateController;
                            $validZone = $tmp->getValidZone();
                                
                            foreach($data as $key=>$val) {
                                $referer = $val['Request']['referer'];
                                $tmp = parse_url($val['Request']['site_referer']);
                                $sReferer = isset($tmp['host']) ? $tmp['host'] : 'direct';
                                
                                $country = $state = $city = 'text-red';
                                if(array_key_exists($val['ip']['country_code'],$validZone)){
                                    $country = 'text-green';
                                    if(array_key_exists($val['ip']['state'],$validZone[$val['ip']['country_code']])){
                                        $state = 'text-green';
                                        if(array_key_exists('*',$validZone[$val['ip']['country_code']][$val['ip']['state']])){
                                            $city = 'text-green';
                                        }elseif(array_key_exists($val['ip']['city'],$validZone[$val['ip']['country_code']][$val['ip']['state']])){
                                            $city = 'text-green';
                                        }
                                    }
                                }
                        ?>
                        <tr>
                            <td><a data-toggle="tooltip" data-original-title="<?php echo $val['ip']['dns']?>" href="javascript:void(0)"><?php echo $val['Request']['ip'] ?></a></td>
                            <td class="<?php echo $country; ?>"><?php echo $val['ip']['country'] ?></td>
                            <td class="<?php echo $state; ?>"><?php echo $val['ip']['state'] ?></td>
                            <td class="<?php echo $city; ?>"><?php echo $val['ip']['city'] ?></td>
                            <td><a href="javascript:void(0)" title="<?php echo $val['Request']['referer'] ?>"><?php echo $val['Request']['referer']; ?></a></td>
                            <td><a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php echo $val['Request']['site_referer']?>"><?php echo $sReferer; ?></a></td>
                            <td><?php echo $val['Request']['proxy'] == 1 ? $true : $false ?></td>
                            <td><?php echo $val['Request']['mobile'] == 1 ? $true : $false ?></td>
                            <td><a data-toggle="tooltip" data-original-title="<?php echo $val['Request']['comments']?>" href="javascript:void(0)"><?php echo $val['Request']['valid'] == 1 ? $true : $false ?></a></td>
                            <td><?php echo date('d M Y h:i:s A',strtotime($val['Request']['created'])) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                    $pagination = $this->Paginator->params();
                    $this->Paginator->options(array('update'=>'#content',
                                                    'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
                                                    'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
                                                    'evalScripts'=>true,
                                                    'data'=>http_build_query($this->request->data),
                                                    'method'=>'POST'));
                    
                    if($pagination['pageCount'] > 1){
                ?>
                <!-- Pagination start --->
                <div class="row pull-left">
                    <div id="example2_info" class="dataTables_info"><br>
                        &nbsp;&nbsp; <?php echo $this->Paginator->counter('Showing <b>{:page}</b> - <b>{:pages}</b>, of {:count} total'); ?>
                        &nbsp;<div id="spinner" style="display:none;float:right;"><?php echo $this->Html->image('spinner.gif');?></div>
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
<script>
    $("[data-toggle='tooltip']").tooltip();
</script>