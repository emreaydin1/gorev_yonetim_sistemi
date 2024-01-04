<?php include('db_connect.php') ?>
<?php

$twhere = "";
if (isset($_SESSION['login_pozisyon']) && $_SESSION['login_pozisyon'] != 1 )
    $twhere = "  ";

?>
<!-- Chart.js Kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Info kutuları -->
<div class="col-12">
    <div class="card">
        <div class="card-body">
            Hoşgeldin <?php echo $_SESSION['login_isim'] ?>!
        </div>
    </div>
</div>
<hr>
<?php
$where = "";
if ($_SESSION['login_pozisyon'] == 2 || $_SESSION['login_pozisyon'] == 3) {
    $where = " where yonetici_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_pozisyon'] == 0 ) {
    $where = " where concat('[',REPLACE(id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}
$where2 = "";
if ($_SESSION['login_pozisyon'] == 2 || $_SESSION['login_pozisyon'] == 3) {
    $where2 = " where p.yonetici_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_pozisyon'] == 0) {
    $where2 = " where concat('[',REPLACE(p.id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <b>Proje İlerlemesi</b>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0 table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="30%">
                            <col width="35%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <th>#</th>
                            <th>Projeler</th>
                            <th>İlerleme</th>
                            <th>Durum</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $stat = array("Askıda ","Başladı","Devam Ediyor","Beklemede","Gecikti","Tamamlandı");
                            $where = "";
                            if($_SESSION['login_pozisyon'] == 2 || $_SESSION['login_pozisyon'] == 0){
                              $where = " where yonetici_id = '{$_SESSION['login_id']}' ";
                            }elseif($_SESSION['login_pozisyon'] == 0 ){
                              $where = " where concat('[',REPLACE(id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                            }
                            $qry = $conn->query("SELECT * FROM projeler $where order by proje_adi asc");
                            if (!$qry) {
                              echo "Sorgu hatası: " . $conn->error;
                          }
                            while ($row = $qry->fetch_assoc()) :
                                $prog = 0;
                                $tprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']}")->num_rows;
                                $cprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']} and durum = 3")->num_rows;
                                $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
                                $prog = $prog > 0 ? number_format($prog, 2) : $prog;
                                $prod = $conn->query("SELECT * FROM calisan_etkinligi where proje_id = {$row['id']}")->num_rows;
                                if ($row['durum'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['baslangic_tarihi']) && $row['durum'] != 2):
                                    if ($prod > 0  || $cprog > 0)
                                        $row['durum'] = 2;
                                    else
                                        $row['durum'] = 1;
                                elseif ($row['durum'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['bitis_tarihi'])) :
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
                                            Due: <?php echo date("Y-m-d", strtotime($row['bitis_tarihi'])) ?>
                                        </small>
                                    </td>
                                    <td class="project_progress">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                                            </div>
                                        </div>
                                        <small>
                                            <?php echo $prog ?>% Tamamlandı
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
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="./index.php?page=projeyi_görüntüle&id=<?php echo $row['id'] ?>">
                                            <i class="fas fa-folder">
                                            </i>
                                            Görüntüle
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-12">
                <div class="small-box bg-light shadow-sm border">
                    <div class="inner">
                        <h3><?php echo $conn->query("SELECT * FROM projeler $where")->num_rows; ?></h3>
                        <p>Toplam Proje</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-12">
                <div class="small-box bg-light shadow-sm border">
                    <div class="inner">
                        <h3><?php echo $conn->query("SELECT t.*,p.proje_adi as pproje_adi,p.baslangic_tarihi,p.durum as pdurum, p.bitis_tarihi,p.id as pid FROM gorevler t inner join projeler p on p.id = t.proje_id")->num_rows; ?></h3>
                        <p>Toplam Görev</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$twhere = "";
if ($_SESSION['login_pozisyon'] != 1 )
    $twhere = "  ";

// Projelerin durumlarını saymak için dizi
$projeDurumSayilari = [
    'Askıda' => 0,
    'Başladı' => 0,
    'Devam Ediyor' => 0,
    'Beklemede' => 0,
    'Gecikti' => 0,
    'Tamamlandı' => 0,
];

// Veritabanından verileri alın ve sayın
$qry = $conn->query("SELECT durum FROM projeler $where");
while ($row = $qry->fetch_assoc()) :
    $projeDurum = isset($stat[$row['durum']]) ? $row['durum'] : null;

if ($projeDurum !== null) {
    if (!isset($projeDurumSayilari[$stat[$projeDurum]])) {
        $projeDurumSayilari[$stat[$projeDurum]] = 0;
    }
    $projeDurumSayilari[$stat[$projeDurum]]++;
} else {
    // "default" indeksini tanımlayın ve artırın
    if (!isset($projeDurumSayilari[$defaultKey])) {
        $projeDurumSayilari[$defaultKey] = 2;
    }
    $projeDurumSayilari[$defaultKey]++;
}
endwhile;

// Kullanıcı çalışma saatlerini çekmek için SQL sorgusu
$calismaSaatiSorgu = "SELECT
calisanlar.isim,
calisanlar.soyisim,
sum(calisan_etkinligi.bitis_tarihi-calisan_etkinligi.baslangic_tarihi)/3600 AS calisilan_saat
FROM calisanlar
INNER JOIN calisan_etkinligi ON calisanlar.id = calisan_etkinligi.calisan_id
GROUP BY calisanlar.id";
$calismaSaatiSonuc = $conn->query($calismaSaatiSorgu);
if (!$calismaSaatiSonuc) {
    echo "Sorgu hatası " . $conn->error;
} else {
    // Sorgu başarılıysa devam et
}
$calismaSaati = [];
 
while ($row = $calismaSaatiSonuc->fetch_assoc()) {
        $calismaSaati[] = ['kullanici' => $row['isim'] . ' ' . $row['soyisim'], 'calisilan_saat' => $row['calisilan_saat']];
}


$enUzunCalismaSuresiSorgu = " SELECT gorev, MAX(abs(gecirilen_sure)) AS en_uzun_calisma_suresi
FROM (
    SELECT gorev, SUM(ROUND(TIMESTAMPDIFF(SECOND, a.baslangic_tarihi, a.bitis_tarihi) / 3600.0, 1)) AS gecirilen_sure
    FROM calisan_etkinligi a
    INNER JOIN gorevler b ON a.gorev_id = b.id
    GROUP BY gorev
) AS temp
GROUP BY gorev";
$enUzunCalismaSuresiSonuc = $conn->query($enUzunCalismaSuresiSorgu);
if (!$enUzunCalismaSuresiSonuc) {
    echo "Sorgu hatası: " . $conn->error;
} else {
    // Sorgu başarılıysa devam et
}
$enUzunCalismaSuresi = [];
while ($row = $enUzunCalismaSuresiSonuc->fetch_assoc()) {
    $enUzunCalismaSuresi[] = ['gorev' => $row['gorev'], 'en_uzun_calisma_suresi' => $row['en_uzun_calisma_suresi']];
}

$toplamGorevSayisi =  "SELECT 
proje_adi,
COUNT(*) AS toplam_gorev_sayisi,
SUM(CASE WHEN b.durum = 3 THEN 1 ELSE 0 END) AS tamamlanan_gorev_sayisi,
SUM(CASE WHEN b.durum != 3 THEN 1 ELSE 0 END) AS tamamlanmamis_gorev_sayisi
FROM 
projeler a
LEFT JOIN 
gorevler b ON a.id = b.proje_id
GROUP BY 
proje_adi";
$toplamGorevSonuc = $conn->query($toplamGorevSayisi);
if (!$toplamGorevSonuc) {
    echo "Sorgu hatası: " . $conn->error;
} else {
    // Sorgu başarılıysa devam et
}
$toplamGorev = [];
while ($row = $toplamGorevSonuc->fetch_assoc()) {
    $toplamGorev[] = ['proje_adi' => $row['proje_adi'], 'toplam_gorev_sayisi' => $row['toplam_gorev_sayisi'], 'tamamlanan_gorev_sayisi' => $row['tamamlanan_gorev_sayisi'], 'tamamlanmamis_gorev_sayisi' => $row['tamamlanmamis_gorev_sayisi']];
}

$katilimSayisi="SELECT isim, COUNT(DISTINCT calisan_etkinligi.id) AS katildigi_gorev_sayisi FROM  calisanlar LEFT JOIN calisan_etkinligi ON FIND_IN_SET(calisanlar.id, calisan_etkinligi.calisan_id) > 0 GROUP BY isim ";
$katilimSonuc = $conn->query($katilimSayisi);
if (!$katilimSonuc) {
    echo "Sorgu hatası: " . $conn->error;
} else {
    // Sorgu başarılıysa devam et
}
$katilim = [];
while ($row = $katilimSonuc->fetch_assoc()) {
    $katilim[] = ['isim' => $row['isim'], 'katildigi_gorev_sayisi' => $row['katildigi_gorev_sayisi']];
}


// JavaScript için veri hazırlığı
echo "<script>
var projeData = " . json_encode(array_values($projeDurumSayilari)) . ";
var projeLabels = " . json_encode(array_keys($projeDurumSayilari)) . ";
var calismaSaatiData = " . json_encode(array_column($calismaSaati, 'calisilan_saat')) . ";
var calismaSaatiLabels = " . json_encode(array_column($calismaSaati, 'kullanici')) . ";
var enUzunCalismaSaatiData = " . json_encode(array_column($enUzunCalismaSuresi, 'en_uzun_calisma_suresi')) . ";
var enUzunCalismaSaatiLabels = " . json_encode(array_column($enUzunCalismaSuresi, 'gorev')) . ";
var toplamGorevData = " . json_encode(array_column($toplamGorev, 'toplam_gorev_sayisi')) . ";
var tamamlananGorevData = " . json_encode(array_column($toplamGorev, 'tamamlanan_gorev_sayisi')) . ";
var tamamlanmamisGorevData = " . json_encode(array_column($toplamGorev, 'tamamlanmamis_gorev_sayisi')) . ";
var ProjeAdi = " . json_encode(array_column($toplamGorev, 'proje_adi')) . ";
var ortalamaCalismaSaatiData = " . json_encode(array_fill(0, count($calismaSaati), array_sum(array_column($calismaSaati, 'calisilan_saat')) / count($calismaSaati))) . ";
var katilimData = " . json_encode(array_column($katilim, 'katildigi_gorev_sayisi')) . ";
var katilimLabels = " . json_encode(array_column($katilim, 'isim')) . ";

</script>";











?>
<?php
if (isset($_SESSION['login_pozisyon']) && ($_SESSION['login_pozisyon'] == 1 || $_SESSION['login_pozisyon'] == 2)) {
    $twhere = "  ";
    $twhere2 = "  ";
} elseif (isset($_SESSION['login_pozisyon']) && $_SESSION['login_pozisyon'] == 3) {
    $twhere = " where concat('[',REPLACE(id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    $twhere2 = " where concat('[',REPLACE(p.id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
} else {
    $twhere = "  ";
    $twhere2 = "  ";
}
?>
<?php if ($_SESSION['login_pozisyon'] == 1 || $_SESSION['login_pozisyon'] == 2) { ?>
<div class="row">
    <!-- Mevcut içeriğiniz -->

    <!-- Projelerin Durumları için Chart Container -->
    <div class="col-md-4">
        <canvas id="projeDurumGrafigi" width="600" height="600"></canvas>
    </div>

    <!-- Kullanıcı Çalışma Saatleri için Chart Container -->
    <div class="col-md-4">
        <canvas id="calismaSaatiGrafigi" width="600" height="580"></canvas>
    </div>
    <!-- En uzun çalışma saatleri için Chart Container -->
   
    <!-- Toplam Görev Sayısı için Chart Container -->
    <div class="col-md-4">
        <canvas id="toplamGorevSayisi" width="600" height="600"></canvas>
    </div>
    <!-- Katılım Sayısı için Chart Container -->
    <div class="col-md-4">
        <canvas id="katilimSayisi" width="600" height="600"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="enUzunCalismaSaatleri" width="1000" height="900"></canvas>
    </div>
</div>
<?php } ?>
<script>
// Projelerin Durumları için Grafik
var ctxProjeDurum = document.getElementById('projeDurumGrafigi').getContext('2d');
var projeDurumGrafigi = new Chart(ctxProjeDurum, {
    type: 'bar',
    data: {
        labels: projeLabels,
        datasets: [{
            label: 'Projelerin Durumları',
            data: projeData,
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Projelerin Durumları Grafiği'
            }
        },scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Kullanıcı Çalışma Saatleri için Grafik 
var ctxCalismaSaati = document.getElementById('calismaSaatiGrafigi').getContext('2d');
var calismaSaatiGrafigi = new Chart(ctxCalismaSaati, {
    type: 'bar',
    data: {
        labels: calismaSaatiLabels,
        datasets: [{
            label: 'Çalışılan Saatler',
            data: calismaSaatiData,
             backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)',
                'rgba(255, 159, 64, 0.5)'
            ],
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }, {
            type: 'line',
            label: 'Ortalama Çalışma Saati',
            data: ortalamaCalismaSaatiData,
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Çalışma Saatleri Grafiği'
            }
        },scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


var ctxEnUzunCalismaSaati = document.getElementById('enUzunCalismaSaatleri').getContext('2d');
var enUzunCalismaSaatiGrafigi = new Chart(ctxEnUzunCalismaSaati, {
    type: 'bar',
    data: {
        labels: enUzunCalismaSaatiLabels,
        datasets: [{
            label: 'En Uzun Çalışılan Görevler',
            data: enUzunCalismaSaatiData,
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'En Uzun Çalışılan Görevler Grafiği'
            }
        },scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
var ctxToplamGorevSayisi = document.getElementById('toplamGorevSayisi').getContext('2d');
var toplamGorevSayisiGrafigi = new Chart(ctxToplamGorevSayisi, {
    type: 'bar',
    data: {
        labels: ProjeAdi,
        datasets: [{
            label: 'Tamamlanmış Görev Sayısı',
            data: tamamlananGorevData,
            backgroundColor: 'rgba(54, 162, 235, 1)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Tamamlanmamış Görev Sayısı',
            data: tamamlanmamisGorevData,
            backgroundColor: 'rgba(255, 99, 132, 1)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Görevlerin Tamamlanma Durumu Grafiği'
            }
        },scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

var ctxKatilimSayisi = document.getElementById('katilimSayisi').getContext('2d');
var katilimSayisiGrafigi = new Chart(ctxKatilimSayisi, {
    type: 'pie',
    data: {
        labels: katilimLabels,
        datasets: [{
            label: 'Katılım Sayısı',
            data: katilimData,
            backgroundColor:
            [ 'rgba(47, 79, 79, 1)',
            'rgba(139, 69, 19, 1)',
            'rgba(0, 0, 255, 1)',
            'rgba(84, 255, 159, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Görevlere Katılım Sayısı Grafiği'
            }
        },scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});













</script>
