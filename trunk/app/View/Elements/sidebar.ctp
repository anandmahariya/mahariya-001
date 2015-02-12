<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
        <div class="pull-left image">
            <img src="<?php echo Router::url('/', true); ?>img/avatar3.png" class="img-circle" alt="User Image" />
        </div>
        <div class="pull-left info">
            <?php $user =  $this->Session->read('Auth.User'); ?>
            <p><?php echo __('Hello') ?>, <?php echo ucwords($user['username']) ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i><?php echo __('Online') ?></a>
        </div>
    </div>
    
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <li class="<?php echo $this->name == 'Dashboard' ? 'active' : '' ?>">
            <?php echo $this->Html->link('<i class="fa fa-dashboard"></i> <span>'.__('Dashboard').'</span>',array('controller' => 'dashboard','action' => 'index'),array('escape'=>false)) ?>
        </li>
        <li class="treeview <?php echo $this->name == 'Sites' ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-desktop"></i>
                <span><?php echo __('Sites') ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="<?php echo in_array($this->action,array('index')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Site List'),array('controller' => 'sites','action' => 'index'),array('escape'=>false)) ?>
                </li>
                <li class="<?php echo in_array($this->action,array('validzone','validzoneopr')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Valid Zone'),array('controller' => 'sites','action' => 'validzone'),array('escape'=>false)) ?>
                </li>
            </ul>
        </li>
        </ul>
</section>