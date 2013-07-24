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
        if ($flash_message == TRUE) {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> new product created with success.';
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
    $options_vendor = array('' => "Select");
    foreach ($vendors as $row) {
        $options_vendor[$row['id']] = $row['name'];
    }

    //form validation
    echo validation_errors();

    echo form_open('admin/products/add', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="name" class="control-label">Product Name</label>

            <div class="controls">
                <input type="text" id="name" name="name"  class="long" value="<?php echo set_value('name'); ?>">
                <span class="help-inline">This name will show on the product page</span>

            </div>
        </div>
        <div class="control-group">
            <label for="short_name" class="control-label">Product Short Name</label>

            <div class="controls">
                <input type="text" id="short_name" name="short_name"  class="long" value="<?php echo set_value('short_name'); ?>">
                <span class="help-inline">This name will show on the home page/category page</span>
            </div>
        </div>
        <div class="control-group">
            <label for="description" class="control-label">Description</label>

            <div class="controls">
                <textarea id="description" name="description" rows="20"><?php echo set_value('description'); ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="fine_print" class="control-label">Fine Print</label>

            <div class="controls">
                <textarea id="fine_print" name="fine_print" rows="10"><?php echo set_value('fine_print'); ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="highlight" class="control-label">Highlights</label>

            <div class="controls">
                <textarea id="highlight" name="highlight" rows="10"><?php echo set_value('highlight'); ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="start_date" class="control-label">Start Date</label>

            <div class="controls">
                <input type="text" id="start_date" name="start_date" value="<?php echo set_value('start_date'); ?>" data-date-format="yyyy-mm-dd">
            </div>
        </div>
        <div class="control-group">
            <label for="end_date" class="control-label">End Date</label>

            <div class="controls">
                <input type="text" id="end_date" name="end_date" value="<?php echo set_value('end_date'); ?>" data-date-format="yyyy-mm-dd">
            </div>
        </div>
        <div class="control-group">
            <label for="stock" class="control-label">Stock</label>

            <div class="controls">
                <input type="text" id="stock" name="stock" value="<?php echo set_value('stock'); ?>">
                <span class="help-inline">Leave it empty for unlimited stock</span>

            </div>
        </div>
        <div class="control-group">
            <label for="cost_price" class="control-label">Original Price</label>
            <div class="controls">
                <input type="text" id="cost_price" name="cost_price" value="<?php echo set_value('cost_price'); ?>">
                <span class="help-inline">Currency AED</span>
            </div>
        </div>
        <div class="control-group">
            <label for="sell_price" class="control-label">Selling Price</label>
            <div class="controls">
                <input type="text" id="sell_price" name="sell_price" value="<?php echo set_value('sell_price'); ?>">
                <span class="help-inline">Currency AED</span>
            </div>
        </div>
        <div class="control-group">
            <label for="active" class="control-label">Active</label>
            <div class="controls">
                <?php echo form_dropdown('active', array('0'=>'No','1'=>'Yes'), set_value('active'));?>
            </div>
        </div>

        <?php
        echo '<div class="control-group">';
        echo '<label for="vendor_id" class="control-label">vendor</label>';
        echo '<div class="controls">';
        //echo form_dropdown('vendor_id', $options_vendor, '', 'class="span2"');

        echo form_dropdown('vendor_id', $options_vendor, set_value('vendor_id'), 'class="span2"');

        echo '</div>';
        echo '</div">';
        ?>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
<script type="text/javascript">
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    var start_date = $('#start_date').datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
    }).on('changeDate', function(ev) {
            if (ev.date.valueOf() > end_date.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                end_date.setValue(newDate);
            }
            start_date.hide();
            $('#end_date')[0].focus();
        }).data('datepicker');
    var end_date = $('#end_date').datepicker({
        onRender: function(date) {
            return date.valueOf() <= start_date.date.valueOf() ? 'disabled' : '';
        }
    }).on('changeDate', function(ev) {
            end_date.hide();
        }).data('datepicker');

    tinymce.init({
        selector: "textarea",
// ===========================================
// INCLUDE THE PLUGIN
// ===========================================
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste jbimages"
        ],
// ===========================================
// PUT PLUGIN'S BUTTON on the toolbar
// ===========================================
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
// ===========================================
// SET RELATIVE_URLS to FALSE (This is required for images to display properly)
// ===========================================
        relative_urls: false
    });

</script>