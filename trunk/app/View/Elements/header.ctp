<?php $user =  $this->Session->read('Auth.User'); ?>
<header class="header">
            <a href="<?php echo Router::url('/', true); ?>" class="logo"><?php echo __('Riseup 24*7'); ?></a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __(ucfirst($this->Session->read('Config.language'))); ?><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__('English'),array('controller'=>'users','action'=>'changelanguage/english'),array('class'=>'changelanguage')); ?></li>
                                <li><?php echo $this->Html->link(__('Hindi'),array('controller'=>'users','action'=>'changelanguage/hindi'),array('class'=>'changelanguage')); ?></li>
                            </ul>
                        </li>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo ucwords($user['username']) ?><i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?php echo Router::url('/', true); ?>img/avatar3.png" class="img-circle" alt="User Image" />
                                    <p>
                                        <?php echo ucwords($user['username']) ?> - Admin
                                        <small>Member since <?php echo date('M, Y',strtotime($user['created'])); ?></small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <!-- <a href="<?php echo Router::url('/users/profile/'.base64_encode($user['id']), true); ?>" class="btn btn-default btn-flat">Profile</a> -->
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo Router::url('/users/logout', true); ?>" class="btn btn-default btn-flat"><?php echo __('Sign out'); ?></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        