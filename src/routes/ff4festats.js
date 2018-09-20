export default {
    init() {
        // JavaScript to be fired on all pages

        const canvas = document.getElementById('heatmap-canvas');
        const dayData = {
            labels: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
            datasets: [{
                label: 'Sunday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Monday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Tuesday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Wednesday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Thursday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Friday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },{
                label: 'Saturday',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }]};

        console.log('ff4-fe-stats');
        fetch('http://api.speedrunslive.com/pastraces?game=ff4hacks&season=0&page=1&pageSize=100')
            .then(resp => resp.json())
            .then(data => {
                data.pastraces.forEach(race => {
                    let UTCDate = new Date(parseInt(race.date * 1000));
                    let raceDate = new Date(UTCDate.getTime() - UTCDate.getTimezoneOffset() * 60000);
                    // raceDate.setUTCSeconds(race.date);
                    let dateDay = raceDate.getDay();
                    let dateHour = raceDate.getHours();
                    dayData.datasets[dateDay].data[dateHour]++;
                });
                console.log(dayData);
                constructHeatMap();
            });

        function findMax() {
            let max = 0;
            dayData.datasets.forEach(day => {
                day.data.forEach(hour => {
                    if (hour > max) max = hour;
                });
            });
            return max;
        }

        function constructHeatMap() {
            const max = findMax();
            canvas.innerHTML = '';
            dayData.labels.forEach((hour, index) => {
                const chartRow = document.createElement('div');
                chartRow.style.height = '20px;'
                chartRow.classList.add(`chart-row-${index}`, 'd-flex');
                canvas.appendChild(chartRow);
                const header = document.createElement('div');
                header.style.width = '12.5%';
                header.style.height = '20px';
                header.style.borderRight = '1px solid #333';
                if (index % 4 === 0) header.innerHTML = hour;
                chartRow.appendChild(header);
                dayData.datasets.forEach((day, dayIndex) => {
                    const cell = document.createElement('div');
                    cell.style.width = '12.5%';
                    cell.style.height = '20px';
                    cell.style.color = '#eee';
                    cell.classList.add('d-flex', 'justify-content-center', 'align-items-center', 'press-start');
                    const blue = Math.floor(dayData.datasets[dayIndex].data[index] / max * 255);
                    cell.style.backgroundColor = `rgb(0, 0, ${blue})`;
                    if (dayData.datasets[dayIndex].data[index] > 0) cell.innerHTML = dayData.datasets[dayIndex].data[index];
                    chartRow.appendChild(cell);
                })
            });
            const xLabel = document.createElement('div');
            xLabel.style.height = '20px';
            xLabel.classList.add('d-flex');
            canvas.appendChild(xLabel);
            const xHeader = document.createElement('div');
            xHeader.style.width = '12.5%';
            xHeader.style.height = '20px';
            xHeader.classList.add('d-flex');
            xLabel.appendChild(xHeader);
            dayData.datasets.forEach((day, dayIndex) => {
                const cell = document.createElement('div');
                cell.style.width = '12.5%';
                cell.style.height = '20px';
                cell.style.borderTop = '1px solid #333';
                cell.innerHTML = day.label;
                cell.classList.add('d-flex', 'justify-content-center');
                xLabel.appendChild(cell);
            })

        }
    },
    finalize() {
        // JavaScript to be fired on all pages, after page specific JS is fired
    },
};
