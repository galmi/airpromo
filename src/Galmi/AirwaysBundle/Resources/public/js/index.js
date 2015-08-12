/**
 * Created by ildar on 02.08.15.
 */
$(document).ready(function () {
    var Router = {
        airports: [],
        routes: [],
        links: [],
        originEl: null,
        destinationEl: null,
        searchForm: $("#searchForm"),
        departureDate: $("#departureDate"),
        submit: $("#submit"),
        results: $("#results"),
        loader: $("#loader"),
        adsense: $("#adsense"),
        requests: 0,

        init: function (origin, destination) {
            this.originEl = origin;
            this.destinationEl = destination;

            this.initOrigin();
            this.initSubmit();
            $('.datepicker').pickadate({
                min: new Date(),
                selectMonths: true,
                selectYears: 15,
                format: 'yyyy-mm-dd'
            });
        },
        initOrigin: function () {
            this.originEl.find('option').remove();
            //this.originEl.append('<option value=""  selected></option>');
            for (var key in this.airports) {
                this.originEl.append('<option value=' + key + '>' + this.airports[key].name + '</option>');
            }
            //this.originEl.find('option').sort(NASort).appendTo(this.originEl);
            this.originEl.val('DMK');
            this.originEl.material_select();
            this.originEl.on('change', function () {
                Router.updateDestination();
            });
            Router.updateDestination();
        },
        initSubmit: function () {
            this.submit.on('click', $.proxy(function () {
                if (this.submit.hasClass('disabled')) {
                    return;
                }
                this.submit.addClass('disabled');
                var data = this.searchForm.serializeArray();
                var dataObj = {};
                $.each(data, function (_, kv) {
                    dataObj[kv.name] = kv.value;
                });
                var i, k;
                var routeOrigin = $.extend({}, this.routes[dataObj['origin']]);
                var routeDestination = [];
                for (i in routeOrigin) {
                    if (routeOrigin[i].code == dataObj['destination']) {
                        //routeOrigin[i]['origin'] = dataObj['origin'];
                        routeDestination.push($.extend({}, routeOrigin[i], {
                            origin: dataObj['origin']
                        }));
                        break;
                    }
                }
                var linkedAirportsOrigin = this.links[dataObj['origin']];
                if (linkedAirportsOrigin) {
                    for (i in linkedAirportsOrigin) {
                        var routeLinkedOrigin = $.extend({}, this.routes[linkedAirportsOrigin[i]]);
                        for (var k in routeLinkedOrigin) {
                            if (routeLinkedOrigin[k].code == dataObj['destination']) {
                                //routeLinkedOrigin[k]['origin'] = linkedAirportsOrigin[i];
                                routeDestination.push($.extend({}, routeLinkedOrigin[k], {
                                    origin: linkedAirportsOrigin[i]
                                }));
                                break;
                            }
                        }
                    }
                }

                var linkedAirportsDestination = this.links[dataObj['destination']];
                if (linkedAirportsDestination) {
                    for (i in linkedAirportsDestination) {
                        var routeLinkedDestination = $.extend({}, this.routes[linkedAirportsDestination[i]]);
                        for (k in routeLinkedDestination) {
                            if (routeLinkedDestination[k].code == dataObj['origin']) {
                                //routeLinkedDestination[k]['origin'] = dataObj['origin'];
                                //routeLinkedDestination[k]['code'] = linkedAirportsDestination[i];
                                routeDestination.push($.extend({}, routeLinkedDestination[k], {
                                    origin: dataObj['origin'],
                                    code: linkedAirportsDestination[i]
                                }));
                                break;
                            }
                        }
                    }
                }
                this.clearResults();
                try {
                    ga('send', 'event', 'search', dataObj.origin + dataObj.destination, dataObj.departureDate);
                } catch (e) {
                }
                var pathname = document.location.pathname;
                if (pathname.slice(-1) != '/') {
                    pathname += '/';
                }
                var urlTpl = "//{sourceId}." + document.location.host + pathname + "search/origin/{origin}/destination/{destination}/departureDate/{departureDate}";
                for (i in routeDestination) {
                    if (routeDestination[i].sources.length > 0) {
                        this.loaderShow(true);
                        this.adsenseShow();
                        $('html, body').animate({
                            scrollTop: this.loader.offset().top
                        }, 'slow');
                        for (k in routeDestination[i].sources) {
                            this.requests++;
                            dataObj['origin'] = routeDestination[i]['origin'];
                            dataObj['destination'] = routeDestination[i]['code'];
                            dataObj['sourceId'] = routeDestination[i].sources[k];
                            $.get(urlTpl.apply(dataObj), function (data) {
                                this.updateResults(data);
                            }.bind(this)).always(this.cameResponse);
                        }
                    }
                }
            }, this));
        },
        updateDestination: function () {
            this.destinationEl.material_select('destroy');
            this.destinationEl.find('option').remove();
            var originCode = this.originEl.val();
            var route = this.routes[originCode];
            if (typeof route != 'undefined') {
                for (var i in route) {
                    var row = route[i];
                    var airport = this.airports[row['code']];
                    this.destinationEl.append('<option value=' + airport.code + '>' + airport.name + '</option>');
                }
                //this.destinationEl.find('option').sort(NASort).appendTo(this.destinationEl);
                this.destinationEl.material_select();
            }
        },
        updateResults: function (data) {
            var rowTemplate = $("#result-row").html();
            var rowsCount = this.results.find('.result-row').length;
            for (var i in data) {
                var row = data[i];
                row['origin_name'] = Router.airports[row.origin].name;
                row['destination_name'] = Router.airports[row.destination].name;
                var div = $(rowTemplate.apply(row)).hide();
                this.initBookSubmit(div, row);
                this.initHotelLink(div, Router.airports[row.destination].city_id);
                this.results.append(div);
                div.fadeIn('slow');
            }

            this.results.find('.result-row').sort(ResultSort).appendTo(this.results);
            if (rowsCount == 0) {
                $('html, body').animate({
                    scrollTop: this.loader.offset().top
                }, 'slow');
            }
        },
        initHotelLink: function($div, id) {
            var hotelUrl = 'http://hotellook.com/search/?searchType=city&searchId={city_id}&marker=44648';
            if (LOCALE == 'th') {
                hotelUrl = 'http://th.hotellook.com/search/?searchType=city&searchId={city_id}&marker=44648';
            }
            if (id) {
                $div.find('a.hotel').attr('href', hotelUrl.apply({city_id: id})).removeClass('hide');
            }
        },
        initBookSubmit: function($div, submitData) {
            $div.find('button').on('click', function() {
                try {
                    ga('send', 'event', 'booknow', submitData.origin + submitData.destination, submitData.departureDate);
                } catch (e) {
                }

                var newForm = jQuery('<form>', {
                    'action': submitData.sourceSubmit.uri,
                    'method': submitData.sourceSubmit.method,
                    'target': '_blank'
                });
                for (var key in submitData.sourceSubmit.data) {
                    newForm.append(jQuery('<input>', {
                        'name': key,
                        'value': submitData.sourceSubmit.data[key],
                        'type': 'hidden'
                    }));
                }
                newForm.submit();
            });
        },
        clearResults: function () {
            this.results.find('.result-row').remove();
        },
        loaderShow: function (value) {
            if (value) {
                this.loader.removeClass('hide');
            } else {
                this.loader.addClass('hide');
            }
        },
        adsenseShow: function (value) {
            this.adsense.html(' <ins class="adsbygoogle"'+
            'style="display:block"'+
            'data-ad-client="ca-pub-7013266992778346"'+
            'data-ad-slot="7332479294"'+
            'data-ad-format="auto"></ins>'+
            '    <script>'+
            '    (adsbygoogle = window.adsbygoogle || []).push({});'+
            '</script>');
        },
        cameResponse: function () {
            Router.requests--;
            if (Router.requests <= 0) {
                Router.loaderShow(false);
                Router.requests = 0;
                Router.submit.removeClass('disabled');
            }
        }
    };
    $.getJSON('/bundles/galmiairways/js/airports_'+LOCALE+'.json', function (data) {
        Router.airports = data;
        Router.init($("#origin"), $("#destination"));
    });
    $.getJSON('/bundles/galmiairways/js/routes.json', function (data) {
        Router.routes = data;
        Router.updateDestination();
    });
    $.getJSON('/bundles/galmiairways/js/links.json', function (data) {
        Router.links = data;
    });

    //Изменяем iframe при смене размера окна
    var sizes = [1300, 700, 400];
    var flights = {
        'en': {
            1300: '<iframe scrolling="no" width="900" height="204" frameborder="0" src="//www.travelpayouts.com/widgets/6a56d09ef7adde499da00a5745eebb66.html?v=494"></iframe>',
            700: '<iframe scrolling="no" width="600" height="287" frameborder="0" src="//www.travelpayouts.com/widgets/42b73d7ec09bdddfa2ec07fb8fd34482.html?v=494"></iframe>',
            400: '<iframe scrolling="no" width="300" height="459" frameborder="0" src="//www.travelpayouts.com/widgets/e7ef9ae55f81f41934c4db18c76a0ae3.html?v=494"></iframe>'
        },
        'th': {
            1300: '<iframe scrolling="no" width="900" height="212" frameborder="0" src="//www.travelpayouts.com/widgets/2d106cdf67aab82f1fb3faaed0f5faa2.html?v=494"></iframe>',
            700: '<iframe scrolling="no" width="600" height="287" frameborder="0" src="//www.travelpayouts.com/widgets/9503a0976b660a895c64101b9830310b.html?v=494"></iframe>',
            400: '<iframe scrolling="no" width="300" height="459" frameborder="0" src="//www.travelpayouts.com/widgets/c75148021d5638a07f8cbb536469fc0a.html?v=494"></iframe>'
        }
    };
    var hotels = {
        'en': {
            1300: '<iframe scrolling="no" width="900" height="212" frameborder="0" src="//www.travelpayouts.com/widgets/0a4ef006ece369f2a7ed2a01b0fa3587.html?v=494"></iframe>',
            700:  '<iframe scrolling="no" width="600" height="287" frameborder="0" src="//www.travelpayouts.com/widgets/0620d8a83157058447705f3c82619e89.html?v=494"></iframe>',
            400:  '<iframe scrolling="no" width="300" height="432" frameborder="0" src="//www.travelpayouts.com/widgets/9688ec5a88ba662def75540434d3919d.html?v=494"></iframe>'
        },
        'th': {
            1300: '<iframe scrolling="no" width="900" height="212" frameborder="0" src="//www.travelpayouts.com/widgets/be684bdb94d66eceb3beca7214979c53.html?v=494"></iframe>',
            700:  '<iframe scrolling="no" width="600" height="287" frameborder="0" src="//www.travelpayouts.com/widgets/c30789cb3b8b8eb255ef8028005eb899.html?v=494"></iframe>',
            400:  '<iframe scrolling="no" width="300" height="432" frameborder="0" src="//www.travelpayouts.com/widgets/bc2ca9e0134622364d9249f4f70affa9.html?v=494"></iframe>'
        }
    };
    function updateAffiliate() {
        var elFlights = $("#international");
        var elHotels = $("#hotel");
        var winWidth = $(window).width();
        var lastWidth = 400;
        for (var i in sizes) {
            if (winWidth >= sizes[i]) {
                lastWidth = sizes[i];
                break;
            }
        }
        if (elFlights.data('lastWidth') != lastWidth) {
            elFlights.data('lastWidth', lastWidth);
            elFlights.html(flights[LOCALE][lastWidth]);
            elHotels.html(hotels[LOCALE][lastWidth]);
        }
    };
    $(window).resize(function () {
        updateAffiliate();
    });
    updateAffiliate();
});
/**
 * @return {number}
 */
function NASort(a, b) {
    if (a.innerHTML == 'NA') {
        return 1;
    }
    else if (b.innerHTML == 'NA') {
        return -1;
    }
    return (a.innerHTML > b.innerHTML) ? 1 : -1;
}

/**
 * @return {number}
 */
function ResultSort(a, b) {
    var priceA = parseFloat($(a).find('.price').text());
    var timeA = $(a).find('.departTime').text();
    var priceB = parseFloat($(b).find('.price').text());
    var timeB = $(b).find('.departTime').text();
    return (priceA == priceB) ? ((timeA > timeB) ? 1 : -1) : ((priceA > priceB) ? 1 : -1);
}

String.prototype.apply = function (data) {
    var string = this.toString();
    for (var key in data) {
        var pattern = new RegExp('\{' + key + '\}', 'g');
        string = string.replace(pattern, data[key]);
    }
    return string;
};