<script>
import VueCharts from 'vue-chartjs'
import { Bar } from 'vue-chartjs'

export default {
  extends: Bar,
  created () {
    this.displayData();
  },
  data () {
    return {
      datacollection: {
        labels: [],
        datasets: [
          {
            type: 'bar',
            label: 'kWh',
            backgroundColor: 'rgba(254,95,0,0.8)',
            data: []
          },
          {
            type: 'line',
            label: 'kWh',
            lineTension: 0,
            fill: false,
            borderColor: '#A14705',
            backgroundColor: '#A14705',
            data: []
          },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: false,
      },
    }
  },
  methods: {
      //Function Name: displayData
      //Function Description: Retrieve all modbus data for monthly consumption
      displayData(){
          axios({
          url: 'modbus-data/monthlyConsumption',
          method: 'post',
          data: {}
          })
          .then((response)=>
            this.fillData(response.data),

          )
          .catch((error) => console.log(error));
      },
      //Function Name: fullData
      //Function Description: Parses data to display in vue chart
      //Params: data from displayData()
      fillData (data) {
        for(var i=0; i<data.length; i++){
          this.datacollection.labels.push(data[i].months);
          this.datacollection.datasets[0].data.push(data[i].floor_data);
          this.datacollection.datasets[1].data.push(data[i].floor_data);
        }
      this.renderChart(this.datacollection, this.options);

      },
  },
}
</script>