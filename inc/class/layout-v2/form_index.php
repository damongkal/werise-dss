<section id="main-content">
    <div class="container">

        <?php if (isset($_GET['err']) && $_GET['err'] === 'noaccess') : ?>
            <div class="alert alert-error alert-fixed" style="display:block"><i class="icon-exclamation-sign" style="color:#ff0000"> </i> You are not allowed to view this content. Please <a href="#">contact us</a> if you need access to this website.</div>
        <?php endif; ?>                

        <div id="home-heroimage" class="mt-2 mb-2 text-center">
            <img class="img-fluid" src="images/banner-image4.jpg" />
            <div id="hero-image-text" class="card">
                <div class="card-body">
                    <h1 class="mb-1">WeRise</h1>
                    <p>Decision Support System for Rainfed Rice Production</p>
                </div>
            </div>                
        </div>


        <h2 id="home-title">WeRise (<u>We</u>ather-<u>r</u>ice-nutrient <u>i</u>ntegrated decision <u>s</u>upport syst<u>e</u>m)</h2>

        <p>
            <?php echo _t('<langidx>index0001</langidx>
            Rainfed rice yields are low and unstable mainly because of
            uncertainty in rainfall amount and distribution, existence of
            nutrient stress, and pest occurrence. Climate change poses the
            grave threat of extreme weather events that could further reduce
            rice productivity in rainfed areas.
            ') ?>
        </p>

        <p>
            <?php echo _t('<langidx>index0002</langidx>
            The IRRI-Japan project on Climate Change Adaptation in Rainfed Rice
            Areas (CCARA) is developing a decision support system (WeRise) to
            improve the livelihood of rainfed rice farmers under current and
            future climate  scenarios. This system uses a seasonal-weather
            forecast that provides farmers crucial weather information such as
            the start and end of the rainy season and rainfall distribution
            during the crop growing season. It also advises farmers when to
            sow and transplant the crop, what variety is appropriate, and how
            fertilizer may be efficiently applied.
            ') ?>
        </p>

        <p>
            <?php echo _t('<langidx>index0003</langidx>
            Field tests on the WeRise prototype are going on in Indonesia and
            Lao PDR. The prototype for other countries may also be explored.
            To run the prototype, please click on the weather advisory or crop
            advisory image below or button above. Feedbacks to improve
            the prototype and/or establish collaboration are welcome.
            ') ?>
        </p>

        <div id="homeimages" class="row">
            <div class="col text-right">
                <div class="home-features-wrapper-left">
                    <a href="index.php?pageaction=weather"><img src="http://<?php echo (_opt(sysoptions::_JAMSTEC_IMG))  ?>" /></a>
                    <div class="home-features-overlay"><?php echo _t('Weather Advisory') ?></div>
                </div>
            </div>    
            <div class="col">
                <div class="home-features-wrapper-right">
                    <a href="index.php?pageaction=oryza"><img src="images/home02.jpg" /></a>
                    <div class="home-features-overlay"><?php echo _t('Crop Advisory') ?></div>
                </div>
            </div>        
        </div>

    </div>            
</section>