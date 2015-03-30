
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="revisit-after" content="1 day">
<?php
// get current url
$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
if ($_SERVER["SERVER_PORT"] != "80")
{
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
} 
else 
{
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}

$pageTitle = "Real-time Registration Graph";
$pageDescription = "View OTC Course Registration in real time. Generate graphs interactively.";

// Getting available semesters from database
$semesters_avail = "";
$dbS = new PDO('mysql:host=db1.otc.edu;dbname=schedulesearch;charset=utf8', 'web3', 'BvCgWHyq');
$dbS->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlS = "SELECT DISTINCT semester FROM class";
$stmtS = $dbS->prepare($sqlS);
$stmtS->execute();
$semesters = $stmtS->fetchAll(PDO::FETCH_ASSOC);
foreach($semesters as $key=>$value)
{
	if($value['semester'] && $value['semester'] != "")
	{
			$semesters_avail .= '<option value="'.strtolower($value['semester']).'">'.$value['semester'].'</option>';
	}
}
?>
    <!-- open graph meta properties -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $pageURL; ?>" />
    <meta property="og:image" content="http://www.otc.edu/reggraph/graph-Art-And-Experience_optimized.png" />
    <meta property="og:title" content="<?php echo $pageTitle; ?>" />
    <meta property="og:description" content="<?php echo $pageDescription; ?>" />
    <title>Real-time Registration Graph</title>
    <link rel="stylesheet" href="/base.css" type="text/css" media="screen" charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="enrollment_graph.css" />
    <script src="https://www.otc.edu/JSSCRIPTS/jquery/jquery-1.6.2.min.js"></script>
    <script src="https://www.otc.edu/JSSCRIPTS/jquery/plugins/json/json.js"></script>
	<script type="text/javascript" src="https://www.otc.edu/JSSCRIPTS/jquery/jquery.extensions.js"></script>
	<script> var $ = jQuery.noConflict();</script>
	<script src="OTC.Chart.js"></script>
    <script src="OTC.StackedBar.js"></script>
    <script src="enrollment_graph.js"></script>
	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=onload" async defer></script>
	
	
</head>
<body>
  
<!-- FACEBOOK ROOT -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
    
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
            <li class="social"><a href="http://www.instagram.com/ozarkstech"><img src="/structuralimages/Instagram_16.png" border="0" alt="OTC Instagram" /></a></li>
            <li class="social"><a href="http://twitter.com/otcedu"><img src="/structuralimages/twitter_16.png" border="0" alt="OTC Twitter" /></a></li>
            <li class="social"><a href="http://www.youtube.com/user/OTCvids"><img src="/structuralimages/youtube_16.png" border="0" alt="OTC YouTube" /></a></li>
            <li class="social"><a href="/news/rss.php"><img src="/structuralimages/rss.png" border="0" alt="OTC RSS Feeds" /></a></li>
        </ul>
    </div>
    <div id="header_search">
        <form name="gs" method="get" action="https://search.otc.edu/search">
            <div class="search_box">
                <input class="textbox" type="text" name="q" size="50" maxlength="256" value="" id="site_search" />
                <input class="button" type="submit" name="btnG" value="Search" />
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
        <div id="content" class="full wrapper">
            <div id="content_body" class="full_page boxes wrapper">
                <div id="content_body_inner" class="wrapper">
                    <h2>Real-time Registration Graph</h2>
                    <div id="content_body_inner_content" class="wrapper">
                      <div id="canvas_header">
                        <noscript>
                          Sorry, this application requires javascript. Please <a href="http://support.microsoft.com/gp/howtoscript" target="_blank">enable javascript</a> to continue.
                        </noscript>
                      </div>
					  <div class="clearfix"></div>
                      <div id="canvas_wrapper">
                        <!--[if lt IE 9]>
                          Sorry, this application requires <a href="http://www.w3schools.com/canvas/default.asp" target="_blank">canvas support</a>.
                        <![endif]-->
                        <canvas id="async_canvas"></canvas>
                      </div>
                      <div id="canvas_axis"></div>
                      <div id="canvas_notice"><p><em>Click a bar to get more information</em></p></div>
                      <div id="canvas_legend"></div>
                      <div id="canvas_button">
                        <label for="semester">Semester: </label>
                        <select id="semester" name="semester">
                          <?php print($semesters_avail) ?>
                        </select>
                        <a class="button" href="#" id="download_button"><input type="button" value="Download .png Copy"></button></a>
                        <input type="button" id="load_all_departments" value="Load All Departments">
                        <input type="button" id="order_by_name" value="Order By Name">
                        <input type="button" id="order_by_value" value="Order By Seats">
                      </div>
		              <div class="clearfix"></div>
                      <div id="section_info" class="ModularBlock"></div>
                      <div id="feedback_wrapper" class="ModularBlock">
                        <p>Was this page helpful? Tell us about it.</p>
                        <form id="feedback_form" name="form" action="backend/canvas_feedback.php" method="post">
                          <textarea id="feedback_text" name="text" placeholder="Type your feedback here..."></textarea>
                          <div class="g-recaptcha" data-sitekey="6LcUAAQTAAAAAGiB8IWVo4D2OsSmX2eHUy461r63"></div>
						  <input type="button" id="feedback_submit" value="Submit Feedback"/>
                        </form>
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