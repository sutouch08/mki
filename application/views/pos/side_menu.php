

<!--  ***********************************   Side menu Start ************************************** -->
	<ul class="nav nav-list">
		<li class="<?php echo active_menu(0,$id_menu); ?>"><a href="<?php echo valid_menu(1,"admin/main"); ?>"><i class="menu-icon fa fa-tachometer"></i><span class="menu-text"> Dashboard </span></a></li><!-- First Level Menu -->
        <li class="<?php echo active_menu(1,$id_menu); ?>"><a href="<?php echo valid_menu(1,"admin/product"); ?>"><i class="menu-icon fa fa-tags"></i><span class="menu-text"> เพิ่ม/แก้ไข รายการสินค้า </span></a></li>

        <li class="<?php echo active_menu(2,$id_menu); ?>"><a href="<?php echo valid_menu(1, "admin/employee"); ?>"><i class="menu-icon fa fa-users"></i><span class="menu-text"> เพิ่ม/แก้ไข พนักงาน </span></a></li>
        <li class="<?php echo active_menu(3,$id_menu); ?>"><a href="<?php echo valid_menu(1, "admin/user"); ?>"><i class="menu-icon fa fa-users"></i><span class="menu-text"> เพิ่ม/แก้ไข ชื่อผู้ใช้งาน </span></a></li>
        <li class="<?php echo active_menu(4,$id_menu); ?>"><a href="<?php echo valid_menu(1,"admin/promotion"); ?>"><i class="menu-icon fa fa-tags"></i><span class="menu-text"> เพิ่ม/แก้ไข โปรโมชั่น </span></a></li>
        <li class="<?php echo active_menu(5,$id_menu); ?>"><a href="<?php echo valid_menu(1,"admin/receive_product"); ?>"><i class="menu-icon fa fa-tags"></i><span class="menu-text"> รับสินค้า </span></a></li>

        <!-- **********************************  เก็บไว้เป็นตัวอย่าง ***********************************
		<li class=""><a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-file-o"></i>
        	<span class="menu-text"> Other Pages
            <!-- #section:basics/sidebar.layout.badge
            	<span class="badge badge-primary">5</span></span> <b class="arrow fa fa-angle-down"></b></a>	<b class="arrow"></b>
			<ul class="submenu">
				<li class=""><a href="#"><i class="menu-icon fa fa-caret-right"></i>FAQ	</a><b class="arrow"></b></li>
				<li class="active"><a href="#"><i class="menu-icon fa fa-caret-right"></i>Blank Page</a></li>
			</ul>
		</li>
        ****************************************************************************************** -->
	</ul><!-- /.nav-list -->
