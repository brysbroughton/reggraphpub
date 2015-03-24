<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="revisit-after" content="1 day">


    <title>Canvas API Documentation</title>


                <link rel="stylesheet" href="/base.css" type="text/css" media="screen" charset="utf-8" />
                
                <link rel="stylesheet" href="/print.css" type="text/css" media="print" charset="utf-8" />
                

    
    <script type="text/javascript" src=""></script>
    
    <style type="text/css">
        .api_code, #parameter_list dt{color:#AAAAAA;font-family:monospace, sans-serif;}
        #parameter_list dd{margin-bottom:15px;}
    </style>
    
</head>
<body>
    
<div id="page" class="wrapper">
    <div id="header" class="wrapper">
        <div id="header_inner" class="wrapper">
            <h1><a href="

/
"><span>Ozarks Technical&nbsp;<br />Community College</span></a></h1>
<div id="header_links">
    <div id="quick_menu" class="wrapper">
        <ul class="tabs">
            <li id="info_link"><a href="http://my.otc.edu" title="MyOTC">my otc</a></li>
            <li><a href="/currentstudents/library.php" title="OTC Library">library</a></li>
            <li><a href="/schedules/schedules.php" title="OTC Class Schedules">schedules</a></li>
            <li><a href="/facultystaff.php" title="OTC Department and Employee Directories">directory</a></li>
            <li class="social"><a href="http://www.facebook.com/pages/Springfield-MO/Ozarks-Technical-Community-College/30169686052"><img src="/structuralimages/facebook_16.png" border="0" alt="OTC Facebook" /></a></li>
            <!--<li class="social"><a href="http://www.myspace.com/otcspace"><img src="/structuralimages/myspace_16.png" border="0" alt="OTC MySpace" /></a></li>-->
            <li class="social"><a href="http://twitter.com/otcedu"><img src="/structuralimages/twitter_16.png" border="0" alt="OTC Twitter" /></a></li>
            <li class="social"><a href="http://www.youtube.com/user/OTCvids"><img src="/structuralimages/youtube_16.png" border="0" alt="OTC YouTube" /></a></li>
            <li class="social"><a href="/news/rss.php"><img src="/structuralimages/rss.png" border="0" alt="OTC RSS Feeds" /></a></li>
        </ul>
    </div>
    <div id="header_search">
        <form name="gs" method="get" action="https://search.otc.edu/search">
            <div class="search_box">
                <input class="textbox" type="text" name="q" size="50" maxlength="256" 



        value="" 
      

                 id="site_search" />
                <input class="button" type="submit" name="btnG" 



value="Search" />
            </div>
            <input type="hidden" name="entqr" value="0" />
            <input type="hidden" name="ud" value="1" />
            <input type="hidden" name="sort" value="date:D:L:d1" />
            <input type="hidden" name="output" value="xml_no_dtd" />
            <input type="hidden" name="oe" value="UTF-8" />
            <input type="hidden" name="ie" value="UTF-8" />
            <input type="hidden" name="client" value="default_frontend" />
            <input type="hidden" name="proxystylesheet" value="default_frontend" />
            <input type="hidden" name="site" value="default_collection" />
        </form>
    </div>
</div>
        </div>
    </div>
    <div id="body" class="wrapper">
        <div id="content" class="wide wrapper">
            <div id="content_sidebar" class="wrapper">
                <ul id="main_menu">
                    <li><a href="/starthere/starthere.php">Start Here</a></li><li><a href="/admissions/admissions.php">Admissions</a></li><li><a href="/workforce/workforce.php">Workforce Development</a></li><li><a href="/foundation/foundation.php">OTC Foundation</a></li><li><a href="/news/news.php">News & Information</a></li><li><a href="/currentstudents/currentstudents.php">Current Students</a></li><li><a href="/programs/programs.php">Programs</a></li><li><a href="/financialaid/financialaid.php">Financial Aid</a></li><li><a href="/about/about.php">About OTC</a></li><li><a href="/employment/employment.php">Employment</a></li><li><a href="/online/online.php">OTC Online</a></li><li><a href="/locations/locations.php">Campuses / Locations</a></li><li><a href="/administration/administration.php">Administration</a></li>
                </ul>
                
            </div>
            <div id="content_body" class="center_image_page boxes wrapper">
                <h2>Canvas API Documentation</h2>
                <div id="content_body_inner" class="wrapper">
                    <p>Project currently located at <a href="http://webdev.otc.edu/canvas">webdev.otc.edu/canvas</a>.</p>
                    <h3>Overview</h3>
                    <p>The canvas API provides an interface to the OTC class information/enrollment database. By making AJAX calls with specific query strings to the file at [webdev.otc.edu/canvas/backend/classpull.php - may change], A javascript application may generate interactive displays of the class/enrollment information.</p>
                    <p>This page lists the supported query parameters, and the data that will be returned for each.</p>
                    <h3>The parameters</h3>
                    <div id="parameter_list">
                        <dl>
                            <dt>data</dt>
                            <dd>Specifies the type of data that should be returned</dd>
                            <dd><p><strong>values:</strong></p>
                                <dl>
                                    <dt>enrollment</dt>
                                    <dd>Request will return <span class="api_code">total_seats</span> and <span class="api_code">empty_seats</span></dd>
                                </dl>
                            </dd>
                            <dd>*Any value other than <span class="api_code">enrollment</span> will return all information.</dd>
                            <dt>department</dt>
                            <dd>Narrows the data selection to the 3 letter department code</dd>
                            <dd>
                                <p><strong>Available department codes:</strong></p>
                                <dl>
                                    <dt>ABR</dt><dd>Auto Collision Repair Technology</dd>
                                    <dt>ACC</dt><dd>Accounting</dd>
                                    <dt>AGR</dt><dd>Agriculture</dd>
                                    <dt>ANT</dt><dd>Anthropology</dd>
                                    <dt>ARB</dt><dd>Arabic</dd>
                                    <dt>ART</dt><dd>Art</dd>
                                    <dt>ASL</dt><dd>American Sign Language</dd>
                                    <dt>ASN</dt><dd>Associate of Science in Nursing</dd>
                                    <dt>ATS</dt><dd>Applied Technical Science</dd>
                                    <dt>AUM</dt><dd>Automotive Technology</dd>
                                    <dt>BCS</dt><dd>Biological Clinical Science</dd>
                                    <dt>BIO</dt><dd>Biology</dd>
                                    <dt>BUS</dt><dd>Business and Marketing</dd>
                                    <dt>CAC</dt><dd>College and Careers</dd>
                                    <dt>CHM</dt><dd>Chemistry</dd>
                                    <dt>CHN</dt><dd>Chinese</dd>
                                    <dt>CIS</dt><dd>Computer Information Science</dd>
                                    <dt>COM</dt><dd>Communication</dd>
                                    <dt>CRM</dt><dd>Criminology</dd>
                                    <dt>CST</dt><dd>Construction Technology</dd>
                                    <dt>CUL</dt><dd>Culinary Arts</dd>
                                    <dt>DAS</dt><dd>Dental Assisting-Traditional Track</dd>
                                    <dt>DDT</dt><dd>Drafting and Design Technology</dd>
                                    <dt>DHY</dt><dd>Dental Hygiene</dd>
                                    <dt>DSL</dt><dd>Diesel Technology</dd>
                                    <dt>ECD</dt><dd>Early Childhood Development</dd>
                                    <dt>ECO</dt><dd>Economics</dd>
                                    <dt>EDU</dt><dd>Education</dd>
                                    <dt>EGR</dt><dd>Engineering</dd>
                                    <dt>ELC</dt><dd>Electrical</dd>
                                </dl>
                            </dd>
                            <dt>course</dt>
                            <dd>Only valid when <span class="api_code">department</span> has been specified. Narrows results to course within a department. A course code could be any 3-digit number.</dd>
                            <dt>section</dt>
                            <dd>Only valid when both <span class="api_code">department</span> and <span class="api_code">course</span> have been specified. Narrows the results to the specified section within a course. A section name may be any combination of letters and numbers in length 3 - 10 characters.</dd>
                            <dt>orderby</dt>
                            <dd>Specifies how the returned data rows should be ordered</dd>
                            <dd><p><strong>values:</strong></p>
                                <dl>
                                    <dt>alpha</dt>
                                    <dd>Returned data rows will be ordered by their grouping label. e.g. if the request is to pull all courses of a department, rows are ordered by department code. If request is to pull all sections of a course, rows are ordered by section code.</dd>
                                    <dt>value</dt>
                                    <dd>Returned data rows will be ordered by the <span class="api_code">total_seats</span> column.</dd>
                                </dl>
                            </dd>
							<dt>semester</dt>
							<dd>Selects the semester to return information for.</dd>
							<dd><p><strong>values:</strong></p>
								<dl>
									<dt>possible values</dt>
									<dd>The select list is populated from the database so that only available semesters appear for selection. The possible semesters should only be Fall, Spring, and Summer.</dd>
									
								</dl>
							</dd>
                        </dl>
                    </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div id="content_footer" class="wrapper">
    
    <p>
        <?php echo  $linkToMobile; ?>
        &copy; <?php echo date(Y); ?> Ozarks Technical Community College, 1001 E. Chestnut Expressway, Springfield, MO 65802&nbsp;&bull;&nbsp;
        (417) 447-7500<br  />
        <a href="/hr/1193.php">EO/AA</a>&nbsp;&bull;&nbsp;
        <a href="/webservices/accessibility.php">Accessibility</a>&nbsp;&bull;&nbsp;
        <a href="/tobaccofree/tobaccofree.php">A Tobacco Free Campus</a>&nbsp;&bull;&nbsp;
        <a href="/webservices/disclaimer.php">Disclaimer</a>
    </p>
    <div id="content_footer_bottom" style="width: 150px; margin: 0 auto; text-align: center;display:none">
        <script language="JavaScript" src="https://chat.otc.edu/phplive/js/status_image.php?base_url=https://chat.otc.edu/phplive&amp;l=phplive&amp;x=1&deptid=0&amp;"></script>
    </div>
    <!-- BEGIN PHP Live! code, (c) OSI Codes Inc. --><span id="phplive_btn_1382390438" onclick="phplive_launch_chat_0(0)" style="color: #0000FF; text-decoration: underline; cursor: pointer;"></span><script type="text/javascript">(function() { var phplive_e_1382390438 = document.createElement("script") ; phplive_e_1382390438.type = "text/javascript" ; phplive_e_1382390438.async = true ; phplive_e_1382390438.src = "//chat.otc.edu/phplive/js/phplive_v2.js.php?q=0|1382390438|0|" ; document.getElementById("phplive_btn_1382390438").appendChild( phplive_e_1382390438 ) ; })() ;</script><!-- END PHP Live! code, (c) OSI Codes Inc. -->
 </div>
    </div>
</div>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  
  ga('create', 'UA-2350068-2', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
</body>
</html>
