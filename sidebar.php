  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
   	<a href="./" class="brand-link">
        <?php if($_SESSION['login_pozisyon'] == 1): ?>
        <h3 class="text-center p-0 m-0"><b>Yönetici</b></h3>
        <?php else: ?>
        <h3 class="text-center p-0 m-0"><b>Kullanıcı</b></h3>
        <?php endif; ?>

    </a>
      
    </div>
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="./" class="nav-link nav-anasayfa">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
              Gösterge Paneli
              </p>
            </a>
          </li>  
          <li class="nav-item">
            <a href="#" class="nav-link nav-projeyi_düzenle nav-projeyi_görüntüle">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Projeler
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <?php if($_SESSION['login_pozisyon'] != 3): ?>
              <li class="nav-item">
                <a href="./index.php?page=yeni_proje" class="nav-link nav-yeni_proje tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Yeni Proje Ekle</p>
                </a>
              </li>
            <?php endif; ?>
              <li class="nav-item">
                <a href="./index.php?page=proje_listesi" class="nav-link nav-proje_listesi tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Liste</p>
                </a>
              </li>
            </ul>
          </li> 
          <li class="nav-item">
                <a href="./index.php?page=görev_listesi" class="nav-link nav-görev_listesi">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>Görevler</p>
                </a>
          </li>
          <?php if($_SESSION['login_pozisyon'] != 3): ?>
           <li class="nav-item">
                <a href="./index.php?page=raporlar" class="nav-link nav-raporlar">
                  <i class="fas fa-th-list nav-icon"></i>
                  <p>Raporlar</p>
                </a>
          </li>
          <?php endif; ?>
          <?php if($_SESSION['login_pozisyon'] == 1): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-kullaniciyi_duzenle">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Kullanıcılar
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=yeni_kullanıcı" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Yeni Kullanıcı Ekle</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=kullanıcı_listesi" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Liste</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'anasayfa' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})
  </script>