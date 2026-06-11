<?php if(isset($supplierDetails) && (!empty($supplierDetails)) && (count($supplierDetails) > 0)){?>
<canvas id="topSuppliersChart" style="height: 350px; max-height: 350px;"></canvas>

<script src="{{ asset('js/chart.js') }}"></script>
<script>
// Use var instead of const to avoid redeclaration errors
var topSuppliersctx = document.getElementById('topSuppliersChart').getContext('2d');

// Destroy existing chart if it exists to prevent duplicates
if (window.topSuppliersChartInstance) {
    window.topSuppliersChartInstance.destroy();
    window.topSuppliersChartInstance = null;
}

const customDataLabelsPlugin = {
    id: 'customDataLabels',
    afterDatasetsDraw: (chart, args, options) => {
        const { ctx } = chart;
        
        ctx.save();
        ctx.font = '10px Roboto';
        ctx.fillStyle = '#000';
        
        chart.data.datasets.forEach((dataset, i) => {
            const meta = chart.getDatasetMeta(i);
            meta.data.forEach((bar, index) => {
                let companyName = "";
                if (window.topCompanies1 && window.topCompanies2) {
                    if (i === 0) {
                        companyName = window.topCompanies1[index];
                    } else {
                        companyName = window.topCompanies2[index];
                    }
                }
                
                if (companyName && companyName !== 'None' && dataset.data[index] > 0) {
                    let displayLabel = companyName.length > 15 ? companyName.substring(0, 15) + '...' : companyName;
                    
                    ctx.save();
                    ctx.translate(bar.x, bar.y - 5);
                    ctx.rotate(-Math.PI / 2); // Rotate -90 degrees
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(displayLabel, 0, 0);
                    ctx.restore();
                }
            });
        });
        ctx.restore();
    }
};

var topSuppliersChart = new Chart(topSuppliersctx, {
    type: 'bar',
    data: {
        labels: [], // Initially empty
        datasets: [
            {
                label: 'Top Company 1',
                data: [], // Initially empty
                backgroundColor: '#8D191A',
                borderColor: '#8D191A',
                borderWidth: 1,
                barThickness: 18,
                maxBarThickness: 18,
            },
            {
                label: 'Top Company 2',
                data: [], // Initially empty
                backgroundColor: '#FFC7C7',
                borderColor: '#FFC7C7',
                borderWidth: 1,
                barThickness: 18,
                maxBarThickness: 18,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                top: 80
            }
        },
        plugins: {
            legend: {
                display: false,
                position: 'bottom',
                onClick: null,
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.parsed.y || 0;
                        let datasetIndex = context.datasetIndex;
                        let index = context.dataIndex;
                        let companyName = "";
                        
                        if(window.topCompanies1 && window.topCompanies2) {
                            if(datasetIndex === 0) {
                                companyName = window.topCompanies1[index];
                            } else {
                                companyName = window.topCompanies2[index];
                            }
                        }
                        
                        if(companyName === 'None') return null;
                        return (companyName ? companyName + ': ' : '') + '£ ' + value.toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: false,
                ticks: {
                    stepSize: 100000,
                    callback: function(value) {
                        return '£' + (value / 1000).toFixed(0) + 'K';
                    },
                    font: {
                        size: '12px',
                        weight: '500',
                    },
                    family: 'Roboto',
                    color: '#000', 
                },
                grid: {
                    color: '#e0e0e0',
                },
                border: {
                    display: false 
                }
            },
            x: {
                ticks: {
                    font: {
                        size: '11px',
                        weight: '500',
                    },
                    family: 'Roboto',
                    color: '#000', 
                    maxRotation: 45,
                    minRotation: 0,
                    // Truncate long supplier names
                    callback: function(value, index, values) {
                        const label = this.getLabelForValue(value);
                        if (label.length > 15) {
                            return label.substring(0, 15) + '...';
                        }
                        return label;
                    }
                },
                grid: {
                    display: false,
                }
            }
        },
    },
    plugins: [customDataLabelsPlugin]
});

// Store reference globally
window.topSuppliersChartInstance = topSuppliersChart;

function updateChart(labels, top1Data, top2Data, top1Companies, top2Companies) {
    // Store globally for tooltip
    window.topCompanies1 = top1Companies;
    window.topCompanies2 = top2Companies;

    // Format the data values to ensure they're numbers
    const formattedData1 = top1Data.map(value => {
        const numValue = parseFloat(value);
        return isNaN(numValue) ? 0 : numValue;
    });
    
    const formattedData2 = top2Data.map(value => {
        const numValue = parseFloat(value);
        return isNaN(numValue) ? 0 : numValue;
    });
    
    topSuppliersChart.data.labels = labels;
    topSuppliersChart.data.datasets[0].data = formattedData1;
    topSuppliersChart.data.datasets[1].data = formattedData2;
    topSuppliersChart.update();
}

<?php if(  count($supplierDetails) > 0 ) { ?>
	var supplierDetails = @json($supplierDetails); 
	var supplierNames = supplierDetails.map(item => item.supplier || 'Unknown');
	
	var top1Data = supplierDetails.map(item => item.top1_amount || 0);
	var top2Data = supplierDetails.map(item => item.top2_amount || 0);
	
	var top1Companies = supplierDetails.map(item => item.top1_company || 'None');
	var top2Companies = supplierDetails.map(item => item.top2_company || 'None');
	
	updateChart(supplierNames, top1Data, top2Data, top1Companies, top2Companies);
<?php } ?>
</script>
<?php } else { ?>
	<div style="text-align: center; padding: 40px; color: #666;">
		<p>No supplier data available</p>
	</div>
<?php } ?>