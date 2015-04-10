(function () {//closure of all names in the document

      window.onload = function () {
        //Set all necessary default values & inits
	    enrollment_chart = new OTCChart('async_canvas');
	    enrollment_chart.setStateFromURL();
	    enrollment_chart.parameters.semester = enrollment_chart.parameters.semester || 'summer';
	    enrollment_chart.pushStateToURL();
	    enrollment_chart.initChart();        
	    
	    //Sets semester drop-down value & loads new semester if drop-down is changed
	    $('#semester').val(enrollment_chart.parameters.semester);
	    $('#semester').change(function(){
			enrollment_chart.parameters.semester = $('#semester').val();
			enrollment_chart.pushStateToURL();
		});
        
        //Download button onclick calls saveCanvas()
        $('#download_button').click(function(){saveCanvas()});
        
        //Orderby buttons onclick call orderBy()
        $('#order_by_name').click(function(){orderBy('name')});
        $('#order_by_value').click(function(){orderBy('value')});
	    
        //Load_all onclick loads all departments
        $('#load_all_departments').click({self:enrollment_chart}, function(event){
            self = enrollment_chart;//providing alias to keep with convention
            if (self.parameters.section) delete self.parameters.section
            if (self.parameters.course) delete self.parameters.course
            if (self.parameters.department) delete self.parameters.department
            self.pushStateToURL();
        });
		
      };

      function OTCChart (chart_id) {
        // @class
        var self = this;//closure to pass to anon functions, will always refer to this instance of OTCChart
        this.chart_id = chart_id;
        this.parameters = {'data':'enrollment'};//default is enrollment for all college

	    //@instance methods
        this.setStateFromURL = function () {
            //checks current url for correct hashbang query
            //if found, initializes chart with the parameters from the string
            
            var new_params = {};
            
            if (window.location.href.indexOf('#!') != -1) {
                var query = window.location.href.split('#!')[1];
                var param_strings = query.split('&');
                
                for (var i = 0, j = param_strings.length; i < j; i++) {
                    var tokens = param_strings[i].split('=');
                    
                    if (tokens.length === 2) {
                        var name = tokens[0];
                        var value = tokens[1];
                        
                        if (OTCChart.isValidParameter(name)) {new_params[name] = value}
                    }
                }
            }
            self.parameters = new_params;
        }
        
		//@instance method
        this.pushStateToURL = function () {
            var new_url = [location.protocol, '//', location.host, location.pathname].join('');
            new_url += '#!';
            
            var parameters = self.parameters;
            
            for (var parameter in parameters) {new_url += parameter + '=' + parameters[parameter] + '&'}
            
            new_url = new_url.replace(/\&$/,'');//remove trailing &
            
            //only push url if it's different - prevents unnecessary back button presses
            if (window.location.href !== new_url) {window.location.href = new_url}
        }
       
        //@instance method
        this.requestChartJSON = function (end_func) {
			
			var url = OTCChart.API_HOST + '?data=enrollment';//this should always be set for the enrollment chart
			
          for (param in self.parameters) {//building server-side query from internal parameters
            if(param == "section"){//never build a chart of a single bar
				continue;
			}
			url += '&' + param + '=' + self.parameters[param];
          }
          
            //translate json in to chart.js format
            $.getJSON(url, function(data) {end_func(OTCChart.translateChartData(data), data)});//in this app, the end func is always genChartFromJSON
        }
        
        //@instance method
        this.genChartFromJSON = function (chartData, json) {
            //Start building the chart with the data
            var ctx = document.getElementById(self.chart_id).getContext("2d");
            var newWidth = 0;
            var dataset_label = '';
            var dataset_axis = '';
            
            //Builds the header & axis labels
            if(json.courses) {
                dataset_label = '<span id="canvas_label">';
                if (json.courses[0].course) {
                    dataset_label += json.department_name;
                    dataset_axis = json.department_code + ' Courses';
                } else {
                    dataset_label += 'All Departments';
                    dataset_axis = 'Departments';
                }
                if (json.courses[0].section) {
                    if (json.courses.length == 1) {
                        dataset_label += ': ' + json.courses[0].title + ' (' + json.courses[0].course + '-' + json.courses[0].section + ')';
                        dataset_axis = 'Section of ' + json.department_code + ' ' + json.courses[0].course + '-' + json.courses[0].section;
                    } else {
                        dataset_label += ': ' + json.courses[0].title + ' (' + json.courses[0].course + ')';
                        dataset_axis = 'Sections of ' + json.department_code + ' ' + json.courses[0].course;
                    }
                }
                dataset_label += '</span>';
            }
            $('#canvas_header').html(dataset_label);
            $('#canvas_axis').html(dataset_axis);
            
            for(var i = 0; i < chartData.datasets.length; i++) {
                for(var j = 0; j < chartData.datasets[i].data.length; j++) {newWidth += (5 + 10)}
            }
            newWidth += 20; //Y-axis buffer width
            
            if (newWidth >= ctx.canvas.width) {
              ctx.canvas.width = newWidth;
            }
            ctx.canvas.height = 300;
            
            if (self.myBar) {self.myBar.destroy()}
            self.myBar = new Chart(ctx).StackedBar(chartData, {
                animation: false,
                responsive: false,
                barStrokeWidth: 1,
                multiTooltipTemplate: "<%= datasetLabel %>: <%= value %>",
				legendTemplate : "<dl><% for (var i=0; i<datasets.length; i++){%><dt style=\"background-color:<%=datasets[i].fillColor%>;border:1px solid <%=datasets[i].strokeColor%>\"></dt><dd><%if(datasets[i].label){%><%=datasets[i].label%><%}%></dd><%}%></dl>"
            });
            
            ctx.canvas.onclick = function (evt) {//onclick functionality
                var active_points = self.myBar.getBarsAtEvent(evt);
                self.initChartFromClick(active_points);
            };
	        document.getElementById("canvas_legend").innerHTML = self.myBar.generateLegend();
        }
        
        //@instance method
        this.initChartFromClick = function (active_points) {
            //Use information from internal parameters and the active_points to determine next state
            if (active_points.length < 0) {return} //click was not on a bar
        
            clicked_parameter = (active_points[0]['label'].split('-')[1]) ? (active_points[0]['label'].split('-')[1]) : (active_points[0]['label']);
            
            if (self.parameters.section) {self.parameters.section = clicked_parameter}
            else {
                if (self.parameters.course) {self.parameters.section = clicked_parameter}
                else if (self.parameters.department) {self.parameters.course = clicked_parameter}
                else {self.parameters.department = clicked_parameter} //this is the most general selection that can happen
            }
            self.pushStateToURL();//triggers page refresh, regular init
            self.requestChartJSON(self.genChartFromJSON);
        }
       
        this.generateSectionDetails = function() {
            var url = OTCChart.API_HOST + '?data=all';
            
            //building server-side query from internal parameters
            for (param in self.parameters) {url += '&' + param + '=' + self.parameters[param]}
            
            $.getJSON(url, function (data) {display_section_data(data, "section_info")});
        }

        //@instance method
        this.initChart = function() {
            if(self.parameters.section){self.generateSectionDetails()}
			
            //fixing forward/back button functionality
            $(window).bind('hashchange', function(evt) {window.location.reload()});
            
            //check for hash query and translate it to the internals
            self.setStateFromURL();
	        self.parameters.orderby = self.parameters.orderby || 'department';
            
            //request chart json should refer to the internals to build its query to the backend
            self.requestChartJSON(self.genChartFromJSON);
        }
      }

      //@static constants
      OTCChart.VALID_PARAMETERS = ['DATA','DEPARTMENT','COURSE','SECTION','ORDERBY','SEMESTER'];
      OTCChart.API_HOST = 'http://www.otc.edu/reggraph/api/classpull.php';

      //@static methods of OTCChart
      OTCChart.isValidParameter = function (param_name) {
          param_name = param_name.toUpperCase();
          return (OTCChart.VALID_PARAMETERS.indexOf(param_name) != -1);
      }

      //@static method
      OTCChart.translateChartData = function (json) {
          //take a json object and translate it to the nested array that canvas expects
          
          var headings = [];
          var empty_counts = [];
          var enroll_counts = [];
          
          //Build chartData arrays from json
          if(json.courses) {
              for(var i = 0; i < json.courses.length; i++) {
                  if(json.courses[i].section) {
                      headings[i] = json.courses[i].course + '-' + json.courses[i].section; 
                  } else if(json.courses[i].course) {
                      headings[i] = json.courses[i].course; 
                  } else {
                      headings[i] = json.courses[i].department.substring(0,3);
                  }
                  empty_counts[i] = json.courses[i].empty_seats;
                  enroll_counts[i] = json.courses[i].total_seats - json.courses[i].empty_seats;
              }
          }
          
          //Set chart labels & datasets
          var chartData = {
            labels : headings,
            datasets : [
              {
                label: "Empty Seats",
                fillColor : "rgba(200,200,200,0.4)",
                strokeColor : "rgba(200,200,200,0.8)",
                highlightFill : "rgba(200,200,200,0.75)",
                highlightStroke : "rgba(200,200,200,1)",
                data : empty_counts
              },
              {
                label: "Filled Seats",
                fillColor : "rgba(30,67,145,0.5)",
                strokeColor : "rgba(30,67,145,0.8)",
                highlightFill : "rgba(27,69,166,0.75)",
                highlightStroke : "rgba(27,69,166,1)",
                data : enroll_counts
              }
            ]
          }
          
          return chartData;
      }

	function saveCanvas() {
		var canvas = document.getElementById('async_canvas');
		var dataURL = canvas.toDataURL();
		var button = document.getElementById('download_button');
		var filename = document.getElementById('canvas_label').innerText;
		
        
		if(checkBrowser('ie')) {
			var instructions = "<p>To save this image, right-click on the image and select \"Save picture as\".</p>";
			open().document.write(instructions + '<img src="'+dataURL+'"/>');
		} else {
			button.href = dataURL;
			button.download = filename + '.png';
		}
	}

	function orderBy(col_name) {
		var a_or_d = enrollment_chart.parameters.orderby;

		if(col_name == 'name') {
			if(enrollment_chart.parameters.course) {enrollment_chart.parameters.orderby = 'section'}
			else if(enrollment_chart.parameters.department) {enrollment_chart.parameters.orderby = 'course'}
			else {enrollment_chart.parameters.orderby = 'department'}
		} else if(col_name == 'value') {
			if(a_or_d != 'total_seats_a' && a_or_d != 'total_seats_d') {enrollment_chart.parameters.orderby = 'total_seats_a'}
			else {
				if(a_or_d == 'total_seats_a' || a_or_d == 'total_seats') {enrollment_chart.parameters.orderby = 'total_seats_d'}
				else {enrollment_chart.parameters.orderby = 'total_seats_a'}
			}
		}
		enrollment_chart.pushStateToURL();
	}
	
	function display_section_data(data, divName) {
		var sec_data = data;
		var section_data = "";
		section_data += "<dl>";
		for (var prop in sec_data['courses'][0]) {
			if(sec_data['courses'][0].hasOwnProperty(prop)) {
				if(prop != "id" && prop != "synonym" && prop != "note" && prop != "row_type") {
					section_data += "<dt>" + prop.charAt(0).toUpperCase() + prop.slice(1);
					section_data +=  ": </dt><dd>";
					if(prop == "start" || prop == "end") {section_data += convert_time((sec_data['courses'][0][prop]))}
					else {section_data += sec_data['courses'][0][prop]}
					section_data += "</dd>";
				}
			}
		}
		section_data += "</dl>";
		$('#' + divName).html(section_data);
	}
	
	/* Function to convert time to human readable format */
	function convert_time(val) {
		var date = new Date((val*1000));
        
		// hours part from the timestamp
		var hours = date.getHours();
        
		// minutes part from the timestamp
		var minutes = "0" + date.getMinutes();
        
		// will display time in 10:30 a.m./p.m. format
		if(hours>12) {
			hours = (hours-12);
			var formattedTime = hours + ':' + minutes.substr(minutes.length-2) + " p.m.";
		} else {
			var formattedTime = hours + ':' + minutes.substr(minutes.length-2) + " a.m.";
		}
		return formattedTime;
	}
    
	/* Browser checking function */
	function checkBrowser(browser) {
		switch(browser)
		{
			case 'opera':
				// Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
				var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
				return isOpera;
			case 'firefox':
				// Firefox 1.0+
				var isFirefox = typeof InstallTrigger !== 'undefined';
				return isFirefox;
			case 'safari':
				// At least Safari 3+: "[object HTMLElementConstructor]"
				var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
				return isSafari;
			case 'chrome':
				// Chrome 1+
				var isChrome = !!window.chrome && !isOpera;
				return isChrome;
			case 'ie':
				// At least IE6
				var isIE = /*@cc_on!@*/false || !!document.documentMode;
				return isIE;
		}
	}

//end closure
})()