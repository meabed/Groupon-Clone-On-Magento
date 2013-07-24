<div class="container top">

    <ul class="breadcrumb">
        <li>
            <a href="<?php echo site_url("admin"); ?>">
                <?php echo ucfirst($this->uri->segment(1)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li class="active">
            <?php echo ucfirst($this->uri->segment(2)); ?>
        </li>
    </ul>

    <div class="page-header users-header">
        <h2>
            <?php echo ucfirst($this->uri->segment(2)); ?>
            <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>/add" class="btn btn-success">Add a new</a>
        </h2>
    </div>

    <div class="row">
        <div class="span12 columns">
            <div class="well">

                <?php

                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform','method'=>'get');

                $options_vendor = array(0 => "all");
                foreach ($vendors as $row) {
                    $options_vendor[$row['id']] = $row['name'];
                }
                //save the columns names in a array that we will use as filter
                $options_products = array();
                foreach ($products as $array) {
                    foreach ($array as $key => $value) {
                        $options_products[$key] = $key;
                    }
                    break;
                }

                echo form_open('admin/products', $attributes);

                echo form_label('Search:', 'search_string');
                echo form_input(
                    'search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"'
                );
                if (is_admin()) {
                    echo form_label('Filter by Vendor:', 'member_id');
                    echo form_dropdown('member_id', $options_vendor, $vendor_selected, 'class="span2"');
                }
                echo form_label('Order by:', 'order');
                echo form_dropdown('order', $options_products, $order, 'class="span2"');

                $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

                $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

                echo form_submit($data_submit);

                echo form_close();
                ?>

            </div>

            <table class="table table-striped table-bordered table-condensed">
                <thead>
                <tr>
                    <th class="header">#</th>
                    <th class="yellow header headerSortDown">Name</th>
                    <th class="green header">Stock</th>
                    <th class="red header">Cost Price</th>
                    <th class="red header">Sell Price</th>
                    <th class="red header">Start Date</th>
                    <th class="red header">End Date</th>
                    <th class="red header">Active</th>
                    <?php if (is_admin()): ?>
                        <th class="red header">vendor</th>
                    <?php endif; ?>
                    <th class="red header">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($products as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['stock'] . '</td>';
                    echo '<td>' . $row['cost_price'] . '</td>';
                    echo '<td>' . $row['sell_price'] . '</td>';
                    echo '<td>' . $row['start_date'] . '</td>';
                    echo '<td>' . $row['end_date'] . '</td>';
                    echo '<td>' . $row['active'] . '</td>';
                    if (is_admin()):
                    echo '<td>' . $row['vendor_name'] . '</td>';
                     endif;
                    echo '<td class="crud-actions">
                  <a href="' . site_url("admin") . '/products/update/' . $row['id'] . '" class="btn btn-info">view & edit</a>
                  <a href="' . site_url("admin") . '/products/delete/' . $row['id'] . '" class="btn btn-danger">delete</a>
                </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

            <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
    </div>