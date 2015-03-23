    

      window.onload = function () {
	    enrollment_chart = new OTCChart('async_canvas');
	    enrollment_chart.setStateFromURL();
	    enrollment_chart.initChart();
	  
	    $('#semester').val(enrollment_chart.parameters.semester);
	    $('#semester').bind('change', function(){
			    enrollment_chart.parameters.semester = $('#semester').val();
			    enrollment_chart.pushStateToURL();
		    });
	  
        };
        
      function OTCChart (chart_id) {
      // @class
      // instantiate using keyword 'new'
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
              //code
              var tokens = param_strings[i].split('=');
              if (tokens.length === 2) {
                var name = tokens[0];
                var value = tokens[1];
                if (OTCChart.isValidParameter(name)) {
                  new_params[name] = value;
                }
              }
            }
          }
          
          self.parameters = new_params;
          
        }
        
		
        this.pushStateToURL = function () {

          var new_url = [location.protocol, '//', location.host, location.pathname].join('');

          new_url += '#!';
          var parameters = self.parameters;

          for (var parameter in parameters) {
			  
            new_url += parameter + '=' + parameters[parameter] + '&';
			
          }
          new_url = new_url.replace(/\&$/,'');//remove trailing &

          if (window.location.href !== new_url) {
            //only push url if it's different - prevents unnecessary back button presses
            window.location.href = new_url;
          }
          
        }
       
        //@instance method
        this.requestChartJSON = function (end_func) {
			
			  var url = OTCChart.API_HOST + '?data=enrollment';//this should always be set for the enrollment chart
			
          
          
          for (param in self.parameters) {//building server-side query from internal parameters
            if(param == "section"){
				continue;
			}
			url += '&' + param + '=' + self.parameters[param];
          }
          
            $.getJSON(
                url,
                function (data) {
                    end_func(OTCChart.translateChartData(data));//translate json in to chart.js format
                }
            );
            
        }
        
        //@instance method
        this.genChartFromJSON = function (chartData) {
            //Start building the chart with the data
            var ctx = document.getElementById(self.chart_id).getContext("2d");
            var newWidth = 0;
            
            for(var i = 0; i < chartData.datasets.length; i++) {
                for(var j = 0; j < chartData.datasets[i].data.length; j++) {
                    newWidth += (5 + 10);
                }
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
	    if (active_points.length < 0) {
		//click was not on a bar
		return;
	    }
	
	    clicked_parameter = (active_points[0]['label'].split('-')[1]) ? (active_points[0]['label'].split('-')[1]) : (active_points[0]['label']);
	    
	    if (self.parameters.section) {
		self.parameters.section = clicked_parameter;
	    } else {
		if (self.parameters.course) {
		    self.parameters.section = clicked_parameter;
		} else if (self.parameters.department) {
		    self.parameters.course = clicked_parameter;
		} else {
		    self.parameters.department = clicked_parameter; //this is the most general selection that can happen
		}
	    }
	    //self.parameters.orderby = setOrderBy(self); //calling setOrderBy() here overrides any current orderby setting <Louis>
	    self.pushStateToURL();
	    self.requestChartJSON(self.genChartFromJSON);
        }
       
	this.generateSectionDetails = function() {
	    var url = OTCChart.API_HOST + '?data=all';
			
	    for (param in self.parameters) {//building server-side query from internal parameters
		url += '&' + param + '=' + self.parameters[param];
	    }
            $.getJSON(
                url,
                function (data) {
		    display_section_data(data, "section_info");
                }
            );
	}
	   
        //@instance method
        this.initChart = function() {
            
            if(self.parameters.section){
		self.generateSectionDetails();
	    }
			
            //fixing forward/back button functionality
            $(window).bind('hashchange', function(evt) {
			window.location.reload();
            });
            
            //check for hash query and translate it to the internals
            self.setStateFromURL();
	    self.parameters.orderby = self.parameters.orderby || 'department';
            //request chart json should refer to the internals to build its query to the backend
            
            self.requestChartJSON(self.genChartFromJSON);
			
        }
        
      }
      
      //@static constants
      OTCChart.VALID_PARAMETERS = ['DATA','DEPARTMENT','COURSE','SECTION','ORDERBY','SEMESTER'];
      OTCChart.API_HOST = 'http://webdev.otc.edu/canvas/backend/classpull.php';
      
      //@static methods of OTCChart
      OTCChart.isValidParameter = function (param_name) {
          //takes string
          //returns boolean
          param_name = param_name.toUpperCase();
          return (OTCChart.VALID_PARAMETERS.indexOf(param_name) != -1);
        }
        
      //@static method
      OTCChart.translateChartData = function (json) {
        //take a json object and translate it to the nested array that canvas expects
			if(enrollment_chart.parameters.data == "all"){
			  enrollment_chart.parameters.sectionInfo = json;
			}
			
            var headings = [];
            var empty_counts = [];
            var enroll_counts = [];
            var dataset_label = '';
            var dataset_axis = '';

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
            //Added to create a label under the chart showing what the displayed information is relevent to
			if(json.courses)
			{
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
				label: "Currently Enrolled Students",
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


	function saveCanvas()
	{
		var canvas = document.getElementById('async_canvas');
		var dataURL = canvas.toDataURL();
		var button = document.getElementById('btn-download');
		var filename = document.getElementById('canvas_label').innerText;
		
		var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
			// Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
		var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
		var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
			// At least Safari 3+: "[object HTMLElementConstructor]"
		var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
		var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6
		
		
		if(isIE)
		{
			var instructions = "<p>To save this image, right-click on the image and select \"Save picture as\".</p>";
			open().document.write(instructions + '<img src="'+dataURL+'"/>');
			return false;
		}
		else
		{
			button.href = dataURL;
			button.download = filename + '.png';
		}
	}
	
	function orderBy(col_name)
	{
		var a_or_d = enrollment_chart.parameters.orderby;
		//delete enrollment_chart.parameters.orderby;
		if(col_name == "value")
		{
			if(enrollment_chart.parameters.course)
			{
				enrollment_chart.parameters.orderby = 'section';
			}
			else if(enrollment_chart.parameters.department)
			{
				enrollment_chart.parameters.orderby = 'course';
			}
			else
			{
				enrollment_chart.parameters.orderby = 'department';
			}
		}
		else if(col_name == 'chart_val')
		{
			if(a_or_d != 'total_seats_a' && a_or_d != 'total_seats_d')
			{
				enrollment_chart.parameters.orderby = 'total_seats_a';
			}
			else
			{
				if(a_or_d == 'total_seats_a' || a_or_d == 'total_seats')
				{
					enrollment_chart.parameters.orderby = 'total_seats_d';
				}
				else
				{
					enrollment_chart.parameters.orderby = 'total_seats_a';
				}
			}
		}

		enrollment_chart.pushStateToURL();
	}
	
	function display_section_data(data, divName)
	{
		var sec_data = data;
		var section_data = "";
		section_data += "<dl>";
		for (var prop in sec_data['courses'][0])
		{
			if(sec_data['courses'][0].hasOwnProperty(prop))
			{
				if(prop != "id" && prop != "synonym" && prop != "note" && prop != "row_type")
				{
					section_data += "<dt>" + prop.charAt(0).toUpperCase() + prop.slice(1);
					section_data +=  ": </dt>";
				}
				
				
				if(prop != "id" && prop != "synonym" && prop != "note" && prop != "row_type")
				{
					section_data += "<dd>";
					if(prop == "start" || prop == "end")
					{
						section_data += convert_time((sec_data['courses'][0][prop]));
					}
					else
					{
						section_data += sec_data['courses'][0][prop];
					}
					section_data += "</dd>";
				}
			}
		}
		section_data += "</dl>";
		$('#' + divName).html(section_data);
	}
	
	function convert_time(val)
	{
		var date = new Date((val*1000));
		// hours part from the timestamp
		var hours = date.getHours();
		// minutes part from the timestamp
		var minutes = "0" + date.getMinutes();
		// will display time in 10:30 a.m./p.m. format
		if(hours>12)
		{
			hours = (hours-12);
			var formattedTime = hours + ':' + minutes.substr(minutes.length-2) + " p.m.";
		}
		else
		{
			var formattedTime = hours + ':' + minutes.substr(minutes.length-2) + " a.m.";
		}
		return formattedTime;
	}
    
    /* FEEDBACK FORM */
    function mailForm(text)
    {
      var form = 'mailto:web@otc.edu?subject=Real-Time%20Enrollment%20Graph%20Feedback&body=' + text + '%0D%0A-Anonymous';
      
      window.location.href = form;
    }