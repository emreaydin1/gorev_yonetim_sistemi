<?php include 'db_connect.php' ?>
 <div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header">
            <b>Proje İlerlemesi</b>
            <div class="card-tools">
            	<button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print"><i class="fa fa-print"></i> Yazdır</button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive" id="printable">
              <table class="table m-0 table-bordered">
               <!--  <colgroup>
                  <col width="5%">
                  <col width="30%">
                  <col width="35%">
                  <col width="15%">
                  <col width="15%">
                </colgroup> -->
                <thead>
                  <th>#</th>
                  <th>Proje</th>
                  <th>Görev</th>
                  <th>Tamamlanan Görev</th>
                  <th>Çalışma Süresi</th>
                  <th>İlerleme</th>
                  <th>Durum</th>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $stat = array("Askıda","Başladı","Devam Ediyor","Beklemede","Gecikti","Tamamlandı");
                $where = "";
                if($_SESSION['login_pozisyon'] == 2){
                  $where = " where yonetici_id = '{$_SESSION['login_id']}' ";
                }elseif($_SESSION['login_pozisyon'] == 3){
                  $where = " where concat('[',REPLACE(calisan_id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                }
                $qry = $conn->query("SELECT * FROM projeler $where order by proje_adi asc");
                while($row= $qry->fetch_assoc()):
                $tprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']}")->num_rows;
                $cprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']} and durum = 3")->num_rows;
                $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
                $prod = $conn->query("SELECT * FROM calisan_etkinligi where proje_id = {$row['id']}")->num_rows;
                $dur = $conn->query("SELECT sum(calisan_etkinligi.bitis_tarihi-calisan_etkinligi.baslangic_tarihi)/3600 AS geçirilen_süre FROM calisan_etkinligi where proje_id = {$row['id']}");
                $dur = $dur->num_rows > 0 ? $dur->fetch_assoc()['geçirilen_süre'] : 0; 
                if($row['durum'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['baslangic_tarihi'])):
                if($prod  > 0  || $cprog > 0)
                  $row['durum'] = 2;
                else
                  $row['durum'] = 1;
                elseif($row['durum'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['bitis_tarihi'])):
                $row['durum'] = 4;
                endif;
                  ?>
                  <tr>
                      <td>
                         <?php echo $i++ ?>
                      </td>
                      <td>
                          <a>
                              <?php echo ucwords($row['proje_adi']) ?>
                          </a>
                          <br>
                          <small>
                              Due: <?php echo date("Y-m-d",strtotime($row['bitis_tarihi'])) ?>
                          </small>
                      </td>
                      <td class="text-center">
                      	<?php echo number_format($tprog) ?>
                      </td>
                      <td class="text-center">
                      	<?php echo number_format($cprog) ?>
                      </td>
                      <td class="text-center">
                            <?php echo number_format($dur,1).' Saat' ?>
                      </td>

                      <td class="project_progress">
                          <div class="progress progress-sm">
                              <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                              </div>
                          </div>
                          <small>
                              <?php echo $prog ?>% Complete
                          </small>
                      </td>
                      <td class="project-state">
                          <?php
                            if($stat[$row['durum']] =='Askıda'){
                              echo "<span class='badge badge-secondary'>{$stat[$row['durum']]}</span>";
                            }elseif($stat[$row['durum']] =='Başladı'){
                              echo "<span class='badge badge-primary'>{$stat[$row['durum']]}</span>";
                            }elseif($stat[$row['durum']] =='Devam Ediyor'){
                              echo "<span class='badge badge-info'>{$stat[$row['durum']]}</span>";
                            }elseif($stat[$row['durum']] =='Beklemede'){
                              echo "<span class='badge badge-warning'>{$stat[$row['durum']]}</span>";
                            }elseif($stat[$row['durum']] =='Gecikti'){
                              echo "<span class='badge badge-danger'>{$stat[$row['durum']]}</span>";
                            }elseif($stat[$row['durum']] =='Tamamlandı'){
                              echo "<span class='badge badge-success'>{$stat[$row['durum']]}</span>";
                            }
                          ?>
                      </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>  
              </table>
            </div>
          </div>
        </div>
        </div>
<script>
	$('#print').click(function(){
		start_load()
		var _h = $('head').clone()
		var _p = $('#printable').clone()
		var _d = "<p class='text-center'><b>Proje İlerleme Raporu: (<?php echo date("F d, Y") ?>)</b></p>"
		_p.prepend(_d)
		_p.prepend(_h)
		var nw = window.open("","","width=900,height=600")
		nw.document.write(_p.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
			nw.close()
			end_load()
		},750)
	})
</script>