var updateInterval = 2000;
var charts = {};

$(function(){
    $.nette.ext('plot', {
        success: function () {
            registerCharts();
        }
    });
    $.nette.ext('unique', null);
    $.nette.init();
    registerCharts();
    updateRecentMeasurementsCharts();
});

function initializeChart($el) {
    if ($el.data('initialized')) return;
    var chart = $.plot($el, [], {
        grid: {
            borderColor: '#f3f3f3',
            borderWidth: 1,
            tickColor: '#f3f3f3',
            backgroundColor: 'white'
        },
        series: {
            shadowSize: 0, // Drawing is faster without shadows
            color: '#3c8dbc'
        },
        lines: {
            fill: true, //Converts the line chart to area chart
            color: '#3c8dbc'
        },
        yaxis: {
            min: 0,
            max: 5,
            show: true
        },
        xaxis: {
            show: true,
            mode: 'time'
        }
    });
    $el.data('initialized', true);
    charts[$el.data('deviceId')] = chart;
}

function registerCharts()
{
    var confirmed = {};
    $('.recent-current-chart').each(function() {
        confirmed[$(this).data('deviceId')] = true;
        if ($(this).data('initialized')) return;
        initializeChart($(this));
    });
    for (var k in charts) {
        if (k in confirmed) continue;
        delete charts[k];
    }
}

function parseMeasurementData(data)
{
    var res = [];
    for (var k in data) {
        res.push([new Date(data[k].time).getTime(), data[k].value]);
    }
    return res;
}

function updateRecentMeasurementsCharts()
{
    if (Object.keys(charts).length) {
        $.nette.ajax({
            method: "get",
            url: "/device/recent-measurements",
            success: function (data) {
                for (var k in charts) {
                    d = parseMeasurementData(data[k].measurements);
                    charts[k].setData([d]);
                    charts[k].setupGrid();
                    charts[k].draw();
                }
                for (k in data) {
                    $('.total-consumed[data-device-id="'+k.toString()+'"]').html(data[k].consumedTotal);
                }
            }
        });
    }
    setTimeout(updateRecentMeasurementsCharts, updateInterval);
}