<?php 

require_once('../model/operationTimeChart.php');

$dataValue = array();
$dataValueTemp = array();

$operationTime = array();
$operationTimeTemp = array();

$selectedMonth;
$selectedMonthString;
foreach ($LOAD_INLOAD as $scheduleChart) {
  
  $selectedMonthString = date('m', strtotime($scheduleChart->getHoraChegada()));
  $selectedMonth = intval(date('m', strtotime($scheduleChart->getHoraChegada())));
  $selectedYear = intval(date('y', strtotime($scheduleChart->getHoraChegada())));

  $currentDay = intval(date('d', strtotime($scheduleChart->getHoraChegada()))-1);

  //operações
  if(!in_array($scheduleChart->getOperationSourceName(), $dataValueTemp)){

    array_push($dataValueTemp, $scheduleChart->getOperationSourceName());

    $dataValue[$scheduleChart->getOperationSourceName()] = array();
    $dataValue[$scheduleChart->getOperationSourceName()][$currentDay] = 1;
    
  }else {
    $quantity = $dataValue[$scheduleChart->getOperationSourceName()][$currentDay];
    $dataValue[$scheduleChart->getOperationSourceName()][$currentDay] = $quantity + 1;
  }

   //tempos de operação
  $start = new DateTime($scheduleChart->getInicioOperacao());
  $end = new DateTime($scheduleChart->getFimOperacao());
  
  $days;
  $hours;
  $minutes;
  $interval;

  if($start >= $end){
    $minutes = 0;
  }else{
    $interval = $end->diff($start);
    $days = $interval->format('%d');
    $hours = 24 * $days + $interval->format('%h');
    $minutes = $interval->format('%i') + ($hours / 60);
  }
  

  $operationTimeChart;
  
  if($minutes > 0){
      
    if(!in_array($scheduleChart->getOperationSourceName(), $operationTimeTemp)){
    
      array_push($operationTimeTemp, $scheduleChart->getOperationSourceName());
    
      $operationTimeChart = new OperationTimeChart();
      $operationTimeChart->setQuantity(1);
      $operationTimeChart->setTotalMinutes($minutes);

      $operationTime[$scheduleChart->getOperationSourceName()] = array();
      $operationTime[$scheduleChart->getOperationSourceName()][$currentDay] = $operationTimeChart;
    
    }else {
      $operationTimeChart = $operationTime[$scheduleChart->getOperationSourceName()][$currentDay];

      if($operationTimeChart == null){
        $operationTimeChart = new OperationTimeChart();
        $operationTimeChart->setQuantity(1);
        $operationTimeChart->setTotalMinutes($minutes);

      }else{
        $operationTimeChart->setQuantity($operationTimeChart->getQuantity() + 1);
        $operationTimeChart->setTotalMinutes($operationTimeChart->getTotalMinutes() +  $minutes);
      }
    }
 
    $operationTime[$scheduleChart->getOperationSourceName()][$currentDay] = $operationTimeChart;
  }
}

$currentMonthString = $GLOBAL_MONTHS[$selectedMonthString];
$totalOfDays = cal_days_in_month(CAL_GREGORIAN,$selectedMonth,$selectedYear);

$dataValueSorted;
$operationTimeSorted;

$labelsBar = array();

$labelLoaded = false;

foreach ($dataValueTemp as $operation) {

  for($x = 0; $x < $totalOfDays; $x++){

    if(!$labelLoaded) array_push($labelsBar, strval($x + 1));
  
    if($dataValue[$operation][$x] == null){
      $dataValue[$operation][$x] = 0;
    }

    $dataValueSorted[$operation][$x] = $dataValue[$operation][$x];
  
    // média de tempos
    $operationTimeChart;

    if($operationTime[$operation][$x] == null){
      $operationTime[$operation][$x] = 0;
    
    }else{
      $operationTimeChart = $operationTime[$operation][$x];
      
      if($operationTimeChart->getTotalMinutes() > 0){
        $averageMinutes = floor($operationTimeChart->getTotalMinutes() / $operationTimeChart->getQuantity());

        $operationTimeChart->setAverage($averageMinutes);
        $operationTime[$operation][$x] = $operationTimeChart->getAverage();    
      }
      else{
        $operationTimeChart->setAverage(0);
      }
    }

    $operationTimeSorted[$operation][$x] = $operationTime[$operation][$x];
  }

  if(!empty($labelsBar)) $labelLoaded  = true;
} 

$labelsBarJson = json_encode($labelsBar);

echo 
'<body onload="loadChart()">
  <div class="chart-box" id="operationChart"></div>
  <div class="chart-box" id="operationLoad"></div>
  <div class="chart-box" id="operationOnLoad"></div>
  <div class="chart-box" id="operationTransfer"></div>
  <div class="chart-box" id="timeOperation"></div>
  <div class="chart-box" id="timeLoadOperation"></div>
  <div class="chart-box" id="timeInLoadOperation"></div>
  <div class="chart-box" id="timeTransferOperation"></div>
</body>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>

function loadChart(){

  const chartConfig = {
    type: "bar",
    height: 450,
    stacked: true,
    toolbar: {
      show: true
    },
    zoom: {
      enabled: true
    }
  };

  const responsiveConfig = [{
    breakpoint: 480,
    options: {
      legend: {
        position: "bottom",
        offsetX: -10,
        offsetY: 0
      }
    }
  }];

  const plotConfig = {
    bar: {
      horizontal: false,
      borderRadius: 10,
      dataLabels: {
        total: {
          enabled: true,
          style: {
            fontSize: "13px",
            fontWeight: 900
          }
        }
      }
    },
  };

  const plotConfigOneColumn = {
    bar: {
      horizontal: false,
      borderRadius: 10,
      dataLabels: {
        total: {
          enabled: false,
          style: {
            fontSize: "13px",
            fontWeight: 900
          }
        }
      }
    },
  };

  const legendConfig = {
    position: "right",
    offsetY: 40
  };

  const fillConfig = {
    opacity: 1
  }

  const dataValue = '.json_encode($dataValueSorted).';
  const operationTime = '.json_encode($operationTimeSorted).';

  let series = [];
  let loads = [];
  let onLoads = [];
  let transfers = [];
  let timeSeries = [];
  let timeLoads = [];
  let timeInLoads = [];
  let timeTransfers = [];

  for (const data of Object.keys(dataValue)) {
      serie = {};

      const element = data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();

      serie["name"] = element;
      serie["data"] = dataValue[data];

      if(element == "CARGA") loads.push(serie);
      if(element == "DESCARGA") onLoads.push(serie);
      if(element == "TRANSFERENCIA") transfers.push(serie);
  }

  if(loads.length > 0) series.push(loads[0]);
  if(onLoads.length > 0) series.push(onLoads[0]);
  if(transfers.length > 0) series.push(transfers[0]);

  // loop de tempo médio de operações 
  for (const data of Object.keys(operationTime)) {
    serie = {};

    const element = data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();

    serie["name"] = element;
    serie["data"] = operationTime[data];

    if(element == "CARGA") {
      timeLoads.push(serie);
    }

    if(element == "DESCARGA"){
      timeInLoads.push(serie);
    } 

    if(element == "TRANSFERENCIA") {
      timeTransfers.push(serie);
    }
  }

  if(timeLoads.length > 0) timeSeries.push(timeLoads[0]);
  if(timeInLoads.length > 0) timeSeries.push(timeInLoads[0]);
  if(timeTransfers.length > 0) timeSeries.push(timeTransfers[0]);

  //operações combinadas
  const operationOptions = {
    series: series,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfig,
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
      label : "dias"
    },
    title: {
      text: "Número Total de Caminhões Carregados, Descarregados e Transferidos - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //número de cargas
  const operationLoadOptions = {
    series: loads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfig,
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    title: {
      text: "Número Total de Caminhões de Carregados - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //número de descargas
  const operationOnLoadOptions = {
    series: onLoads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfig,
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    title: {
      text: "Número Total de Caminhões de Descarregados - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //número de transferencias
  const operationTransferOptions = {
    series: transfers,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfig,
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    title: {
      text: "Número Total de Transferências - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };

  // tempo médio de carregamento
  const loadTimeOptions = {
    series: timeLoads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        if(val < 60){
          if(val < 10) return `00:0${val}`;
          else return `00:${val}`; 
        }else{
          const hour = (val >= 60) ? floor(val/60) : 0;
          const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
          return `0${hour}:${resultMinutes}`;
        }
      }
    },
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    yaxis: {
      title: {
        text: "Minutos"
      }
    },
    tooltip: {
      y: {
        formatter: function (val) {
          if(val < 60){
            if(val < 10) return `00:0${val}`;
            else return `00:${val}`; 
          }else{
            const hour = (val >= 60) ? floor(val/60) : 0;
            const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
            return `0${hour}:${resultMinutes}`;
          }
        }
      }
    },
    title: {
      text: "Tempo médio de CARREGAMENTO - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };

  // tempo médio de descarga
  const inLoadsTimeOptions = {
    series: timeInLoads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        if(val < 60){
          if(val < 10) return `00:0${val}`;
          else return `00:${val}`; 
        }else{
          const hour = (val >= 60) ? floor(val/60) : 0;
          const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
          return `0${hour}:${resultMinutes}`;
        }
      }
    },
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    yaxis: {
      title: {
        text: "Minutos"
      }
    },
    tooltip: {
      y: {
        formatter: function (val) {
          if(val < 60){
            if(val < 10) return `00:0${val}`;
            else return `00:${val}`; 
          }else{
            const hour = (val >= 60) ? floor(val/60) : 0;
            const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
            return `0${hour}:${resultMinutes}`;
          }
        }
      }
    },
    title: {
      text: "Tempo médio de DESCARGAS - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };

  // tempo médio de transferência
  const transferTimeOptions = {
    series: timeTransfers,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        if(val < 60){
          if(val < 10) return `00:0${val}`;
          else return `00:${val}`; 
        }else{
          const hour = (val >= 60) ? floor(val/60) : 0;
          const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
          return `0${hour}:${resultMinutes}`;
        }
      }
    },
    xaxis: {
      type: "string",
      categories: '.$labelsBarJson.',
    },
    yaxis: {
      title: {
        text: "Minutos"
      }
    },
    tooltip: {
      y: {
        formatter: function (val) {
          if(val < 60){
            if(val < 10) return `00:0${val}`;
            else return `00:${val}`; 
          }else{
            const hour = (val >= 60) ? floor(val/60) : 0;
            const resultMinutes = ((val - 60) < 10) ? `0${(val - 60)}`: `${(val - 60)}`;
            return `0${hour}:${resultMinutes}`;
          }
        }
      }
    },
    title: {
      text: "Tempo médio de TRANSFERÊNCIAS - '.$currentMonthString.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };

  const operationChartBox = new ApexCharts(document.querySelector("#operationChart"), operationOptions);
  operationChartBox.render();

  const operationLoadChartBox = new ApexCharts(document.querySelector("#operationLoad"), operationLoadOptions);
  operationLoadChartBox.render();

  const operationOnLoadChartBox = new ApexCharts(document.querySelector("#operationOnLoad"), operationOnLoadOptions);
  operationOnLoadChartBox.render();

  const operationTransferChartBox = new ApexCharts(document.querySelector("#operationTransfer"), operationTransferOptions);
  operationTransferChartBox.render();

  const timeLoadChartBox = new ApexCharts(document.querySelector("#timeLoadOperation"), loadTimeOptions);
  timeLoadChartBox.render();

  const timeInLoadChartBox = new ApexCharts(document.querySelector("#timeInLoadOperation"), inLoadsTimeOptions);
  timeInLoadChartBox.render();

  const timeTransferChartBox = new ApexCharts(document.querySelector("#timeTransferOperation"), transferTimeOptions);
  timeTransferChartBox.render();
}
</script>';
?>
