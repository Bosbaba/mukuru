<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mukuru</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-toggle.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/stylish-portfolio.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <a id="menu-toggle" href="#" class="btn btn-dark btn-lg toggle"><i class="fa fa-bars"></i></a>
    <nav id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <a id="menu-close" href="#" class="btn btn-light btn-lg pull-right toggle"><i class="fa fa-times"></i></a>
            <li class="sidebar-brand">
                <a href="#top"  onclick = $("#menu-close").click(); >Mukuru</a>
            </li>
            <li>
                <a href="#top" onclick = $("#menu-close").click(); >Home</a>
            </li>
            <li>
                <a href="#services" onclick = $("#menu-close").click(); >Order Now</a>
            </li>
            <li>
                <a href="#contact" onclick = $("#menu-close").click(); >Contact</a>
            </li>
        </ul>
    </nav>

    <!-- Header -->
    <header id="top" class="header">
        <div class="text-vertical-center">
            <h1>We Sell Currency...</h1>
            <h3>Mukuru</h3>
            <br>
            <a href="#about" class="btn btn-dark btn-lg">Buy Now</a>
        </div>
    </header>

    <!-- About -->
    <section id="about" class="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>We will convert your RANDs into any supported currency.</h2>
                    <p class="lead">You have come to the right place for the best conversion rates</p>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>

    <!-- Services -->
    <!-- The circle icons use Font Awesome's stacked icon classes. For more information, visit http://fontawesome.io/examples/ -->
    <section id="services" class="services bg-primary">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-10 col-lg-offset-1">
                    <h2>Place your order now!!</h2>
                    <hr class="small">
                    <div class="row">
                        <?php foreach ($currency as $cur): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="service-item">
                                <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-<?= $cur->icon; ?> fa-stack-1x text-primary"></i>
                            </span>
                                <h4>
                                    <strong>Buy <?= $cur->display_name; ?>[<?= $cur->currency; ?>]</strong>
                                </h4>
                                <p>
                                    Exchange rate:<br /><strong> 1 ZAR = <?= floatval($cur->rate) . ' ' . $cur->currency; ?></strong><br />
                                    Surcharge: <?= floatval($cur->surcharge); ?>%<br />
                                </p>
                                <a href="#" class="btn btn-light init-buy" id="<?=strtolower($cur->currency);?>" data-id="<?=$cur->id;?>" data-cur="<?=$cur->currency;?>" data-display="<?=$cur->display_name;?>">Buy <?= $cur->currency; ?></a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.col-lg-10 -->
                <div id='calc-container' class="col-lg-10 col-lg-offset-1 hide">
                    <div id="calc-error" class="error hide"><h4 class="red"> Oops something went wrong please try again later.</h4></div>
                    <div id='calc-start' class="currency_forms" >
                        <hr class="small">
                        <h2>Calculate Amount</h2>
                        <form class="form-inline" id="calc-form" action="/calculate" method="post">
                            <div class="form-group bg-primary">
                                <input id="calc-amount" type="number" min="0.01" step="0.01" name="calc_amount" class="form-control number">
                                <input id="calc-id" type="hidden" name="calc_id" class="form-control">
                            </div>
                            <div class="form-group bg-primary">
                                <input id="calc-currency" type="checkbox" name="calc_type" value="ZAR" checked data-toggle="toggle" data-on="South African Rand" data-off="British Pound" data-onstyle="default" data-offstyle='default' data-width="150" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="button" id="calculate" name="calculate" value="Calculate" class="form-control" />
                            </div>
                        </form>
                    </div>
                    <div id='buy-container' class="currency_forms hide" >
                        <div><br /></div>
                        <hr class="small">
                        <h2>Your rates</h2>
                        <div class="init-rates" ><span id="local" >0 ZAR</span> = <span id="foreign" >0 USD</span></div>
                        <form class="form-inline" id="buy-form" action="/buy" method="post">
                            <div class="form-group">
                                <input id="buy-token" type="hidden" name="buy_token" class="form-control">
                                <input type="button" id="buy" name="buy" value="Buy Now" class="form-control" />
                            </div>
                        </form>
                     </div>
                    <div id='buy-success' class="currency_forms hide" >
                        <div><br /></div>
                        <hr class="small">
                        <h2>Thank You</h2>
                        <div class="final-rates" >Your purchase was successful</span></div>
                        <div class="final-rates" >You purchased <span id="foreign-final" >0 USD</span> for R <span id="local-final" ></span> [ZAR] </div>
                        <div class="final-rates currency_forms hide" id="discount-final" >A <span></span>% discount was awarded</div>
                        <div class="final-rates currency_forms hide" id="email-final" >You will receive an email with your purchase details on</div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    <aside class="call-to-action bg-primary">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <!-- h3>The buttons below are impossible to resist.</h3>
                    <a href="#" class="btn btn-lg btn-light">Click Me!</a>
                    <a href="#" class="btn btn-lg btn-dark">Look at Me!</a -->
                </div>
            </div>
        </div>
    </aside>

    <!-- Footer -->
    <footer>
        <div id="contact" class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1 text-center">
                    <h4><strong>Developed by Jaco Uys</strong>
                    </h4>
                    <p>609 Vacy Lyle<br>Elarduspark, Pretoria, 0181</p>
                    <ul class="list-unstyled">
                        <li><i class="fa fa-phone fa-fw"></i> 082 782 5818</li>
                        <li><i class="fa fa-envelope-o fa-fw"></i>  <a href="mailto:jaco@zendfusion.co.za">jaco@zendfusion.co.za</a>
                        </li>
                    </ul>
                    <br>
                    <ul class="list-inline">
                        <li>
                            <a href="#"><i class="fa fa-facebook fa-fw fa-3x"></i></a>
                        </li>
                        <li>
                            <a href="https://twitter.com/zendfusion"><i class="fa fa-twitter fa-fw fa-3x"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dribbble fa-fw fa-3x"></i></a>
                        </li>
                    </ul>
                    <hr class="small">
                    <p class="text-muted">Copyright &copy; Mukuru Practical Test 2016</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-toggle.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script>
    // Closes the sidebar menu
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });

    // Opens the sidebar menu
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });

    $(".init-buy").click(function(e) {
        e.preventDefault();
        //Close all containers
        $(".currency_forms:not([class*='bbox'])").addClass('hide');
        var currency = $(this).data();
        $("#calc-currency").bootstrapToggle('destroy');
        $("#calc-currency").data('off', currency.display);
        $("#calc-currency").bootstrapToggle({off: currency.display});
        $("#calc-start").removeClass("hide");
        $("#calc-container").removeClass("hide");
        $("#calc-id").val(currency.id);

        console.log($("#calc-currency").data());
    });

    $("#calculate").click(function(e) {
        e.preventDefault();

        if (!$("#calc-amount").val())
        {
            return;
        }

        $.post('/calculate', $("#calc-form").serialize()).done(function(data) {
            console.log(data);
            if (data.result == 1)
            {
                var local_display = data.calc.local;
                var foreign_display = data.calc.foreign;

                $("#local").html(local_display.toFixed(2) + " ZAR");
                $("#foreign").html(foreign_display.toFixed(2) + " " + data.currency.currency);
                $("#buy-token").val(data.token);
                $("#buy-container").removeClass('hide');
                $("#buy-form").removeClass('hide');
            }
            else
            {
                $("#calc-error").removeClass('hide');
                $(".currency_forms").addClass('hide');
            }
        })
    });

    $("#buy").click(function(e) {
        e.preventDefault();
        $("#buy-form").addClass('hide');

        $.post('/buy', $("#buy-form").serialize()).done(function(data) {
            console.log(data);
            if (data.result == 1)
            {
                $("#local-final").html(data.save.local);
                $("#foreign-final").html(data.save.foreign + " " + data.save.currency);
                $('.currency_forms').addClass('hide');

                document.getElementById("calc-form").reset();
                document.getElementById("buy-form").reset();

                if (data.save.discount)
                {
                    $("#discount-final span").html(data.save.discount);
                    $("#discount-final").removeClass('hide');
                }

                if (data.save.mail_send)
                {
                    $("#email-final").removeClass('hide');
                }

                $('#buy-success').removeClass('hide');
            }
            else
            {
                $("#calc-error").removeClass('hide');
                $(".currency_forms").addClass('hide');
            }
        });
    });

    $('.number').keypress(function(event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
            ((event.which < 48 || event.which > 57) &&
            (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }

        var text = $(this).val();

        if ((text.indexOf('.') != -1) &&
            (text.substring(text.indexOf('.')).length > 2) &&
            (event.which != 0 && event.which != 8) &&
            ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    });

    // Scrolls to the selected menu item on the page
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
    </script>

</body>

</html>
