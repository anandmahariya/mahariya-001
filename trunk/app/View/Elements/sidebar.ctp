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
        <li class="treeview <?php echo $this->name == 'Dashboard' ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-desktop"></i>
                <span><?php echo __('Dashboard') ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="<?php echo in_array($this->action,array('index')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Dashboard'),array('controller' => 'dashboard','action' => 'index'),array('escape'=>false)) ?>
                </li>
                <li class="<?php echo in_array($this->action,array('analytics')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Analytics'),array('controller' => 'dashboard','action' => 'analytics'),array('escape'=>false)) ?>
                </li>
                <li class="<?php echo in_array($this->action,array('search')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Search'),array('controller' => 'dashboard','action' => 'search'),array('escape'=>false)) ?>
                </li>
            </ul>
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
        <li class="treeview <?php echo $this->name == 'Settings' ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-cogs"></i>
                <span><?php echo __('Settings') ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="<?php echo in_array($this->action,array('getscript')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Get Script'),array('controller' => 'settings','action' => 'getscript'),array('escape'=>false)) ?>
                </li>
                <li class="<?php echo in_array($this->action,array('conditions')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Conditions'),array('controller' => 'settings','action' => 'conditions'),array('escape'=>false)) ?>
                </li>
                <li class="<?php echo in_array($this->action,array('blockip','blockipopr')) ? 'active' : '' ?>">
                    <?php echo $this->Html->link('<i class="fa fa-angle-double-right"></i>'.__('Block Ip\'s'),array('controller' => 'settings','action' => 'blockip'),array('escape'=>false)) ?>
                </li>
            </ul>
        </li>
        </ul>
</section>
