export default {
    init() {
        console.log('stat page');

        function getPlayerName() {
            const url = window.location.search;
            const index = url.indexOf('player=');
            if (index === -1) {
                return 'Fleury14';
            } else {
                return url.substring(index+7, url.length);
            }
        }

        function addZScore(race) {
            // This function takes in a race and assigns a standard mean, standard deviation for the race and a z-score for each individual playeer

            const raceTimes = [];
            const raceLongTime = getLongestTime(race);
            const forfeitPenalty = 60 * 5;
            // put all times into one array
            race.results.forEach(result => {
                if (result.time === -1) {
                    raceTimes.unshift(raceLongTime + forfeitPenalty);
                } else {
                    raceTimes.unshift(result.time);
                }
            });
            
            // calculate mean
            let standaredMean = raceTimes.reduce((a, b) => a + b, 0) / raceTimes.length;
            race['stdMean'] = standaredMean;

            const sqrdDiff = [];
            // populate array of squared differences
            raceTimes.forEach(time => {
                if (time === -1) time = raceLongTime + forfeitPenalty;
                sqrdDiff.unshift(Math.pow(time - standaredMean, 2));
            });
            
            // get the mean of squred differences
            let sqrdMean = sqrdDiff.reduce((a, b) => a + b, 0) / sqrdDiff.length;
            // and from that get std dev and assign it to race
            let stdDev = Math.sqrt(sqrdMean);
            race['stdDev'] = stdDev;

            // assign each calculated z-score to the respective result
            race.results.forEach(result => {
                result['zScore'] = ((result.time === -1 ? raceLongTime + forfeitPenalty : result.time) - race.stdMean) / race.stdDev;
            })
            
        }

        function getLongestTime(race) {
            let longestTime = 0;
            race.results.forEach(result => {
                if (result.time > longestTime) longestTime = result.time;
            });
            return longestTime;
        }

        function preparePastRaces(races) {
            const tags = document.getElementsByClassName('race-tag');
            Array.prototype.forEach.call(tags, tag => {
                tag.addEventListener('click', (e) => {
                    Array.prototype.forEach.call(tags, tag => tag.classList.remove('active-tag'));
                    const tagPieces = tag.dataset.index.split('-');
                    createRace( raceData[parseInt(tagPieces[1])] );
                    tag.classList.add('active-tag');
                });
            });
        }

        function createRace(race) {
            raceDataElement.innerHTML = '';
            const raceContainer = document.createElement('div');
            // raceContainer.innerHTML = JSON.stringify(race);
            raceDataElement.appendChild(raceContainer);
            const subTitle = document.createElement('h2');
            const title = document.createElement('h2');
            title.classList.add('past-race-header', 'text-center', 'mb-0');
            
            // format date
            const d = new Date(0);
            d.setUTCSeconds( parseInt(race.date) );
            title.textContent = `${d}`;
            raceContainer.appendChild(title);
            subTitle.classList.add('past-race-header');
            subTitle.classList.add('text-center');
            subTitle.innerHTML = race.goal;
            raceContainer.appendChild(subTitle);

            // sort race by old TS to get expected order, then back to place....
            race.results.sort((a, b) => b.oldtrueskill - a.oldtrueskill);
            race.results.forEach( (result, index) => result['expectedFinish'] = index + 1 );
            race.results.sort((a, b) => a.place - b.place);

            const raceTable = document.createElement('table');

            let tableHTML = `
            <thead>
                <tr>
                    <th scope="col">Rank</th>
                    <th scope="col">Name</th>
                    <th scope="col">Expected Finish</th>
                    <th scope="col">Time</th>
                    <th scope="col">SRL Change</th>
                    
                </tr>
            </thead>
            <tbody>
            `;
            race.results.forEach( (result, index) => {
                // format time
                let s = result.time % 60;
                if (s < 10) s = '0' + s;
                let m = Math.floor(result.time / 60 % 60);
                if (m < 10) m = '0' + m;
                let h = Math.floor(result.time / 3600);
                let expectedClass = null;
                if (result.expectedFinish < index + 1) expectedClass = 'negative-change';
                if (result.expectedFinish > index + 1) expectedClass = 'positive-change';
                // if (result.trueskillchange > 0) result.trueskillchange = '+' + result.trueskillchange;
                tableHTML += `
                    <tr scope="row">
                        <td class="press-start">${index + 1}</td>
                        <td class="audiowide">${result.player}</td>
                        <td class="press-start ${expectedClass}">${result.expectedFinish}</td>
                        <td class="press-start blue-highlight">${result.time === -1 ? 'Forfeit' : h + ':' + m + ':' + s}</td>
                        <td class="press-start">${result.oldtrueskill} > ` ;
                        
                if (result.trueskillchange > 0) {
                    tableHTML += `<span class="positive-change"> +${result.trueskillchange}</span>`;
                } else if (result.trueskillchange < 0) {
                    tableHTML += `<span class="negative-change">${result.trueskillchange}</span>`;
                } else {
                    tableHTML += `<span class="">${result.trueskillchange}</span>`;
                }
                tableHTML +=` > ${result.newtrueskill}</td>
                    </tr>
                `;
            });

            tableHTML += `</tbody>`
            raceTable.innerHTML = tableHTML;
            raceTable.classList.add('table', 'table-striped');
            raceContainer.appendChild(raceTable);
        }

        const ctx = document.getElementById('ratingOverTime');
        const ctx2 = document.getElementById('zScoreOverTime');
        const playerName = getPlayerName();
        const ratingData = [];
        const dateLabels = [];
        const zScores = [];
        const raceData = [];
        let totalRaces = null;
        const raceDataElement = document.querySelector('#race-data');
        fetch(`http://api.speedrunslive.com/pastraces?player=${playerName}&game=ff4hacks&page=1&pageSize=20`)
        .then(resp => resp.json())
        .then(resp => {
            totalRaces = resp.count;
            resp.pastraces.forEach(race => {
                addZScore(race);
                raceData.unshift(race);
                race.results.forEach(result => {
                    if (result.player.toLowerCase() === playerName.toLowerCase()) {
                        ratingData.unshift(result.newtrueskill);
                        const d = new Date(0);
                        d.setUTCSeconds(race.date);
                        let dateString = `${d.getMonth()}/${d.getDate()}`;
                        if (race.goal.indexOf('J2KC2T4S3BF2NE3$X2Y2GWZ') >= 0) dateString += ' (LQ)';
                        if (race.goal.indexOf('JK2PCT3S2BF2NE3X2Y2GZ') >= 0) dateString += ' (Ro32)';
                        if (race.goal.indexOf('JK2PC3T3S2BF2NE3X2Y2GZ') >= 0) dateString += ' (Ro16)';
                        if (race.goal.indexOf('Community Race') >= 0) dateString += ' (Comm.)';
                        if (race.goal.indexOf('HTTZ') >= 0) dateString += ' (LEAGUE)';
                        dateLabels.unshift(dateString);
                        zScores.unshift(result.zScore);
                        
                    }
                })
            })
            console.log(resp.pastraces);
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dateLabels,
                    datasets: [{
                        label: 'SRL Points',
                        data: ratingData,
                        backgroundColor: 'rgba(3, 0, 50, 0.4)',
                        borderColor: '#010065'
                    }],
                
                    
                }
            });
            const myChart2 = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: dateLabels,
                    datasets: [{
                        label: 'Z-Score',
                        data: zScores,
                        backgroundColor: 'rgba(50, 0, 3, 0.4)',
                        borderColor: '#650001'
                    }]
                }
            });

            // put together past race bar:
            preparePastRaces(totalRaces);
        });

        
       
    },
    finalize() {
      // JavaScript to be fired on the home page, after the init JS
    },
  };
  