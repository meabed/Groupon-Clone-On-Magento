    <div class="container top">
      
      <ul class="breadcrumb">
        <li>
          <a href="<?php echo site_url("admin"); ?>">
            <?php echo ucfirst($this->uri->segment(1));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li>
          <a href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>">
            <?php echo ucfirst($this->uri->segment(2));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          <a href="#">Update</a>
        </li>
      </ul>
      
      <div class="page-header">
        <h2>
          Updating <?php echo ucfirst($this->uri->segment(2));?>
        </h2>
      </div>


      <?php
      //flash messages
      if($this->session->flashdata('flash_message')){
        if($this->session->flashdata('flash_message') == 'updated')
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> product updated with success.';
          echo '</div>';       
        }else{
          echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> change a few things up and try submitting again.';
          echo '</div>';          
        }
      }
      ?>
      
      <?php
      //form data
      $product = $product[0];
      $attributes = array('class' => 'form-horizontal', 'id' => '');
      $options_vendor = array('' => "Select");
      foreach ($vendors as $row)
      {
          $options_vendor[$row['id']] = $row['name'];
      }

      //form validation
      echo validation_errors();

      echo form_open('admin/products/update/'.$this->uri->segment(4).'', $attributes);
      ?>
        <fieldset>
            <div class="control-group">
                <label for="name" class="control-label">Product Name</label>

                <div class="controls">
                    <input type="text" id="name" name="name"  class="long" value="<?php echo $product['name']; ?>">
                    <span class="help-inline">This name will show on the product page</span>

                </div>
            </div>
            <div class="control-group">
                <label for="short_name" class="control-label">Product Short Name</label>

                <div class="controls">
                    <input type="text" id="short_name" name="short_name"  class="long" value="<?php echo $product['short_name']; ?>">
                    <span class="help-inline">This name will show on the home page/category page</span>
                </div>
            </div>
            <div class="control-group">
                <label for="description" class="control-label">Description</label>

                <div class="controls">
                    <textarea id="description" name="description" rows="20"><?php echo $product['description']; ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="fine_print" class="control-label">Fine Print</label>

                <div class="controls">
                    <textarea id="fine_print" name="fine_print" rows="10"><?php echo $product['fine_print']; ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="highlight" class="control-label">Highlights</label>

                <div class="controls">
                    <textarea id="highlight" name="highlight" rows="10"><?php echo $product['highlight']; ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="start_date" class="control-label">Start Date</label>

                <div class="controls">
                    <input type="text" id="start_date" name="start_date" value="<?php echo $product['start_date']; ?>" data-date-format="yyyy-mm-dd">
                </div>
            </div>
            <div class="control-group">
                <label for="end_date" class="control-label">End Date</label>

                <div class="controls">
                    <input type="text" id="end_date" name="end_date" value="<?php echo $product['end_date']; ?>" data-date-format="yyyy-mm-dd">
                </div>
            </div>
            <div class="control-group">
                <label for="stock" class="control-label">Stock</label>

                <div class="controls">
                    <input type="text" id="stock" name="stock" value="<?php echo $product['stock']; ?>">
                    <span class="help-inline">Leave it empty for unlimited stock</span>

                </div>
            </div>
            <div class="control-group">
                <label for="cost_price" class="control-label">Original Price</label>
                <div class="controls">
                    <input type="text" id="cost_price" name="cost_price" value="<?php echo $product['cost_price']; ?>">
                    <span class="help-inline">Currency AED</span>
                </div>
            </div>
            <div class="control-group">
                <label for="sell_price" class="control-label">Selling Price</label>
                <div class="controls">
                    <input type="text" id="sell_price" name="sell_price" value="<?php echo $product['sell_price']; ?>">
                    <span class="help-inline">Currency AED</span>
                </div>
            </div>
            <div class="control-group">
                <label for="active" class="control-label">Active</label>
                <div class="controls">
                    <?php echo form_dropdown('active', array('0'=>'No','1'=>'Yes'), $product['active']);?>
                </div>
            </div>

            <?php
            if(is_admin()):
            echo '<div class="control-group">';
            echo '<label for="vendor_id" class="control-label">Vendor</label>';
            echo '<div class="controls">';
            //echo form_dropdown('vendor_id', $options_vendor, '', 'class="span2"'];

            echo form_dropdown('vendor_id', $options_vendor, $product['vendor_id'], 'class="span2"');

            echo '</div>';
            echo '</div>';
            endif;
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
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste jbimages"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
            relative_urls: false
        });

    </script>