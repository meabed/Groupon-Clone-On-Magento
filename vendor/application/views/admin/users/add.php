<div class="container top">

    <ul class="breadcrumb">
        <li>
            <a href="<?php echo site_url("admin"); ?>">
                <?php echo ucfirst($this->uri->segment(1)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li>
            <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>">
                <?php echo ucfirst($this->uri->segment(2)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li class="active">
            <a href="#">New</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Adding <?php echo ucfirst($this->uri->segment(2)); ?>
        </h2>
    </div>

    <?php
    //flash messages
    if (isset($flash_message)) {
        if ($flash_message == 'success') {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> new user created with success.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> '.$flash_message.'.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();

    echo form_open('admin/users/add', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="name" class="control-label">Name</label>

            <div class="controls">
                <input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label for="username" class="control-label">Username</label>

            <div class="controls">
                <input type="text" id="username" name="username" value="<?php echo set_value('username'); ?>">
            </div>
        </div>
        <div class="control-group">
            <label for="email" class="control-label">Email</label>

            <div class="controls">
                <input type="text" id="email" name="email" value="<?php echo set_value('email'); ?>">
            </div>
        </div>

        <div class="control-group">
            <label for="password" class="control-label">Password</label>

            <div class="controls">
                <input type="password" id="password" name="password" value="">
            </div>
        </div>
        <div class="control-group">
            <label for="is_admin" class="control-label">Super User</label>

            <div class="controls">
                <?php echo form_dropdown('is_admin', array('0' => 'No', '1' => 'Yes'), set_value('is_admin')); ?>
            </div>
        </div>

        <div class="control-group">
            <label for="active" class="control-label">Active</label>

            <div class="controls">
                <?php echo form_dropdown('active', array('0' => 'No', '1' => 'Yes'), set_value('active')); ?>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
        </div>
    </fieldset>
    <?php echo form_close(); ?>

</div>
     