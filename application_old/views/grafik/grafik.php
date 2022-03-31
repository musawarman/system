<div class="row">
    <div class="col-lg-6" style="padding:20px">
        <canvas style="border:1px solid #ccc;padding:0px 0px 20px 0px" id="barChart"></canvas>
    </div>

    <div class="col-lg-6" style="padding:20px">
        <canvas style="border:1px solid #ccc;padding:0px 0px 20px 0px" id="barChart2"></canvas>
    </div>
    <div class="col-lg-6" style="padding:20px">
        <canvas style="border:1px solid #ccc;padding:0px 0px 20px 0px" id="barChart3"></canvas>
    </div>
</div>
<script src="<?=base_url()?>/assets/chartjs/chartjs.js"></script>
<script>
// var ctx = document.getElementById("myChart").getContext("2d");

var canvas = document.getElementById("barChart");
var ctx = canvas.getContext('2d');



// Global Options:
 Chart.defaults.global.defaultFontColor = 'black';
 Chart.defaults.global.defaultFontSize = 16;

var data = {
    labels: ["Quotes In", "Quotes Out"],
      datasets: [
        {
            fill: true,
            backgroundColor: [
                'orange',
                'green'],
            data: [<?=$quote_in?>, <?=$quote_out?>],
// Notice the borderColor 
            borderColor:	['black', 'black'],
            borderWidth: [2,2]
        }
    ]
};

// Notice the rotation from the documentation.

var options = {
        title: {
                  display: true,
                  text: 'Jumlah Quotes',
                  position: 'top'
              },
        rotation: -0.7 * Math.PI
};


// Chart declaration:
var myBarChart = new Chart(ctx, {
    type: 'pie',
    data: data,
    options: options
});


var canvas2 = document.getElementById("barChart2");
var ctx2 = canvas2.getContext('2d');
var data = {
    labels: ["PO In", "PO Out"],
      datasets: [
        {
            fill: true,
            backgroundColor: [
                'lightskyblue',
                'navy'],
            data: [<?=$po_in?>, <?=$po_out?>],
// Notice the borderColor 
            borderColor:	['black', 'black'],
            borderWidth: [2,2]
        }
    ]
};

// Notice the rotation from the documentation.

var options = {
        title: {
                  display: true,
                  text: 'Jumlah PO',
                  position: 'top'
              },
        rotation: -0.7 * Math.PI
};
var myBarChart2 = new Chart(ctx2, {
    type: 'pie',
    data: data,
    options: options
});

var canvas3 = document.getElementById("barChart3");
var ctx3 = canvas3.getContext('2d');
var data = {
    labels: ["Invoice In", "Invoice Out"],
      datasets: [
        {
            fill: true,
            backgroundColor: [
                'red',
                'yellow'],
            data: [<?=$inv_in?>, <?=$inv_out?>],
// Notice the borderColor 
            borderColor:	['black', 'black'],
            borderWidth: [2,2]
        }
    ]
};

// Notice the rotation from the documentation.

var options = {
        title: {
                  display: true,
                  text: 'Jumlah Invoice',
                  position: 'top'
              },
        rotation: -0.7 * Math.PI
};
var myBarChart3 = new Chart(ctx3, {
    type: 'pie',
    data: data,
    options: options
});
// var data = {
//     labels: [
//     "Qoutes",
//     "PO",
//     "Invoice"],
//   datasets: [{
//     label : "IN",
//     backgroundColor: "#666555",
//     data: [<?=$quote_in?>,<?=$po_in?>,<?=$inv_in?>]
//   }, {
//     label : "OUT",
//     backgroundColor: "red",
//     data: [<?=$quote_out?>,<?=$po_out?>,<?=$inv_out?>]
//   }]
// };

// var myBarChart = new Chart(ctx, {
//   type: 'bar',
//   data: data,
//   options: {
//     barValueSpacing: 20,
//     scales: {
//       yAxes: [{
//         ticks: {
//           min: 0,
//         }
//       }]
//     }
//   }
// });

</script>