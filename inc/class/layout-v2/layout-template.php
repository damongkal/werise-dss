<!DOCTYPE html>
<html lang="en">
    <?php $lang_choice = language::getInstance()->getLang(); ?>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>IRRI &raquo; Tools and Database &raquo; WeRise - Weather-rice-nutrient integrated decision support system</title>

        <!-- Bootstrap core CSS -->
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="assets/vendor/fontawesome-free-5.0.8/css/fa-solid.min.css" rel="stylesheet">
        <link href="assets/vendor/fontawesome-free-5.0.8/css/fontawesome.min.css" rel="stylesheet">

        <!-- Flag Icons -->
        <link href="assets/vendor/flag-icon-css/css/flag-icon.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="assets/fonts/opensans.css" rel="stylesheet">
        <link href="assets/css/scrolling-nav.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="assets/vendor/jquery/jquery.min.js"></script>        
        <script type="text/javascript">
            jQuery.noConflict();
        </script>                        

        <script type="text/javascript" src="gzip.php?group=common"></script>
        <script type="text/javascript">
            window.langdata = <?php echo json_encode(language::getInstance()->jstranslate()); ?>;
            weriseApp.recentSelect = <?php echo json_encode(dss_utils::getLastSelectValues()); ?>;
            window.dataselect = weriseApp.recentSelect;
            window.show_googlemaps = <?php echo ((_opt(sysoptions::_SHOW_MAP)) ? 'true' : 'false') ?>;
            window.is_logged = <?php echo (dss_auth::checkAccess2() ? 'true' : 'false') ?>;
            window._env = '<?php echo _ADM_ENV ?>';
        </script>

    </head>

    <body id="page-top">

        <!-- Navigation -->
        <nav class="navbar navbar-expand-md navbar-light bg-light fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">&nbsp;</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarResponsive">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="index.php">WeRise</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="index.php?pageaction=about"><?php __('About') ?> WeRise</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="index.php?pageaction=weather"><?php echo strtoupper(_t('Weather Advisory')) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="index.php?pageaction=oryza"><?php echo strtoupper(_t('Crop Advisory')) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="index.php?pageaction=terms"><?php echo strtoupper(_t('Terms and Conditions')) ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section id="bread-crumbs">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://irri.org">IRRI</a></li>
                                <li class="breadcrumb-item"><a href="http://werise.irri.org">WeRise</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo _CURRENT_OPT ?></li>
                            </ol>
                        </nav>
                    </div>
                    <div id="btn-group-top" class="col-sm-12 col-md-6">
                        <div class="btn-group" role="group">
                            <div class="btn-group" role="group">
                                <button id="btn-select-lang" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="flag-icon flag-icon-<?php echo language::convertToCountry($lang_choice) ?>"></span> <?php echo language::getLangs($lang_choice) ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btn-select-lang">
                                    <?php foreach (language::getLangs() as $lang_id => $lang_name): ?>
                                        <a class="dropdown-item" href="index.php?lang=en"><span class="flag-icon flag-icon-<?php echo language::convertToCountry($lang_id) ?>"></span> <?php echo $lang_name ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button class="btn btn-sm" onclick="javascript:window.print()"><i class="fas fa-print"></i> Print</button>
                            <?php if (_opt(sysoptions::_ADM_SHOW_MENU) || (dss_auth::getUsername() === 'admin')) : ?>
                                <button class="btn btn-sm" onclick="javascript:window.location.assign('admin.php')"><i class="fas fa-cog"></i> Admin</a></button>
                            <?php endif; ?>    
                            <?php if (dss_auth::getUsername() !== '') : ?>
                                <button class="btn btn-sm"><i class="fas fa-user-circle"></i> <?php echo dss_auth::getUsername() ?></button>
                            <?php endif; ?>                                     
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="werise-error-div" class="hide">
            <div class="container">
                <div class="alert alert-danger">
                    <span class="fa-stack">
                        <i class="fas fa-circle fa-stack-2x"></i>
                        <i class="fas fa-exclamation fa-stack-1x fa-inverse"></i>
                    </span>
                    <span id="werise-error-msg">error message here</span>
                </div>     
            </div>
        </section>        

        -content-

        <section id="irri-japan" class="mt-4">
            <div class="container">
                <div class="row">

                    <div class="col-md-auto"><img src="images/irri-jcrp3.png" width="146" height="148" /></div>
                    <div class="col">
                        <p id="irri-japan-header"><?php __('IRRI-Japan Collaborative Research Project') ?></p>
                        <p><?php __('The IRRI-Japan Collaborative Research Projects have been funded by the government of Japan through the Ministry of Agriculture, Forestry, and Fisheries (MAFF) since 1984. CCARA, which is the latest project, was launched in August 2010 and will end in September 2015.') ?></p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="pt-3 pb-2">
            <div class="container">

                <div class="row">
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <p>ABOUT IRRI</p>
                        <ul>
                            <li><a href="http://irri.org/about-us/our-mission">Our mission</a></li>
                            <li><a href="http://irri.org/about-us/our-people">Our people</a></li>
                            <li><a href="http://irri.org/about-us/our-funding">Our funding</a></li>
                            <li><a href="http://irri.org/about-us/our-facilities">Our facilities</a></li>
                            <li><a href="http://irri.org/resources/publications/annual-reports">Annual reports</a></li>
                            <li><a href="http://irri.org/about-us/our-history">History</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <p>OUR WORK</p>
                        <ul>
                            <li><a href="http://irri.org/our-work/research">Research</a>
                                <ul>
                                    <li><a href="http://irri.org/our-work/research/#research-2">GRiSP</a></li>
                                    <li><a href="http://irri.org/about-us/our-organization?slider=underresearch#country_office-1">Organizational units</a></li>
                                </ul>
                            </li>
                            <li><a href="http://irri.org/?Itemid=344">Training</a></li>
                            <li><a href="http://irri.org/our-work/seeds">Seeds</a></li>
                            <li><a href="http://irri.org/our-work/locations">Locations</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <p>OUR IMPACT</p>
                        <ul>
                            <li><a href="http://irri.org/our-impact/increase-food-security">Increasing food security</a></li>
                            <li><a href="http://irri.org/our-impact/protecting-the-environment">Protecting the environment</a></li>
                            <li><a href="http://irri.org/our-impact/engaging-women">Engaging women</a></li>
                            <li><a href="http://irri.org/our-impact/tackling-climate-change">Tackling climate change</a></li>
                            <li><a href="http://irri.org/our-impact/reducing-poverty">Reducing poverty</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <p>NEWSROOM</p>
                        <ul>
                            <li><a href="http://irri.org/news">Media releases</a></li>
                            <li><a href="http://irri-news.blogspot.com/">IRRI News</a></li>
                            <li><a href="http://irri.org/blogs">Blogs</a></li>
                            <li><a href="http://ricetoday.irri.org" target="_blank">Rice Today Magazine</a></li>
                            <li><a href="http://irri.org/events">Events</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <p>GET INVOLVED</p>
                        <ul>
                            <li><a href="http://irri.org/jobs">Jobs</a></li>
                            <li><a href="http://irri.org/?Itemid=344">Training</a></li>
                            <li><a href="http://irri.org/?Itemid=344">Scholarships</a></li>
                            <li><a href="http://irri.org/get-involved/donate">Donate</a></li>
                            <li><a style="font-weight: 700" href="http://irri.org/sitemap">Sitemap</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-lg-2 mb-3">
                        <a style="font-weight: 700" href="http://irri.org/legal-notice">Legal Notice</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 col-lg-4 mb-3">
                        <p>NETWORKS</p>
                        <ul>
                            <li><a href="http://c4rice.irri.org">C4 Rice Project</a></li>
                            <li><a href="http://corra.irri.org" target="_blank">Council for Partnership on Rice Research in Asia </a></li>
                            <li><a href="http://csisa.cimmyt.org/" target="_blank">Cereal Systems Initiative for South Asia</a></li>
                            <li><a href="http://irri.org/networks/climate-change-affecting-land-use-in-the-mekong-delta">Climate change affecting land use in the Mekong Delta</a></li>
                            <li><a href="http://cure.irri.org/">Consortium for Unfavorable Rice Environments</a></li>
                            <li><a href="http://irri.org/networks/climate-change-adaptation-in-rainfed-rice-areas">Climate Change Adaptation in Rainfed Rice Areas</a></li>
                            <li><a href="http://ricephenonetwork.irri.org/" target="_blank">Global Rice Phenotyping Network</a></li>
                            <li><a href="http://hrdc.irri.org/">Hybrid Rice Development Consortium</a></li>
                            <li><a href="http://irri.org/networks/irrigated-rice-research-consortium">Irrigated Rice Research Consortium</a>
                                <ul>
                                    <li><a href="http://irri.org/networks/irrigated-rice-research-consortium/closing-rice-yield-gaps-in-asia-with-reduced-environmental-footprint">Closing rice yield gaps in Asia with reduced environmental footprint</a></li>
                                </ul>
                            </li>
                            <li><a href="http://inger.irri.org/">International Network for Genetic Evaluation of Rice </a></li>
                            <li><a href="http://postharvestla.irri.org">Postharvest Learning Alliance</a></li>
                            <li><a href="http://climatechange.irri.org/">Rice and climate change research</a></li>
                            <li><a href="http://irri.org/networks/rice-straw-project">Rice Straw Project</a></li>
                            <li><a href="http://strasa.irri.org/" target="_blank">Stress-Tolerant Rice for Africa and South Asia</a></li>
                            <li><a href="http://irri.org/networks/temperate-rice-research-consortium">Temperate Rice Research Consortium</a></li>
                            <li><a href="http://irri.org/networks/siil-polder-project-bangladesh">SIIL-Polder Project Bangladesh</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4 col-lg-3 mb-3">
                        <p>TOOLS AND DATABASES</p>
                        <ul>
                            <li><a href="http://ricepedia.org">Ricepedia</a></li>
                            <li><a href="http://irri.org/tools-and-databases/international-rice-information-system">International Rice Information System</a></li>
                            <li><a href="http://library.irri.org" target="_blank">Library Catalog</a></li>
                            <li><a href="https://sites.google.com/a/irri.org/oryza2000/home">ORYZA</a></li>
                            <li><a href="http://www.knowledgebank.irri.org/" target="_blank">Rice Knowledge Bank</a></li>
                            <li><a href="http://ricestat.irri.org:8080/wrsv3/entrypoint.htm" target="_blank">World Rice Statistics</a></li>
                            <li><a href="http://ricestat.irri.org/fhsd/index.php" target="_blank">Farm Household Survey Database</a></li>
                            <li><a href="https://sites.google.com/a/irri.org/weedsmart/">Weedsmart</a></li>
                            <li><a href="http://www.knowledgebank.irri.org/decision-tools/weed-identification">Weed identification tool</a></li>
                            <li><a href="https://sites.google.com/a/irri.org/climate-unit/real-time-weather">Local climate</a></li>
                            <li><a href="http://irri.org/tools-and-databases/irri-dataverse">IRRI Dataverse</a></li>
                            <li><a href="http://cropmanager.irri.org/">Crop Manager</a></li>
                            <li><a href="http://www.genesys-pgr.org/">Genesys</a></li>
                            <li><a href="http://oryzasnp.org/iric-portal/">Rice SNP-SEEK database</a></li>
                            <li><a href="http://werise.irri.org" target="_blank">WeRise</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-3">
                        <p>RESOURCES</p>
                        <ul>
                            <li><a href="http://scientific-output.irri.org/" target="_blank">Scientific outputs</a></li>
                            <li><a href="http://irri.org/resources/publications">Publications</a></li>
                            <li><a href="http://irri.org/resources/publications/books">IRRI Books</a></li>
                            <li><a href="http://www.youtube.com/user/irrivideo" target="_blank">Videos</a></li>
                            <li><a href="http://irri.org/resources/publications/brochures">Brochures</a></li>
                            <li><a href="http://www.flickr.com/photos/ricephotos/collections/" target="_blank">Images</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3">
                        <p>CONTACT US</p>
                        <ul>
                            <li><a href="http://irri.org/contact-us/media-inquiries">Media inquiries</a></li>
                            <li><a href="http://irri.org/contact-us/general-inquiries">General inquiries</a></li>
                            <li><a href="http://irri.org/about-us/visitor-information">Visitor information</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </footer>

        <section id="post-footer" style="background-color: #282828">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 py-2">
                        <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img src="images/layout/creative_commons.png" class="cc" width="149" height="40" style="float: right;" /></a>
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </section>

        <?php if (dss_auth::checkAccess2() === false) : ?>
            <!-- Login Modal -->
            <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Login</h5>    
                        </div>

                        <form id="login-form">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" placeholder="username" tabindex="1">
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" placeholder="password" tabindex="2">
                                </div>

                                <div id="login-error" class="alert alert-danger d-none">
                                    <span class="fa-stack">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fas fa-exclamation fa-stack-1x fa-inverse"></i>
                                    </span>
                                    <span id="login-error-msg"></span>
                                </div>
                                <div class="alert alert-info alert-fixed">Do you wish to access to this website? <a href="index.php?pageaction=register"><strong>Register an account now!</strong></a></div>

                            </div>

                            <div class="modal-footer">
                                <button id="login-submit" type="button" class="btn" tabindex="3">Login</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        <?php endif; ?>        


        <!-- Bootstrap core JavaScript -->
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Plugin JavaScript -->
        <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom JavaScript for this theme -->
        <script src="assets/js/scrolling-nav.js"></script>

    <debug></debug>

</body>

</html>
