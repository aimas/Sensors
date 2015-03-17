<?php

$hostname = 'localhost';
$username = 'xxxxxx';
$password = 'xxxxxx';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=xxxxxxx",
                               $username, $password);

    /*** The SQL SELECT statement ***/
    $sth = $dbh->prepare("SELECT  `dtg`, `atm_press`, `sea_press`, `altitude` FROM  `env_measures` ORDER BY `dtg` DESC LIMIT 0,2016");
    $sth->execute();

    /* Fetch all of the remaining rows in the result set */
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);

    /*** close the database connection ***/
    $dbh = null;
    
}
catch(PDOException $e)
    {
        echo $e->getMessage();
    }

$json_data = json_encode($result); 

?>

<!DOCTYPE html>
<meta charset="utf-8">
<style> /* set the CSS */

body { font: 12px Arial; background-color: lightblue}
#centerbuttons {width:350px;margin:0 auto;}
#headerTitle {
	text-align: center;
	.bold {
		font-weight:bold;
		}
	.underline{text-decoration: underline;
	}	
}
path {
stroke: steelblue;
stroke-width: 2;
fill: none;
}
.axis path,
.axis line {
fill: none;
stroke: grey;
stroke-width: 1;
shape-rendering: crispEdges;
}
</style>

<body>

<div id=headerTitle>
<H2>External Measures - Atmospheric Pressure - with PI</H2></div>

<!-- load the d3.js library -->    
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>

// Set the dimensions of the canvas / graph with every update the graph resizes based on the size of the windows
var margin = {top: 30, right: 60, bottom: 30, left: 50},
    width = document.documentElement.clientWidth*0.80 - margin.left - margin.right,
    height = document.documentElement.clientHeight*0.75 - margin.top - margin.bottom;

// Parse the date / time
var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;
var formatTime = d3.time.format("%Y-%m-%d %H:%M:%S");
// Set the ranges
var x = d3.time.scale().range([0, width]);
var y0 = d3.scale.linear().range([height, 0]);
var y1 = d3.scale.linear().range([height, 0]);

// Define the axes
var xAxis = d3.svg.axis().scale(x)
    .orient("bottom");

var yAxisLeft = d3.svg.axis().scale(y0)
    .orient("left").ticks(25);
var yAxisRight = d3.svg.axis().scale(y1)
    .orient("right").ticks(25);
// Define the line
var valueline = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.dtg); })
    .y(function(d) { return y0(d.atm_press); });

var valueline2 = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.dtg); })
    .y(function(d) { return y0(d.sea_press); })
        
var valueline3 = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.dtg); })
    .y(function(d) { return y1(d.altitude); })
//    .interpolate("basis");

    
// Adds the svg canvas
var svg = d3.select("body")
    .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
    .append("g")
        .attr("transform", 
              "translate(" + margin.left + "," + margin.top + ")");

// Get the data
<?php echo "data=".$json_data.";" ?>
data.forEach(function(d) {
	d.dtg = parseDate(d.dtg);
	d.atm_press = +d.atm_press;
	d.sea_press = +d.sea_press;
	d.altitude = +d.altitude;
});

// Scale the range of the data
x.domain(d3.extent(data, function(d) { return d.dtg; }));
//y.domain([200, d3.max(data, function(d) { return d.atm_press+100; })]);
y0.domain([d3.min(data, function(d) {return d.atm_press-50;}), d3.max(data, function(d) { return d.atm_press+50; })]);
y1.domain([d3.min(data, function(d) {return d.altitude-50;}), d3.max(data, function(d) { return d.altitude+50; })]);
// Add the valueline path.
svg.append("path")
	.style("stroke", "black")
	.attr("d", valueline(data));
svg.append("path")
	.attr("class", "line")
	.style("stroke-dasharray", ("4, 5, 4, 5"))
	.style("stroke", "yellow")
	.attr("d", valueline2(data));
svg.append("path")
	.style("stroke", "red")
	.attr("d", valueline3(data));	

// Add the X Axis
svg.append("g")
	.attr("class", "x axis")
	.attr("transform", "translate(0," + height + ")")
	.call(xAxis);
svg.append("text")
        .attr("x", width/2)
        .attr("y", height+margin.bottom)
        .style("text-anchor", "middle")
        .text("Date");
// Add the Y Axis'
svg.append("g")
	.attr("class", "y axis")
	.call(yAxisLeft);
svg.append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 0 - margin.left)
        .attr("x", 0 - (height/2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("hPA");	
svg.append("g")
	.attr("class", "y axis")
	.attr("transform", "translate(" + width + " ,0)")
	.style("fill", "red")
	.call(yAxisRight);

//automates the refresh by triggering a reload to ensure SQL picks up the latest values
var inter = setInterval(function() {
	updateData();
}, 60000);   

function updateData(){
  window.location.reload();
  
}         
</script>

//buttons for manual refresh and closing of window
<div id=centerbuttons>
<p>Black Line = Atmospheric Pressure in hPa <br> Yellow = Pressure at Sea Level <br> Red = calculated Altitude in m</p>
<button onclick="updateData()">Update Data</button>
<button onclick="clsWindow()" draggable="true">Close Window</button>
</div>
<script>
function clsWindow() {
    window.close();
}
</script>
</body>
