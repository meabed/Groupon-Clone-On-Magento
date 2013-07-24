<!DOCTYPE html>
<html lang="en-US">
<head>
    <title><?php echo ucwords($this->uri->segment(2))?> | Vendor Portal</title>
    <meta charset="utf-8">
    <link href="<?php echo base_url(); ?>assets/css/admin/global.css" rel="stylesheet" type="text/css">
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/admin.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/tinymce/tinymce.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/tinymce/jquery.tinymce.min.js"></script>
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" style="color: #ffffff">Vendor Portal</a>
            <ul class="nav">
                <li <?php if ($this->uri->segment(2) == 'products') {
                    echo 'class="active"';
                } ?>>
                    <?php echo anchor('admin/products', 'Products') ?>
                </li>
                <li <?php if ($this->uri->segment(2) == 'vouchers') {
                    echo 'class="active"';
                } ?>>
                    <?php echo anchor('admin/vouchers', 'Vouchers') ?>
                </li>
                <li <?php if ($this->uri->segment(2) == 'reports') {
                    echo 'class="active"';
                } ?>>
                    <?php echo anchor('admin/reports', 'Reports') ?>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <?php echo anchor('admin/profile', 'Profile') ?>
                            <?php if(is_admin()){ echo anchor('admin/users', 'Users');}?>
                            <?php echo anchor('admin/logout', 'Logout') ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
