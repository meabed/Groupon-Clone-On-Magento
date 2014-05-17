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
            <a href="#">Update</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Updating <?php echo ucfirst($this->uri->segment(2)); ?>
        </h2>
    </div>


    <?php
    //flash messages
    if ($this->session->flashdata('flash_message')) {
        if ($this->session->flashdata('flash_message') == 'updated') {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> user updated with success.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> change a few things up and try submitting again.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();
    $vendor = $vendor[0];
    echo form_open('admin/users/update/' . $this->uri->segment(4) . '', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="name" class="control-label">Name</label>

            <div class="controls">
                <input type="text" id="name" name="name" value="<?php echo $vendor['name']; ?>">
            </div>
        </div>

        <div class="control-group">
            <label for="email" class="control-label">Email</label>

            <div class="controls">
                <input type="text" id="email" name="email" <?!is_admin() ? 'disabled="disabled"' : '' ;?> value="<?php echo $vendor['email']; ?>">
            </div>
        </div>

        <div class="control-group">
            <label for="password" class="control-label">Password</label>

            <div class="controls">
                <input type="password" id="password" name="password" value="">
                &nbsp;<small>Leave it <u>empty</u> if you don't want to change it</small>
            </div>
        </div>
        <?php if (is_admin()): ?>
            <div class="control-group">
                <label for="is_admin" class="control-label">Super User</label>

                <div class="controls">
                    <?php echo form_dropdown('is_admin', array('0'=>'No','1'=>'Yes'), $vendor['is_admin']);?>
                </div>
            </div>

            <div class="control-group">
                <label for="active" class="control-label">Active</label>

                <div class="controls">
                    <?php echo form_dropdown('active', array('0'=>'No','1'=>'Yes'), $vendor['active']);?>
                </div>
            </div>

            <div class="control-group">
                <label for="created_at" class="control-label">Create At</label>

                <div class="controls">
                    <input type="text" id="created_at" name="created_at" disabled="disabled" value="<?php echo $vendor['created_at']; ?>">
                </div>
            </div>

            <div class="control-group">
                <label for="updated_at" class="control-label">Updated At</label>

                <div class="controls">
                    <input type="text" id="updated_at" name="updated_at" disabled="disabled" value="<?php echo $vendor['updated_at']; ?>">
                </div>
            </div>
            <div class="control-group">
                <label for="last_login" class="control-label">Last Login</label>

                <div class="controls">
                    <input type="text" id="last_login" name="last_login" disabled="disabled" value="<?php echo $vendor['last_login']; ?>">
                </div>
            </div>
        <?php endif; ?>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     