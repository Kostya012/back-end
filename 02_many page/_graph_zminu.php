<?php
$cook = isset($_COOKIE["numuz"]);
$ip = $_SERVER["REMOTE_ADDR"];
$ipCheck = false;

if ($ip == "::1") {
  $ipCheck = true;
}
if (true !== $cook && true !== $ipCheck || $cook && true !== $ipCheck || true !== $cook && $ipCheck) {
  echo "Error 404 Сторінка не знайдена!";
  unset($cook);
  unset($ipCheck);
  unset($ip);
  exit();
}
unset($cook);
unset($ipCheck);
unset($ip);
$title = 'Графік роботи';
$article = 'Графік змінної роботи працівників відділу ХКВП';
require_once 'pages/head.php';
?>
<link rel="stylesheet" href="css/graph1.css" />
<link rel="stylesheet" href="css/loader.css" />
<?php
require_once 'pages/body-header.php';
?>
<main>
  <div class="container container-cody">
    <div class="list-group">
      <h4>
        <span class="label label-primary">
          <a href="_index.php">/Головна</a>
          <a href="_graph.php">/Графік роботи</a>
        </span>
      </h4>
    </div>
    <div class="search-table">
      <div id="searcharea">
        <input type="search" name="search" id="mySearch" placeholder="Пошук" />
      </div>
      <div class="linkDownload">
        <a href="doc/graphic.xls">
          <div class="linkDown">
            <img class="hicon-down" src="img/ms_excel.svg" />
            <span>Скачати графік роботи.xls</span>
          </div>
        </a>
      </div>
      <h2>ГРАФІК змінної роботи</h2>
      <div id="loader">
        <div class="loader">Loading...</div>
      </div>
      <h2 id="h2"></h2>
      <div id="info">
      </div>
      <?php
      function createJSON1($name)
      {
        $excel = PHPExcel_IOFactory::load($name);
        $mas = array(array());
        $countRows = 0;
        for ($i = 8; $i <= 150; $i++) {
          if (gettype($excel->getSheetByName('смена')->getCellByColumnAndRow(0, $i)->getValue()) === "NULL") break;
          $countRows++;
        }
        $countColumns = 40;
        $mas[0][0] = $excel->getSheetByName('смена')->getCellByColumnAndRow(3, 7)->getValue();
        for ($i = 1; $i <= $countRows; $i++) {
          for ($j = 0; $j <= $countColumns - 1; $j++) {
            $mas[$i][$j] = $excel->getSheetByName('смена')->getCellByColumnAndRow($j, $i + 7)->getCalculatedValue();
          }
        }
        $result = json_encode($mas, JSON_UNESCAPED_UNICODE);
        file_put_contents('json/graph1.json', $result);
        $excel->disconnectWorksheets();
        unset($excel);
      }
      function createJSON2($name)
      {
        $excel = PHPExcel_IOFactory::load($name);
        $mas = array(array());
        $countRows = 0;
        for ($i = 4; $i <= 150; $i++) {
          if (gettype($excel->getSheetByName('ежедневка')->getCellByColumnAndRow(0, $i)->getValue()) === "NULL") break;
          $countRows++;
        }
        $countColumns = 36;
        $mas[0][0] = $excel->getSheetByName('смена')->getCellByColumnAndRow(3, 7)->getValue();
        for ($i = 1; $i <= $countRows; $i++) {
          for ($j = 0; $j <= $countColumns - 1; $j++) {
            $mas[$i][$j] = $excel->getSheetByName('ежедневка')->getCellByColumnAndRow($j, $i + 3)->getCalculatedValue();
          }
        }
        $result = json_encode($mas, JSON_UNESCAPED_UNICODE);
        file_put_contents('json/graph2.json', $result);
        $excel->disconnectWorksheets();
        unset($excel);
      }
      $fileName = 'doc/graphic.xls';
      if (file_exists($fileName)) {
        $chCurrent = date("d.m.Y H:i:s.", filemtime($fileName));
        echo "<br>Останні зміни файла $fileName : " . $chCurrent;
        require_once 'Classes/PHPExcel.php';
        $fN_changes_xls = "statistics/fN_changes_graph_xls.txt";
        if (file_exists($fN_changes_xls)) {
          $fileLust = file_get_contents($fN_changes_xls);
          $fileCurrent = date("F d Y H:i:s.", filemtime($fileName));
          if ($fileLust === $fileCurrent) {
            unset($fileLust);
            unset($fileCurrent);
          } else {
            echo "<br>Файл змінено: " . $fileCurrent . ", а перед цим останні зміни були: " . $fileLust;
            $file = fopen($fN_changes_xls, "w+");
            fwrite($file, $fileCurrent);
            fclose($file);
            $file = file_get_contents($fN_changes_xls);
            unset($file);
            unset($fileLust);
            unset($fileCurrent);
            createJSON1($fileName);
            createJSON2($fileName);
          }
        } else {
          $file = fopen($fN_changes_xls, "a+");
          fwrite($file, date("F d Y H:i:s.", filemtime($fileName)));
          fclose($file);
          $file = file_get_contents($fN_changes_xls);
          unset($file);
          createJSON1($fileName);
          createJSON2($fileName);
        }
      } else {
        echo "<br>Нажаль файла: $fileName не має. Необхідно завантажити файл.";
      }
      ?>
    </div>
  </div>
  <?php
  require_once 'pages/aside.php';
  ?>
</main>
<?php
require_once 'pages/footer.php';
?>
<script>
  let graph = document.getElementById('graph');
  graph.classList.add('active');
</script>
<script src="js/graph1.js"></script>
</body>

</html>