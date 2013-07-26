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
    <?php
    //flash messages
    if ($this->session->flashdata('flash_message')) {
        if ($this->session->flashdata('flash_message') == 'not_deleted') {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> You can not delete this user.';
            echo '</div>';
        }
    }
    ?>
    <div class="row">
        <div class="span12 columns">
            <div class="well">

                <?php

                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform', 'method', 'method' => 'get');

                //save the columns names in a array that we will use as filter
                $options_vendors = array();
                foreach ($vendors as $array) {
                    foreach ($array as $key => $value) {
                        $options_vendors[$key] = $key;
                    }
                    break;
                }

                echo form_open('admin/users', $attributes);

                echo form_label('Search:', 'search_string');
                echo form_input('search_string', $search_string_selected);

                echo form_label('Order by:', 'order');
                echo form_dropdown('order', $options_vendors, $order, 'class="span2"');

                $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

                $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

                echo form_submit($data_submit);

                echo form_close();
                ?>

            </div>

            <table class="table table-striped table-bordered table-condensed" id="oTable">
                <thead>
                <tr>
                    <th class="header">#</th>
                    <th class="yellow header headerSortDown">Name</th>
                    <th class="header">Email</th>
                    <th class="header">Active</th>
                    <th class="header">Super User</th>
                    <th class="header">Created At</th>
                    <th class="header">Updated At</th>
                    <th class="header no-sort">Action</th>

                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($vendors as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['email'] . '</td>';
                    echo '<td>' . $row['is_admin'] . '</td>';
                    echo '<td>' . $row['active'] . '</td>';
                    echo '<td>' . $row['created_at'] . '</td>';
                    echo '<td>' . $row['updated_at'] . '</td>';
                    echo '<td class="crud-actions">
                  <a href="' . site_url("admin") . '/users/update/' . $row['id'] . '" class="btn btn-info">Edit</a>
                  <a href="' . site_url("admin") . '/users/delete/' . $row['id'] . '" class="btn btn-danger">Delete</a>
                </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

            <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
    </div>
    <script type="text/javascript">
        sort = [
            [0, 'asc'],
            [1, 'asc']
        ];
        $(document).ready(function () {
            $('#oTable').dataTable({
                "iDisplayLength": 100,
                "aLengthMenu": [[100, 200, 500, -1], [100, 200, 500, "All"]],
                "aoColumnDefs": [
                    {
                        "bSortable": false,
                        "aTargets": [ "no-sort" ]
                    }
                ],
                "sDom": "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "oTableTools": {
                    "sSwfPath": "<?php echo base_url()?>/assets/copy_csv_xls_pdf.swf",
                    "aButtons": [
                        "copy",
                        "print",
                        {
                            "sExtends": "collection",
                            "sButtonText": 'Save <span class="caret" />',
                            "aButtons": [ "csv", "xls", "pdf" ]
                        }
                    ]
                }

            });
        });
    </script>