    /* 03.09. Charts */
    if (typeof Chart !== "undefined") {
      Chart.defaults.global.defaultFontFamily = "'Nunito', sans-serif";

      Chart.defaults.LineWithShadow = Chart.defaults.line;
      Chart.controllers.LineWithShadow = Chart.controllers.line.extend({
        draw: function (ease) {
          Chart.controllers.line.prototype.draw.call(this, ease);
          var ctx = this.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.15)";
          ctx.shadowBlur = 10;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 10;
          ctx.responsive = true;
          ctx.stroke();
          Chart.controllers.line.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.BarWithShadow = Chart.defaults.bar;
      Chart.controllers.BarWithShadow = Chart.controllers.bar.extend({
        draw: function (ease) {
          Chart.controllers.bar.prototype.draw.call(this, ease);
          var ctx = this.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.15)";
          ctx.shadowBlur = 12;
          ctx.shadowOffsetX = 5;
          ctx.shadowOffsetY = 10;
          ctx.responsive = true;
          Chart.controllers.bar.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.LineWithLine = Chart.defaults.line;
      Chart.controllers.LineWithLine = Chart.controllers.line.extend({
        draw: function (ease) {
          Chart.controllers.line.prototype.draw.call(this, ease);
          if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
            var activePoint = this.chart.tooltip._active[0];
            var ctx = this.chart.ctx;
            var x = activePoint.tooltipPosition().x;
            var topY = this.chart.scales["y-axis-0"].top;
            var bottomY = this.chart.scales["y-axis-0"].bottom;
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, topY);
            ctx.lineTo(x, bottomY);
            ctx.lineWidth = 1;
            ctx.strokeStyle = "rgba(0,0,0,0.1)";
            ctx.stroke();
            ctx.restore();
          }
        }
      });

      Chart.defaults.DoughnutWithShadow = Chart.defaults.doughnut;
      Chart.controllers.DoughnutWithShadow = Chart.controllers.doughnut.extend({
        draw: function (ease) {
          Chart.controllers.doughnut.prototype.draw.call(this, ease);
          let ctx = this.chart.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.15)";
          ctx.shadowBlur = 10;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 10;
          ctx.responsive = true;
          Chart.controllers.doughnut.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.PieWithShadow = Chart.defaults.pie;
      Chart.controllers.PieWithShadow = Chart.controllers.pie.extend({
        draw: function (ease) {
          Chart.controllers.pie.prototype.draw.call(this, ease);
          let ctx = this.chart.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.15)";
          ctx.shadowBlur = 10;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 10;
          ctx.responsive = true;
          Chart.controllers.pie.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.ScatterWithShadow = Chart.defaults.scatter;
      Chart.controllers.ScatterWithShadow = Chart.controllers.scatter.extend({
        draw: function (ease) {
          Chart.controllers.scatter.prototype.draw.call(this, ease);
          let ctx = this.chart.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.2)";
          ctx.shadowBlur = 7;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 7;
          ctx.responsive = true;
          Chart.controllers.scatter.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.RadarWithShadow = Chart.defaults.radar;
      Chart.controllers.RadarWithShadow = Chart.controllers.radar.extend({
        draw: function (ease) {
          Chart.controllers.radar.prototype.draw.call(this, ease);
          let ctx = this.chart.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.2)";
          ctx.shadowBlur = 7;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 7;
          ctx.responsive = true;
          Chart.controllers.radar.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      Chart.defaults.PolarWithShadow = Chart.defaults.polarArea;
      Chart.controllers.PolarWithShadow = Chart.controllers.polarArea.extend({
        draw: function (ease) {
          Chart.controllers.polarArea.prototype.draw.call(this, ease);
          let ctx = this.chart.chart.ctx;
          ctx.save();
          ctx.shadowColor = "rgba(0,0,0,0.2)";
          ctx.shadowBlur = 10;
          ctx.shadowOffsetX = 5;
          ctx.shadowOffsetY = 10;
          ctx.responsive = true;
          Chart.controllers.polarArea.prototype.draw.apply(this, arguments);
          ctx.restore();
        }
      });

      var chartTooltip = {
        backgroundColor: foregroundColor,
        titleFontColor: primaryColor,
        borderColor: separatorColor,
        borderWidth: 0.5,
        bodyFontColor: primaryColor,
        bodySpacing: 10,
        xPadding: 15,
        yPadding: 15,
        cornerRadius: 0.15,
        displayColors: false
      };

      if (document.getElementById("visitChartFull")) {
        var visitChartFull = document
          .getElementById("visitChartFull")
          .getContext("2d");
        var myChart = new Chart(visitChartFull, {
          type: "LineWithShadow",
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "Data",
                borderColor: themeColor1,
                pointBorderColor: themeColor1,
                pointBackgroundColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: themeColor1,
                pointRadius: 3,
                pointBorderWidth: 3,
                pointHoverRadius: 3,
                fill: true,
                backgroundColor: themeColor1_10,
                borderWidth: 2,
                data: [180, 140, 150, 120, 180, 110, 160],
                datalabels: {
                  align: "end",
                  anchor: "end"
                }
              }
            ]
          },
          options: {
            layout: {
              padding: {
                left: 0,
                right: 0,
                top: 40,
                bottom: 0
              }
            },
            plugins: {
              datalabels: {
                backgroundColor: "transparent",
                borderRadius: 30,
                borderWidth: 1,
                padding: 5,
                borderColor: function (context) {
                  return context.dataset.borderColor;
                },
                color: function (context) {
                  return context.dataset.borderColor;
                },
                font: {
                  weight: "bold",
                  size: 10
                },
                formatter: Math.round
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              display: false
            },
            tooltips: chartTooltip,
            scales: {
              yAxes: [
                {
                  ticks: {
                    min: 0
                  },
                  display: false
                }
              ],
              xAxes: [
                {
                  ticks: {
                    min: 0
                  },
                  display: false
                }
              ]
            }
          }
        });
      }

      if (document.getElementById("visitChart")) {
        var visitChart = document.getElementById("visitChart").getContext("2d");
        var myChart = new Chart(visitChart, {
          type: "LineWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 0
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [54, 63, 60, 65, 60, 68, 60],
                borderColor: themeColor1,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 4,
                pointBorderWidth: 2,
                pointHoverRadius: 5,
                fill: true,
                borderWidth: 2,
                backgroundColor: themeColor1_10
              }
            ]
          }
        });
      }

      if (document.getElementById("conversionChart")) {
        var conversionChart = document
          .getElementById("conversionChart")
          .getContext("2d");
        var myChart = new Chart(conversionChart, {
          type: "LineWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 0
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [65, 60, 68, 54, 63, 60, 60],
                borderColor: themeColor2,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor2,
                pointHoverBackgroundColor: themeColor2,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 4,
                pointBorderWidth: 2,
                pointHoverRadius: 5,
                fill: true,
                borderWidth: 2,
                backgroundColor: themeColor2_10
              }
            ]
          }
        });
      }

      var smallChartOptions = {
        layout: {
          padding: {
            left: 5,
            right: 5,
            top: 10,
            bottom: 10
          }
        },
        plugins: {
          datalabels: {
            display: false
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        tooltips: {
          intersect: false,
          enabled: false,
          custom: function (tooltipModel) {
            if (tooltipModel && tooltipModel.dataPoints) {
              var $textContainer = $(this._chart.canvas.offsetParent);
              var yLabel = tooltipModel.dataPoints[0].yLabel;
              var xLabel = tooltipModel.dataPoints[0].xLabel;
              var label = tooltipModel.body[0].lines[0].split(":")[0];
              $textContainer.find(".value").html("$" + $.fn.addCommas(yLabel));
              $textContainer.find(".label").html(label + "-" + xLabel);
            }
          }
        },
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              },
              display: false
            }
          ],
          xAxes: [
            {
              display: false
            }
          ]
        }
      };

      var smallChartInit = {
        afterInit: function (chart, options) {
          var $textContainer = $(chart.canvas.offsetParent);
          var yLabel = chart.data.datasets[0].data[0];
          var xLabel = chart.data.labels[0];
          var label = chart.data.datasets[0].label;
          $textContainer.find(".value").html("$" + $.fn.addCommas(yLabel));
          $textContainer.find(".label").html(label + "-" + xLabel);
        }
      };

      if (document.getElementById("smallChart1")) {
        var smallChart1 = document
          .getElementById("smallChart1")
          .getContext("2d");
        var myChart = new Chart(smallChart1, {
          type: "LineWithLine",
          plugins: [smallChartInit],
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "Total Orders",
                borderColor: themeColor1,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: themeColor1,
                pointRadius: 2,
                pointBorderWidth: 3,
                pointHoverRadius: 2,
                fill: false,
                borderWidth: 2,
                data: [1250, 1300, 1550, 921, 1810, 1106, 1610],
                datalabels: {
                  align: "end",
                  anchor: "end"
                }
              }
            ]
          },
          options: smallChartOptions
        });
      }

      if (document.getElementById("smallChart2")) {
        var smallChart2 = document
          .getElementById("smallChart2")
          .getContext("2d");
        var myChart = new Chart(smallChart2, {
          type: "LineWithLine",
          plugins: [smallChartInit],
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "Pending Orders",
                borderColor: themeColor1,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: themeColor1,
                pointRadius: 2,
                pointBorderWidth: 3,
                pointHoverRadius: 2,
                fill: false,
                borderWidth: 2,
                data: [115, 120, 300, 222, 105, 85, 36],
                datalabels: {
                  align: "end",
                  anchor: "end"
                }
              }
            ]
          },
          options: smallChartOptions
        });
      }

      if (document.getElementById("smallChart3")) {
        var smallChart3 = document
          .getElementById("smallChart3")
          .getContext("2d");
        var myChart = new Chart(smallChart3, {
          type: "LineWithLine",
          plugins: [smallChartInit],
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "Active Orders",
                borderColor: themeColor1,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: themeColor1,
                pointRadius: 2,
                pointBorderWidth: 3,
                pointHoverRadius: 2,
                fill: false,
                borderWidth: 2,
                data: [350, 452, 762, 952, 630, 85, 158],
                datalabels: {
                  align: "end",
                  anchor: "end"
                }
              }
            ]
          },
          options: smallChartOptions
        });
      }

      if (document.getElementById("smallChart4")) {
        var smallChart4 = document
          .getElementById("smallChart4")
          .getContext("2d");
        var myChart = new Chart(smallChart4, {
          type: "LineWithLine",
          plugins: [smallChartInit],
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "Shipped Orders",
                borderColor: themeColor1,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: themeColor1,
                pointRadius: 2,
                pointBorderWidth: 3,
                pointHoverRadius: 2,
                fill: false,
                borderWidth: 2,
                data: [200, 452, 250, 630, 125, 85, 20],
                datalabels: {
                  align: "end",
                  anchor: "end"
                }
              }
            ]
          },
          options: smallChartOptions
        });
      }

      if (document.getElementById("salesChart")) {
        var salesChart = document.getElementById("salesChart").getContext("2d");
        var myChart = new Chart(salesChart, {
          type: "LineWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [54, 63, 60, 65, 60, 68, 60],
                borderColor: themeColor1,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 6,
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                fill: false
              }
            ]
          }
        });
      }

      if (document.getElementById("areaChart")) {
        var areaChart = document.getElementById("areaChart").getContext("2d");
        var myChart = new Chart(areaChart, {
          type: "LineWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 0
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [54, 63, 60, 65, 60, 68, 60],
                borderColor: themeColor1,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 4,
                pointBorderWidth: 2,
                pointHoverRadius: 5,
                fill: true,
                borderWidth: 2,
                backgroundColor: themeColor1_10
              }
            ]
          }
        });
      }

      if (document.getElementById("areaChartNoShadow")) {
        var areaChartNoShadow = document
          .getElementById("areaChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(areaChartNoShadow, {
          type: "line",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 0
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [54, 63, 60, 65, 60, 68, 60],
                borderColor: themeColor1,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 4,
                pointBorderWidth: 2,
                pointHoverRadius: 5,
                fill: true,
                borderWidth: 2,
                backgroundColor: themeColor1_10
              }
            ]
          }
        });
      }

      if (document.getElementById("scatterChart")) {
        var scatterChart = document
          .getElementById("scatterChart")
          .getContext("2d");
        var myChart = new Chart(scatterChart, {
          type: "ScatterWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 20,
                    min: -80,
                    max: 80,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)"
                  }
                }
              ]
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                borderWidth: 2,
                label: "Cakes",
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [
                  { x: 62, y: -78 },
                  { x: -0, y: 74 },
                  { x: -67, y: 45 },
                  { x: -26, y: -43 },
                  { x: -15, y: -30 },
                  { x: 65, y: -68 },
                  { x: -28, y: -61 }
                ]
              },
              {
                borderWidth: 2,
                label: "Desserts",
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [
                  { x: 79, y: 62 },
                  { x: 62, y: 0 },
                  { x: -76, y: -81 },
                  { x: -51, y: 41 },
                  { x: -9, y: 9 },
                  { x: 72, y: -37 },
                  { x: 62, y: -26 }
                ]
              }
            ]
          }
        });
      }

      if (document.getElementById("scatterChartNoShadow")) {
        var scatterChartNoShadow = document
          .getElementById("scatterChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(scatterChartNoShadow, {
          type: "scatter",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 20,
                    min: -80,
                    max: 80,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)"
                  }
                }
              ]
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                borderWidth: 2,
                label: "Cakes",
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [
                  { x: 62, y: -78 },
                  { x: -0, y: 74 },
                  { x: -67, y: 45 },
                  { x: -26, y: -43 },
                  { x: -15, y: -30 },
                  { x: 65, y: -68 },
                  { x: -28, y: -61 }
                ]
              },
              {
                borderWidth: 2,
                label: "Desserts",
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [
                  { x: 79, y: 62 },
                  { x: 62, y: 0 },
                  { x: -76, y: -81 },
                  { x: -51, y: 41 },
                  { x: -9, y: 9 },
                  { x: 72, y: -37 },
                  { x: 62, y: -26 }
                ]
              }
            ]
          }
        });
      }

      if (document.getElementById("radarChartNoShadow")) {
        var radarChartNoShadow = document
          .getElementById("radarChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(radarChartNoShadow, {
          type: "radar",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scale: {
              ticks: {
                display: false
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                label: "Stock",
                borderWidth: 2,
                pointBackgroundColor: themeColor1,
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [80, 90, 70]
              },
              {
                label: "Order",
                borderWidth: 2,
                pointBackgroundColor: themeColor2,
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [68, 80, 95]
              }
            ],
            labels: ["Cakes", "Desserts", "Cupcakes"]
          }
        });
      }

      if (document.getElementById("radarChart")) {
        var radarChart = document.getElementById("radarChart").getContext("2d");
        var myChart = new Chart(radarChart, {
          type: "RadarWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scale: {
              ticks: {
                display: false
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                label: "Stock",
                borderWidth: 2,
                pointBackgroundColor: themeColor1,
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [80, 90, 70]
              },
              {
                label: "Order",
                borderWidth: 2,
                pointBackgroundColor: themeColor2,
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [68, 80, 95]
              }
            ],
            labels: ["Cakes", "Desserts", "Cupcakes"]
          }
        });
      }

      if (document.getElementById("polarChart")) {
        var polarChart = document.getElementById("polarChart").getContext("2d");
        var myChart = new Chart(polarChart, {
          type: "PolarWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scale: {
              ticks: {
                display: false
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                label: "Stock",
                borderWidth: 2,
                pointBackgroundColor: themeColor1,
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                data: [80, 90, 50]
              }
            ],
            labels: ["Cakes", "Desserts", "Cupcakes"]
          }
        });
      }

      if (document.getElementById("polarChartNoShadow")) {
        var polarChartNoShadow = document
          .getElementById("polarChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(polarChartNoShadow, {
          type: "polarArea",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scale: {
              ticks: {
                display: false
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            datasets: [
              {
                label: "Stock",
                borderWidth: 2,
                pointBackgroundColor: themeColor1,
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                data: [80, 90, 70]
              }
            ],
            labels: ["Cakes", "Desserts", "Cupcakes"]
          }
        });
      }

      if (document.getElementById("salesChartNoShadow")) {
        var salesChartNoShadow = document
          .getElementById("salesChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(salesChartNoShadow, {
          type: "line",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 5,
                    min: 50,
                    max: 70,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              display: false
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
              {
                label: "",
                data: [54, 63, 60, 65, 60, 68, 60],
                borderColor: themeColor1,
                pointBackgroundColor: foregroundColor,
                pointBorderColor: themeColor1,
                pointHoverBackgroundColor: themeColor1,
                pointHoverBorderColor: foregroundColor,
                pointRadius: 6,
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                fill: false
              }
            ]
          }
        });
      }

      if (document.getElementById("productChart")) {
        var productChart = document
          .getElementById("productChart")
          .getContext("2d");
        var myChart = new Chart(productChart, {
          type: "BarWithShadow",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 100,
                    min: 300,
                    max: 800,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["January", "February", "March", "April", "May", "June"],
            datasets: [
              {
                label: "Cakes",
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [456, 479, 324, 569, 702, 600],
                borderWidth: 2
              },
              {
                label: "Desserts",
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [364, 504, 605, 400, 345, 320],
                borderWidth: 2
              }
            ]
          }
        });
      }

      if (document.getElementById("productChartNoShadow")) {
        var productChartNoShadow = document
          .getElementById("productChartNoShadow")
          .getContext("2d");
        var myChart = new Chart(productChartNoShadow, {
          type: "bar",
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [
                {
                  gridLines: {
                    display: true,
                    lineWidth: 1,
                    color: "rgba(0,0,0,0.1)",
                    drawBorder: false
                  },
                  ticks: {
                    beginAtZero: true,
                    stepSize: 100,
                    min: 300,
                    max: 800,
                    padding: 20
                  }
                }
              ],
              xAxes: [
                {
                  gridLines: {
                    display: false
                  }
                }
              ]
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          },
          data: {
            labels: ["January", "February", "March", "April", "May", "June"],
            datasets: [
              {
                label: "Cakes",
                borderColor: themeColor1,
                backgroundColor: themeColor1_10,
                data: [456, 479, 324, 569, 702, 600],
                borderWidth: 2
              },
              {
                label: "Desserts",
                borderColor: themeColor2,
                backgroundColor: themeColor2_10,
                data: [364, 504, 605, 400, 345, 320],
                borderWidth: 2
              }
            ]
          }
        });
      }

      var contributionChartOptions = {
        type: "LineWithShadow",
        options: {
          plugins: {
            datalabels: {
              display: false
            }
          },
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            yAxes: [
              {
                gridLines: {
                  display: true,
                  lineWidth: 1,
                  color: "rgba(0,0,0,0.1)",
                  drawBorder: false
                },
                ticks: {
                  beginAtZero: true,
                  stepSize: 5,
                  min: 50,
                  max: 70,
                  padding: 20
                }
              }
            ],
            xAxes: [
              {
                gridLines: {
                  display: false
                }
              }
            ]
          },
          legend: {
            display: false
          },
          tooltips: chartTooltip
        },
        data: {
          labels: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
          ],
          datasets: [
            {
              borderWidth: 2,
              label: "",
              data: [54, 63, 60, 65, 60, 68, 60, 63, 60, 65, 60, 68],
              borderColor: themeColor1,
              pointBackgroundColor: foregroundColor,
              pointBorderColor: themeColor1,
              pointHoverBackgroundColor: themeColor1,
              pointHoverBorderColor: foregroundColor,
              pointRadius: 4,
              pointBorderWidth: 2,
              pointHoverRadius: 5,
              fill: false
            }
          ]
        }
      };

      if (document.getElementById("contributionChart1")) {
        var contributionChart1 = new Chart(
          document.getElementById("contributionChart1").getContext("2d"),
          contributionChartOptions
        );
      }

      if (document.getElementById("contributionChart2")) {
        var contributionChart2 = new Chart(
          document.getElementById("contributionChart2").getContext("2d"),
          contributionChartOptions
        );
      }

      if (document.getElementById("contributionChart3")) {
        var contributionChart3 = new Chart(
          document.getElementById("contributionChart3").getContext("2d"),
          contributionChartOptions
        );
      }

      var centerTextPlugin = {
        afterDatasetsUpdate: function (chart) { },
        beforeDraw: function (chart) {
          var width = chart.chartArea.right;
          var height = chart.chartArea.bottom;
          var ctx = chart.chart.ctx;
          ctx.restore();

          var activeLabel = chart.data.labels[0];
          var activeValue = chart.data.datasets[0].data[0];
          var dataset = chart.data.datasets[0];
          var meta = dataset._meta[Object.keys(dataset._meta)[0]];
          var total = meta.total;

          var activePercentage = parseFloat(
            ((activeValue / total) * 100).toFixed(1)
          );
          activePercentage = chart.legend.legendItems[0].hidden
            ? 0
            : activePercentage;

          if (chart.pointAvailable) {
            activeLabel = chart.data.labels[chart.pointIndex];
            activeValue =
              chart.data.datasets[chart.pointDataIndex].data[chart.pointIndex];

            dataset = chart.data.datasets[chart.pointDataIndex];
            meta = dataset._meta[Object.keys(dataset._meta)[0]];
            total = meta.total;
            activePercentage = parseFloat(
              ((activeValue / total) * 100).toFixed(1)
            );
            activePercentage = chart.legend.legendItems[chart.pointIndex].hidden
              ? 0
              : activePercentage;
          }

          ctx.font = "36px" + " Nunito, sans-serif";
          ctx.fillStyle = primaryColor;
          ctx.textBaseline = "middle";

          var text = activePercentage + "%",
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;
          ctx.fillText(text, textX, textY);

          ctx.font = "14px" + " Nunito, sans-serif";
          ctx.textBaseline = "middle";

          var text2 = activeLabel,
            textX = Math.round((width - ctx.measureText(text2).width) / 2),
            textY = height / 2 - 30;
          ctx.fillText(text2, textX, textY);

          ctx.save();
        },
        beforeEvent: function (chart, event, options) {
          var firstPoint = chart.getElementAtEvent(event)[0];

          if (firstPoint) {
            chart.pointIndex = firstPoint._index;
            chart.pointDataIndex = firstPoint._datasetIndex;
            chart.pointAvailable = true;
          }
        }
      };

      if (document.getElementById("categoryChart")) {
        var categoryChart = document.getElementById("categoryChart");
        var myDoughnutChart = new Chart(categoryChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: ["Cakes", "Cupcakes", "Desserts"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor3, themeColor2, themeColor1],
                backgroundColor: [
                  themeColor3_10,
                  themeColor2_10,
                  themeColor1_10
                ],
                borderWidth: 2,
                data: [15, 25, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("categoryChartNoShadow")) {
        var categoryChartNoShadow = document.getElementById(
          "categoryChartNoShadow"
        );
        var myDoughnutChart = new Chart(categoryChartNoShadow, {
          plugins: [centerTextPlugin],
          type: "doughnut",
          data: {
            labels: ["Cakes", "Cupcakes", "Desserts"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor3, themeColor2, themeColor1],
                backgroundColor: [
                  themeColor3_10,
                  themeColor2_10,
                  themeColor1_10
                ],
                borderWidth: 2,
                data: [15, 25, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("pieChartNoShadow")) {
        var pieChart = document.getElementById("pieChartNoShadow");
        var myChart = new Chart(pieChart, {
          type: "pie",
          data: {
            labels: ["Cakes", "Cupcakes", "Desserts"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                borderWidth: 2,
                data: [15, 25, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("pieChart")) {
        var pieChart = document.getElementById("pieChart");
        var myChart = new Chart(pieChart, {
          type: "PieWithShadow",
          data: {
            labels: ["Cakes", "Cupcakes", "Desserts"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                borderWidth: 2,
                data: [15, 25, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("frequencyChart")) {
        var frequencyChart = document.getElementById("frequencyChart");
        var myDoughnutChart = new Chart(frequencyChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: ["Adding", "Editing", "Deleting"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                borderWidth: 2,
                data: [15, 25, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("ageChart")) {
        var ageChart = document.getElementById("ageChart");
        var myDoughnutChart = new Chart(ageChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: ["12-24", "24-30", "30-40", "40-50", "50-60"],
            datasets: [
              {
                label: "",
                borderColor: [
                  themeColor1,
                  themeColor2,
                  themeColor3,
                  themeColor4,
                  themeColor5
                ],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10,
                  themeColor4_10,
                  themeColor5_10
                ],
                borderWidth: 2,
                data: [15, 25, 20, 30, 14]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("genderChart")) {
        var genderChart = document.getElementById("genderChart");
        var myDoughnutChart = new Chart(genderChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: ["Male", "Female", "Other"],
            datasets: [
              {
                label: "",
                borderColor: [themeColor1, themeColor2, themeColor3],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10
                ],
                borderWidth: 2,
                data: [85, 45, 20]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("workChart")) {
        var workChart = document.getElementById("workChart");
        var myDoughnutChart = new Chart(workChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: [
              "Employed for wages",
              "Self-employed",
              "Looking for work",
              "Retired"
            ],
            datasets: [
              {
                label: "",
                borderColor: [
                  themeColor1,
                  themeColor2,
                  themeColor3,
                  themeColor4
                ],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10,
                  themeColor4_10
                ],
                borderWidth: 2,
                data: [15, 25, 20, 8]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }

      if (document.getElementById("codingChart")) {
        var codingChart = document.getElementById("codingChart");
        var myDoughnutChart = new Chart(codingChart, {
          plugins: [centerTextPlugin],
          type: "DoughnutWithShadow",
          data: {
            labels: ["Python", "JavaScript", "PHP", "Java", "C#"],
            datasets: [
              {
                label: "",
                borderColor: [
                  themeColor1,
                  themeColor2,
                  themeColor3,
                  themeColor4,
                  themeColor5
                ],
                backgroundColor: [
                  themeColor1_10,
                  themeColor2_10,
                  themeColor3_10,
                  themeColor4_10,
                  themeColor5_10
                ],
                borderWidth: 2,
                data: [15, 25, 20, 8, 25]
              }
            ]
          },
          draw: function () { },
          options: {
            plugins: {
              datalabels: {
                display: false
              }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 80,
            title: {
              display: false
            },
            layout: {
              padding: {
                bottom: 20
              }
            },
            legend: {
              position: "bottom",
              labels: {
                padding: 30,
                usePointStyle: true,
                fontSize: 12
              }
            },
            tooltips: chartTooltip
          }
        });
      }
    }