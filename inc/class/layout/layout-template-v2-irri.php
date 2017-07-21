<?php $tpldir = 'layout-v2-irri'; // local='layout-v2-irri' irri='http://irri.org/templates' ?>
<!DOCTYPE HTML>
<html lang="en-gb" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link href="favicon.ico" rel="icon" type="image/x-icon" />

        <title>IRRI &raquo; Tools and Database &raquo; WeRise - Weather-rice-nutrient integrated decision support system</title>

        <script type="text/javascript" src="js/jquery/jquery-1.12.0.min.js"></script>
        <script type="text/javascript">
            jQuery.noConflict();
        </script>

        <?php if ($pageaction==='weather' || $pageaction==='oryza'): ?>
        <link type="text/css" rel="stylesheet" href="css/jquery/jquery.tidy.table.min.css" />
        <script type="text/javascript" src="js/jquery/jquery.tidy.table.min.js"></script>
        <?php endif; ?>        

        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap-formhelpers.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap-formhelpers-countries.flags.css" />
        <script type="text/javascript" src="js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-selectbox.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-countries.en_US.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-countries.js"></script>

        <script type="text/javascript" src="gzip.php?group=common"></script>
        <script type="text/javascript">
            window.langdata = <?php echo json_encode(language::getInstance()->jstranslate()); ?>;
            weriseApp.recentSelect = <?php echo json_encode(dss_utils::getLastSelectValues()); ?>;
            window.dataselect = weriseApp.recentSelect;
            window.show_googlemaps = <?php echo ((_opt(sysoptions::_SHOW_MAP)) ? 'true' : 'false') ?>;
            window.is_logged = <?php echo (dss_auth::checkAccess2() ? 'true' : 'false') ?>;
            window._env = '<?php echo _ADM_ENV ?>';
        </script>

        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/base.css" />
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/layout.css" />
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/menus.css" />
        <!--link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/modules.css" /-->
        <!--link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/tools.css" /-->
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/system.css" />
        <!--link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/extensions.css" /-->
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/custom.css" />
        <!--link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/animation.css" /-->
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/style.css" />
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/css/print.css" />

        <!--script src="<?php echo $tpldir?>/yoo_infinite/warp/js/warp.js"></script-->
        <script src="<?php echo $tpldir?>/yoo_infinite/warp/js/responsive.js"></script>
        <!--script src="<?php echo $tpldir?>/yoo_infinite/js/template.js"></script-->

        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/fonts/opensans.css" />
        <link rel="stylesheet" href="<?php echo $tpldir?>/yoo_infinite/fonts/opensanslight.css" />
        <link rel="stylesheet" type="text/css" href="layout-v2-irri/css/irrifont.css" />
        <link rel="stylesheet" type="text/css" href="layout-v2-irri/css/base.css" />
        <link rel="stylesheet" type="text/css" href="layout-v2-irri/css/print.css" />

		<?php if (_ADM_ENV==='PROD' && _opt(sysoptions::_OPT_GOOGLE_ANALYTICS)) : ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-70313853-1', 'auto');
  ga('send', 'pageview');

</script>
		<?php endif; ?>
		
    </head>

    <body id="page" class="page body-dark">

        <div id="block-header">
            <div class="block-header wrapper ">
                <header id="header" class="clearfix">
                    <a id="logo" href="http://irri.org"><img style="max-height:none" src="<?php echo $tpldir?>/images/IRRI-logo.png" alt="logo" width="100" height="80" /></a>
                    <div id="menubar">
                        <nav id="menu">
                            <ul class="menu menu-dropdown">
                                <li class="level1"><span class="separator level1 parent"><span class="dssmenu"><a href="index.php">WeRise</a></span></span></li>
                                <li class="level1"><span class="separator level1 parent"><span class="dssmenu"><a href="index.php?pageaction=about"><?php __('About') ?> WeRise</a></span></span></li>
                                <li class="level1"><span class="separator level1 parent"><span class="dssmenu"><a href="index.php?pageaction=weather"><?php echo strtoupper(_t('Weather Advisory')) ?></a></span></span></li>
                                <li class="level1"><span class="separator level1 parent"><span class="dssmenu"><a href="index.php?pageaction=oryza"><?php echo strtoupper(_t('Crop Advisory')) ?></a></span></span></li>
                                <li class="level1"><span class="separator level1 parent"><span class="dssmenu"><a href="index.php?pageaction=terms"><?php echo strtoupper(_t('Terms and Conditions')) ?></a></span></span></li>
                            </ul>
                        </nav>
                    </div>
                    <div id="banner">
                        <ul class="menu menu-sidebar">
                            <li class="level1 item933"><a class="level1" href="http://irri-hrs.blogspot.com/"><span>Jobs</span></a></li>
                            <li class="level1 item934"><a class="level1" href="/news"><span>News and events</span></a></li>
                            <li class="level1 item935"><a class="level1" href="/rice-today"><span>Rice Today</span></a></li>
                            <li class="level1 item936"><a class="level1" href="/blogs"><span>Blogs</span></a></li>
                            <li class="level1 item937"><a class="level1" href="/resources"><span>Resources</span></a></li>
                            <li class="level1 item938"><a class="level1" href="/cgiar"><span>CGIAR</span></a></li>
                        </ul>
                    </div>
                </header>
            </div>
        </div>

        <div id="block-main" class="bg-white">

            <div id="footerv1" class="width-center">
                <div class="breadcrumbs" style="float:left">
                    <a href="http://irri.org">IRRI</a><span>TOOLS AND DATABASES</span><span>WeRise</span><strong><?php echo _CURRENT_OPT ?></strong>
                </div>
                <div style="float:right">

                    <?php if(dss_auth::getUsername()!=='') : ?>
                    <div class="btn-group">                    
                    <span class="label label-success" style="padding:3px 10px 7px 10px; margin-right:5px"><i class="icon-user"> </i> &nbsp;<?php echo dss_auth::getUsername() ?></span>
                    </div>
                    <?php endif; ?>                    
                    
                    <div class="btn-group">
                        <button class="btn btn-small dropdown-toggle" data-toggle="dropdown"><i class="icon-globe"> </i> Select Language <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?lang=en"><i class="icon-flag-US"> </i> English</a></li>
                            <li><a href="index.php?lang=la"><i class="icon-flag-LA"> </i> ພາສາລາວ</a></li>
                            <li><a href="index.php?lang=id"><i class="icon-flag-ID"> </i> Bahasa Indonesia</a></li>
                            <li><a href="index.php?lang=th"><i class="icon-flag-TH"> </i> ไทย</a></li>
                            <li><a href="index.php?lang=ph"><i class="icon-flag-PH"> </i> Filipino</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-small" onclick="javascript:window.print()"><i class="icon-print"> </i> Print</button>
                    </div>
                    
                    <?php if (_opt(sysoptions::_ADM_SHOW_MENU)) : ?>
                        <button class="btn btn-small" onclick="javascript:window.location.assign('admin.php')"><i class="icon-cog"> </i> Admin</button>
                    <?php endif;?>                    
                    
                </div>
                <div class="clearfix"></div>
                
                <div id="werise-error-div" class="alert alert-error alert-fixed">
                    <h4>An error has occurred!</h4>
                    <p id="werise-error-msg" style="margin:5px 0 0 0"></p>
                </div>                            
                
            </div>            

            <div class="content clearfix">                
                -content-
            </div>

<?php if (dss_auth::checkAccess2()===false) : ?>
<!-- Login Modal -->
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" data-backdrop="static" style="width:400px">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form id="login-form">

        <div class="modal-header modal-header-info">
		  <h2><i class="icon-user icon-white"></i> Login</h2>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" placeholder="username" tabindex="1">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="password" tabindex="2">
          </div>

          <div class="alert alert-error alert-fixed" id="login-error" style="width:300px"><i class="icon-exclamation-sign" style="color:#ff0000"> </i> Invalid credentials!</div>
		  <div class="alert alert-info alert-fixed" style="display:block;width:300px">Do you wish to access to this website? <a href="index.php?pageaction=register"><strong>Register an account now!</strong></a></div>

        </div>

        <div class="modal-footer">
          <button id="login-submit" type="button" class="btn btn-default" tabindex="3">Login</button>
        </div>

      </form>

    </div>
  </div>
</div>
<?php endif;?>

            <div id="footerv1" class="width-center">
                <div id="footer-content">
                    <div id="irrijcrp"><img src="images/irri-jcrp3.png" width="146" height="148" /></div>
                    <div id="footer-text">
                        <div id="footer-info">
                            <p><strong><?php __('IRRI-Japan Collaborative Research Project') ?></strong></p>
                            <p><?php __('The IRRI-Japan Collaborative Research Projects have been funded by the government of Japan through the Ministry of Agriculture, Forestry, and Fisheries (MAFF) since 1984. CCARA, which is the latest project, was launched in August 2010 and will end in September 2015.') ?> </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

        </div>

        <div id="block-bottom-c" class="bg-white">
            <div class="block-bottom-c wrapper ">

                <section id="bottom-c" class="grid-block">
                    <div class="grid-box width100 grid-v">
                        <div class="module mod-box mod-box-default  deepest">

                            <ul id="mainlevel">
                                <li><span class="mainlevel">ABOUT IRRI</span>
                                    <ul>
                                        <li><a href="/about-us/our-mission" class="sublevel">Our mission</a></li>
                                        <li><a href="/about-us/our-people" class="sublevel">Our people</a></li>
                                        <li><a href="/about-us/our-funding" class="sublevel">Our funding</a></li>
                                        <li><a href="/about-us/our-facilities" class="sublevel">Our facilities</a></li>
                                        <li><a href="/resources/publications/annual-reports" class="sublevel">Annual reports</a></li>
                                        <li><a href="/about-us/our-history" class="sublevel">History</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">OUR WORK</span>
                                    <ul>
                                        <li><a href="/our-work/research" class="sublevel">Research</a>
                                            <ul >
                                                <li><a href="http://irri.org/our-work/research/#research-2" class="sublevel">GRiSP</a></li>
                                                <li><a href="http://irri.org/about-us/our-organization?slider=underresearch#country_office-1" class="sublevel">Organizational units</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="/our-work/training" class="sublevel">Training</a></li>
                                        <li><a href="/our-work/seeds" class="sublevel">Seeds</a></li>
                                        <li><a href="/our-work/locations" class="sublevel">Locations</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">OUR IMPACT</span>
                                    <ul>
                                        <li><a href="/our-impact/increase-food-security" class="sublevel">Increasing food security</a></li>
                                        <li><a href="/our-impact/protecting-the-environment" class="sublevel">Protecting the environment</a></li>
                                        <li><a href="/our-impact/engaging-women" class="sublevel">Engaging women</a></li>
                                        <li><a href="/our-impact/tackling-climate-change" class="sublevel">Tackling climate change</a></li>
                                        <li><a href="/our-impact/reducing-poverty" class="sublevel">Reducing poverty</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">NEWSROOM</span>
                                    <ul>
                                        <li><a href="/news" class="sublevel">Media releases</a></li>
                                        <li><a href="http://irri-news.blogspot.com/" class="sublevel">IRRI News</a></li>
                                        <li><a href="/blogs" class="sublevel">Blogs</a></li>
                                        <li><a href="/resources/publications/rice-today-magazine" class="sublevel">Rice Today Magazine</a></li>
                                        <li><a href="http://eveo.irri.org/" class="sublevel">Events</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">GET INVOLVED</span>
                                    <ul>
                                        <li><a href="http://irri-hrs.blogspot.com/" class="sublevel">Jobs</a></li>
                                        <li><a href="/our-work/training" class="sublevel">Training</a></li>
                                        <li><a href="/our-work/training" class="sublevel">Scholarships</a></li>
                                        <li><a href="/get-involved/donate" class="sublevel">Donate</a></li>
                                    </ul>
                                </li>
                                <li><a href="/sitemap" class="mainlevel">Sitemap</a></li>
                                <li><a href="/legal-notice" class="mainlevel">Legal Notice</a></li>
                            </ul>

                        </div>
                    </div>

                    <div class="grid-box width100 grid-v">
                        <div class="module mod-box mod-box-default row2 deepest">

                            <ul id="mainlevel">
                                <li><span class="mainlevel">NETWORKS</span>
                                    <ul>
                                        <li><a href="http://c4rice.irri.org" class="sublevel">C4 Rice Project</a></li>
                                        <li><a href="/networks/council-for-partnership-on-rice-research-in-asia" class="sublevel">Council for Partnership on Rice Research in Asia</a></li>
                                        <li><a href="http://csisa.cimmyt.org/" target="_blank" class="sublevel">Cereal Systems Initiative for South Asia</a></li>
                                        <li><a href="/networks/climate-change-affecting-land-use-in-the-mekong-delta" class="sublevel">Climate change affecting land use in the Mekong Delta</a></li>
                                        <li><a href="/networks/climate-change-adaptation-in-rainfed-rice-areas" class="sublevel">Climate Change Adaptation in Rainfed Rice Areas</a></li>
                                        <li><a href="/networks/consortium-for-unfavorable-rice-environments" class="sublevel">Consortium for Unfavorable Rice Environments</a></li>
                                        <li><a href="http://ricephenonetwork.irri.org/" target="_blank" class="sublevel">Global Rice Phenotyping Network</a></li>
                                        <li><a href="http://hrdc.irri.org/" class="sublevel">Hybrid Rice Development Consortium</a></li>
                                        <li><a href="/networks/irrigated-rice-research-consortium" class="sublevel">Irrigated Rice Research Consortium</a>
                                            <ul>
                                                <li><a href="/networks/irrigated-rice-research-consortium/closing-rice-yield-gaps-in-asia-with-reduced-environmental-footprint" class="sublevel">Closing rice yield gaps in Asia with reduced environmental footprint</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="http://inger.irri.org/" class="sublevel">International Network for Genetic Evaluation of Rice </a></li>
                                        <li><a href="https://inqr.groupsite.com/main/summary" class="sublevel">International Network for Quality Rice</a></li>
                                        <li><a href="https://sites.google.com/a/irri.org/strasa/" class="sublevel">Stress-Tolerant Rice for Africa and South Asia</a></li>
                                        <li><a href="/networks/temperate-rice-research-consortium" class="sublevel">Temperate Rice Research Consortium</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">TOOLS AND DATABASES</span>
                                    <ul>
                                        <li><a href="/tools-and-databases/international-rice-information-system" class="sublevel">International Rice Information System</a></li>
                                        <li><a href="http://ricelib.irri.org" target="_blank" class="sublevel">Library Catalog</a></li>
                                        <li><a href="http://cropmanager.irri.org/" class="sublevel">Rice Crop Manager</a></li>
                                        <li><a href="https://sites.google.com/a/irri.org/oryza2000/home" class="sublevel">ORYZA</a></li>
                                        <li><a href="http://www.knowledgebank.irri.org/" target="_blank" class="sublevel">Rice Knowledge Bank</a></li>
                                        <li><a href="http://ricestat.irri.org:8080/wrs2/entrypoint.htm" target="_blank" class="sublevel">World Rice Statistics</a></li>
                                        <li><a href="http://ricestat.irri.org/research/index.php" target="_blank" class="sublevel">Farm Household Survey Database</a></li>
                                        <li><a href="https://sites.google.com/a/irri.org/weedsmart/" class="sublevel">Weedsmart</a></li>
                                        <li><a href="https://sites.google.com/a/irri.org/climate-unit/real-time-weather" class="sublevel">Local climate</a></li>
                                        <li><a href="/tools-and-databases/irri-dataverse" class="sublevel">IRRI Dataverse</a></li>
                                        <li><a href="/tools-and-databases/werise" class="sublevel">WeRise</a></li>
                                    </ul>
                                </li>
                                <li><a href="/resources" class="mainlevel">RESOURCES</a>
                                    <ul>
                                        <li><a href="/resources/publications" class="sublevel">Publications</a></li>
                                        <li><a href="/resources/publications/books" class="sublevel">IRRI Books</a></li>
                                        <li><a href="/resources/publications/brochures" class="sublevel">Brochures</a></li>
                                        <li><a href="http://www.youtube.com/user/irrivideo" target="_blank" class="sublevel">Videos</a></li>
                                        <li><a href="http://www.flickr.com/photos/ricephotos/collections/" target="_blank" class="sublevel">  Images</a></li>
                                    </ul>
                                </li>
                                <li><span class="mainlevel">CONTACT US</span>
                                    <ul>
                                        <li><a href="/contactus/media-releases" class="sublevel">Media inquiries</a></li>
                                        <li><a href="/contactus/general-inquiries" class="sublevel">General inquiries</a></li>
                                        <li><a href="/about-us/visitor-information" class="sublevel">Visitor information</a></li>
                                    </ul>
                                </li>
                            </ul>

                        </div>
                    </div>
                </section>

            </div>
        </div>

        <div id="block-footer" class="bg-dark bg-colored">
            <div class="block-footer wrapper ">

                <footer id="footer">
                    <div class="module   deepest">

                        <p><a href="http://www.cgiar.org" target="_blank"><img class="cgiar" style="float: left;" src="<?php echo $tpldir?>/images/cgiar.png" alt="cgiar" width="50" height="58" /></a><span></span></p>
                        <p><span>IRRI is a member of the <a href="http://www.cgiar.org" target="_blank"><strong>CGIAR</strong></a> Consortium.</span><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img class="cc" src="<?php echo $tpldir?>/images/creative_commons.png" alt="" align="right" /></a></p>

                    </div>
                </footer>

            </div>
        </div>

    <debug></debug>
    </body>
</html>