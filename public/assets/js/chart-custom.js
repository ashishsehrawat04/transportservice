  // Get the drawing context on the canvas 
//   var densityCanvas = document.getElementById("consulting-chart").getContext('2d');

//   Chart.defaults.global.defaultFontFamily = "SUSE";
//   Chart.defaults.global.defaultFontSize = 14;
 
   
//   var barChart = new Chart(densityCanvas, {
//     type: 'bar',
//     data: { 
//         labels: ["2018", "2019", "2020", "2021", "2022", "2023"],
//         datasets: [{
//             label: 'Retention Rate',
//             backgroundColor: '#60E59D',
//             borderColor: '#60E59D',
//             Color: '#fff',
//             data: [66, 78, 88, 70, 90, 92],
//         },{
//             label: 'Satisfaction Rate',
//             backgroundColor: '#028D61',
//             borderColor: '#789FC0',
//             data: [53, 69, 95, 82, 96, 87],
//         }
//     ] 
//     }, 
//     options: { 
//         plugins: { 
//             title: { 
//                 display: true, 
//                 text: 'Chart.js Bar Chart - Stacked' 
//             }, 
//         }, 
//         interaction: { 
//             intersect: false, 
//         }, 
//         scales: {
//             backgroundColor: 'red',
//             borderColor: 'red',
//             y: {
//                 beginAtZero: true
//             }
//         },
//         responsive: true 
//     } 
//   });

const ctx = document.getElementById('consulting-chart');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["2018", "2019", "2020", "2021", "2022", "2023"],
    datasets: [{
      label: 'Retention Rate',
      backgroundColor: '#60E59D',
      borderColor: '#60E59D',
      data: [66, 78, 88, 70, 90, 92],
      barThickness: 15 // Set bar width to 15px
    }, {
      label: 'Satisfaction Rate',
      backgroundColor: '#028D61',
      borderColor: '#789FC0',
      data: [53, 69, 95, 82, 96, 87],
      barThickness: 15 // Set bar width to 15px
    }]
  },
  options: {
    plugins: {
      legend: {
        display: true,
        labels: {
          color: '#FFFFFF',
          font: {
            size: 14
          },
          boxWidth: 20,
          boxHeight: 10,
          padding: 20
        },
        position: 'top',
      },
      tooltip: {
        enabled: true
      }
    },
    layout: {
      padding: {
        top: 20
      }
    },
    scales: {
      x: {
        ticks: {
          color: '#ddd',
          font: {
            size: 14
          }
        },
        grid: {
          display: false
        },
        border: {
          color: '#ddd'
        }
      },
      y: {
        min: 0, // Define the minimum value for y-axis
        max: 100, // Define the maximum value for y-axis
        ticks: {
          color: '#ddd',
          font: {
            size: 14
          },
          stepSize: 20, // Define the interval between ticks
        //   callback: function(value) {
        //     const allowedValues = [0, 10,20,30,40, 50, 60, 70, 80, 90, 100, 110];
        //     return allowedValues.includes(value) ? value : null; // Display only allowed values
        //   }
        },
        grid: {
          display: false
        },
        border: {
          color: '#ddd'
        }
      }
    },
    responsive: false,
    maintainAspectRatio: false
  }
});
