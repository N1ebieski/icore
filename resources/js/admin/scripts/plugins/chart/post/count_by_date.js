
$(document).on('ready.n1ebieski/icore/admin/scripts/plugins/chart/post@countByDate', function () {
    let $chart = $('#count-posts-and-pages-by-date');

    if ($chart.length) {
        $chart.dataset = JSON.parse($chart.attr('data'));

        let timeline = [...new Map($chart.dataset.map(item => [`${item.month}.${item.year}`, item])).values()];
        let types = [...new Map($chart.dataset.map(item => [item.type.value, item])).values()];

        $chart.chart({
            type: 'bar',            
            data: {
                datasets: [{
                    label: $chart.data('all-label'),
                    type: 'line',
                    backgroundColor: 'rgb(0, 123, 255)',
                    borderColor: 'rgb(0, 123, 255)',
                    borderWidth: 1,                   
                    data: timeline.map(t => {
                        return {
                            x: `${t.month}.${t.year}`,
                            y: $chart.dataset.filter(i => i.month === t.month && i.year === t.year)
                                .reduce((sum, i) => { return sum + i.count }, 0)
                        };
                    })
                }].concat(types.map(item => {
                    return {
                        label: item.type.label,
                        data: timeline.map(t => {
                            return $chart.dataset.find(i => {
                                return i.month === t.month && i.year === t.year && i.type.value === item.type.value;
                            })?.count || 0;
                        }),
                        backgroundColor: item.color,
                        borderColor: item.color,
                        borderWidth: 1               
                    };
                }))
            },
            options: {  
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            color: $chart.data('font-color') || "#666",
                            display: true,
                            text: $chart.data('x-label')
                        },
                        ticks: {
                            color: $chart.data('font-color') || "#666"
                        }                     
                    },
                    y: {
                        stacked: true,
                        title: {
                            color: $chart.data('font-color') || "#666",
                            display: true,
                            text: $chart.data('y-label')
                        },
                        ticks: {
                            color: $chart.data('font-color') || "#666"
                        }                    
                    }
                },                
                plugins: {
                    legend: {
                        labels: {
                            color: $chart.data('font-color') || "#666"
                        }
                    },                    
                    title: {
                        display: true,
                        text: $chart.data('label'),
                        color: $chart.data('font-color') || "#666",                        
                        font: {
                            size: 14
                        }                    
                    }              
                }
            }
        });

        if (timeline.length > 15) {
            let width = timeline.length * 50;

            $chart.parent().css('width', width);
            $chart.parents().eq(1).scrollLeft(width);
        }
    }
});