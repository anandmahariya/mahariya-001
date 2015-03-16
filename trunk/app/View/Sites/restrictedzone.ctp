<div class="row">
     <div class="col-md-12">
          <div class="box box-solid">
               <div class="box-body">
                    <div class="box-group" id="restrictedzone">
                    <?php
                    $box_color = array('box-primary','box-danger','box-success','box-warning');
                    foreach($states as $key=>$val) { ?>
                         <div class="panel box <?php echo $box_color[rand(0,count($box_color) - 1)]; ?>">
                              <div class="tmpheader" data-country="<?php echo $val['State']['country_code'] ?>" data-state="<?php echo $val['State']['code'] ?>">
                                   <div class="box-header">
                                        <h4 class="box-title">
                                             <a href="#collapse-<?php echo $val['State']['code'] ?>">
                                                  <?php echo $val['State']['name']?>
                                             </a>
                                        </h4>
                                   </div>
                              </div>
                              <div id="collapse-<?php echo $val['State']['code'] ?>" class="panel-collapse collapse">
                                   <div class="box-body containerRestrictedZone"></div>
                              </div>
                         </div>
                    <?php } ?>
               </div>
          </div><!-- /.box-body -->
     </div><!-- /.box -->
</div>