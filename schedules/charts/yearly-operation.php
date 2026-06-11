<?php 

require_once('../model/operationTimeChart.php');

$dataValue = array();
$dataValueTemp = array();

$operationTime = array();
$operationTimeTemp = array();

foreach ($LOAD_INLOAD as $scheduleChart) {
  
  $month = intval(date('m', strtotime($scheduleChart->getHoraChegada()))) - 1;

  //operações
  if(!in_array($scheduleChart->getOperationSourceName(), $dataValueTemp)){

    array_push($dataValueTemp, $scheduleChart->getOperationSourceName());

    $dataValue[$scheduleChart->getOperationSourceName()] = array();
    $dataValue[$scheduleChart->getOperationSourceName()][$month] = 1;
    
  }else {
    $quantity = $dataValue[$scheduleChart->getOperationSourceName()][$month];
    $dataValue[$scheduleChart->getOperationSourceName()][$month] = $quantity + 1;
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
      $operationTime[$scheduleChart->getOperationSourceName()][$month] = $operationTimeChart;
    
    }else {
      $operationTimeChart = $operationTime[$scheduleChart->getOperationSourceName()][$month];

      if($operationTimeChart == null){
        $operationTimeChart = new OperationTimeChart();
        $operationTimeChart->setQuantity(1);
        $operationTimeChart->setTotalMinutes($minutes);

      }else{
        $operationTimeChart->setQuantity($operationTimeChart->getQuantity() + 1);
        $operationTimeChart->setTotalMinutes($operationTimeChart->getTotalMinutes() +  $minutes);
      }
    }
 
    $operationTime[$scheduleChart->getOperationSourceName()][$month] = $operationTimeChart;
  }
}

$dataValueSorted;
$operationTimeSorted;

foreach ($dataValueTemp as $operation) {
  
  for($x = 0; $x < 12; $x++){
    
    //operações
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
}


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

  const monthsCategories = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto","Setembro", "Outubro", "Novembro", "Dezembro"]

  const legendConfig = {
    position: "right",
    offsetY: 40
  };

  const fillConfig = {
    opacity: 1
  }

  const dataValue = '.json_encode($dataValueSorted).';
  const operationTime = '.json_encode($operationTimeSorted).';

  console.log(operationTime);

  let series = [];
  let loads = [];
  let onLoads = [];
  let transfers = [];

  let timeSeries = [];
  let timeLoads = [];
  let timeInLoads = [];
  let timeTransfers = [];

  // loop operations
  for (const data of Object.keys(dataValue)) {
      serie = {};

      const element = data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();

      serie["name"] = element;
      serie["data"] = dataValue[data];

      if(element == "CARGA") {
        loads.push(serie);
      }

      if(element == "DESCARGA"){
        onLoads.push(serie);
      } 

      if(element == "TRANSFERENCIA") {
        transfers.push(serie);
      }
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
      categories: monthsCategories,
    },
    title: {
      text: "Número Total de Caminhões Carregados, Descarregados e Transferidos - '.$selectedYear.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //número de cargas
  const operationLoadOptions = {
    series: loads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    xaxis: {
      type: "string",
      categories: monthsCategories,
    },
    title: {
      text: "Número Total de Caminhões de CARREGADOS - '.$selectedYear.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //numero de descargas
  const operationOnLoadOptions = {
    series: onLoads,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    xaxis: {
      type: "string",
      categories: monthsCategories,
    },
    title: {
      text: "Número Total de Caminhões de DESCARREGADOS - '.$selectedYear.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  // numero de transferencias
  const operationTransferOptions = {
    series: transfers,
    chart: chartConfig,
    responsive: responsiveConfig,
    plotOptions: plotConfigOneColumn,
    xaxis: {
      type: "string",
      categories:monthsCategories,
    },
    title: {
      text: "Número Total de TRANSFERÊNCIAS - '.$selectedYear.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };


  //tempo médio de operação
  const operationTimeOptions = {
    series: timeSeries,
    chart: {
    type: "bar",
    height: 350
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "28px",
      endingShape: "rounded"
    },
  },
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
    },
    offsetY: 0,
    style: {
      fontSize: "10px",
      colors: ["#fff"]
    }
  },
  stroke: {
    show: true,
    width: 2,
    colors: ["transparent"]
  },
  xaxis: {
    categories: monthsCategories,
  },
  yaxis: {
    title: {
      text: "Minutos"
    }
  },
  title: {
    text: "Tempo médio de operação - '.$selectedYear.'",
  },
  fill: {
    opacity: 1
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
  }
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
      categories: monthsCategories,
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
      text: "Tempo médio de CARREGAMENTO - '.$selectedYear.'",
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
      categories: monthsCategories,
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
      text: "Tempo médio de DESCARGAS - '.$selectedYear.'",
    },
    legend: legendConfig,
    fill: fillConfig
  };

  // tempo médio de descarga
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
      categories: monthsCategories,
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
      text: "Tempo médio de TRANSFERÊNCIAS - '.$selectedYear.'",
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

  const timeOperationsChartBox = new ApexCharts(document.querySelector("#timeOperation"), operationTimeOptions);
  timeOperationsChartBox.render();

  const timeLoadChartBox = new ApexCharts(document.querySelector("#timeLoadOperation"), loadTimeOptions);
  timeLoadChartBox.render();

  const timeInLoadChartBox = new ApexCharts(document.querySelector("#timeInLoadOperation"), inLoadsTimeOptions);
  timeInLoadChartBox.render();

  const timeTransferChartBox = new ApexCharts(document.querySelector("#timeTransferOperation"), transferTimeOptions);
  timeTransferChartBox.render();

}
</script>';
?>
