<div class="width-center">

    <?php if (isset($_GET['err']) && $_GET['err']==='noaccess') : ?>
    <div class="alert alert-error alert-fixed" style="display:block"><i class="icon-exclamation-sign" style="color:#ff0000"> </i> You are not allowed to view this content. Please <a href="#">contact us</a> if you need access to this website.</div>
    <?php endif; ?>

    <img src="images/banner-image3.jpg" style="margin-bottom: 10px" />

    <header>
        <h3 class="title" style="font-weight: 700; color:#547e1a">WeRise (<u>We</u>ather-<u>r</u>ice-nutrient <u>i</u>ntegrated decision <u>s</u>upport syst<u>e</u>m)</h3>
    </header>

    <div id="introduction">
        <p>
            <?php echo _t('<langidx>index0001</langidx>
            Rainfed rice yields are low and unstable mainly because of
            uncertainty in rainfall amount and distribution, existence of
            nutrient stress, and pest occurrence. Climate change poses the
            grave threat of extreme weather events that could further reduce
            rice productivity in rainfed areas.
            ')?>
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
            ')?>
        </p>
        <p>
            <?php echo _t('<langidx>index0003</langidx>
            Field tests on the WeRise prototype are going on in Indonesia and
            Lao PDR. The prototype for other countries may also be explored.
            To run the prototype, please click on the weather advisory or grain
            yield advisory image below or button above. Feedbacks to improve
            the prototype and/or establish collaboration are welcome.
            ')?>
        </p>
    </div>

    <div id="homeimages" class="clearfix">
        <div style="width:383px;position:relative;float:left;margin-right:40px;margin-left: 40px">
            <!-- image from -->
            <a href="index.php?pageaction=weather"><img src="images/home01-aug2015.gif" width="383" height="322" style="width:383px;height:322px" /></a>
            <div class="homeimages_overlay"><?php __('Weather Advisory')?></div>
        </div>
        <div style="width:383px;position:relative;;float:left">
            <a href="index.php?pageaction=oryza"><img src="images/home02.jpg" width="383" height="322" style="width:385px;height:322px" /></a>
        </div>
    </div>


</div>

<?php if (_opt(sysoptions::_OPT_GOOGLE_ANALYTICS)) : ?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-49099816-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php endif; ?>