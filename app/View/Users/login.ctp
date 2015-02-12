<div class="form-box" id="login-box">
    <div class="header">Sign In</div>
    <?php echo $this->Form->create(null, array('url' => array('controller' => 'users', 'action' => 'login'))); ?>
        <div class="body bg-gray">
            <?php echo $this->Session->flash(); ?>
            <div class="form-group">
                <?php echo $this->Form->input('username',array('class'=>'form-control','placeholder'=>'User ID','label'=>false)); ?>
            </div>
            <div class="form-group">
                <?php echo $this->Form->password('password',array('class'=>'form-control','placeholder'=>'Password')); ?>
            </div>          
            <div class="form-group">
                <?php echo $this->Form->checkbox('remember_me', array('hiddenField' => false)); ?> Remember me
            </div>
        </div>
        <div class="footer">                                                               
            <button type="submit" class="btn bg-olive btn-block">Sign me in</button>  
            <!--
            <p><a href="#">I forgot my password</a></p>
            <a href="register.html" class="text-center">Register a new membership</a>
            -->
        </div>
    <?php echo $this->Form->end(); ?>
    
    <div class="margin text-center">
        <?php echo $this->Html->link('<i class="fa fa-facebook"></i>',array('controller'=>'users','action'=>'socialconnect','?'=>array('provider'=>'facebook')),array('class'=>'btn bg-light-blue btn-circle','escape' => false)); ?>
        <?php echo $this->Html->link('<i class="fa fa-google-plus"></i>',array('controller'=>'users','action'=>'socialconnect','?'=>array('provider'=>'google')),array('class'=>'btn bg-red btn-circle','escape' => false)); ?>
    </div>
</div>