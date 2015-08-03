/**
 * Created by ildar on 02.08.15.
 */
$(document).ready(function () {
    var Router = {
        airports: [],
        routes: [],
        originEl: null,
        destinationEl: null,
        searchForm: $("#searchForm"),
        departureDate: $("#departureDate"),
        submit: $("#submit"),
        results: $("#results"),

        init: function (origin, destination) {
            this.originEl = origin;
            this.destinationEl = destination;

            this.initOrigin();
            this.initSubmit();
        },
        initOrigin: function () {
            this.originEl.find('option').remove();
            this.originEl.append('<option value=""></option>');
            for (var key in this.airports) {
                this.originEl.append('<option value=' + key + '>' + this.airports[key].name + '</option>');
            }
            this.originEl.find('option').sort(NASort).appendTo(this.originEl);

            this.originEl.on('change', function () {
                Router.updateDestination();
            });
        },
        initSubmit: function () {
            this.submit.on('click', $.proxy(function () {
                var data = this.searchForm.serializeArray();
                var dataObj = {};
                $.each(data, function (_, kv) {
                    dataObj[kv.name] = kv.value;
                });

                var routeOrigin = this.routes[dataObj['origin']];
                var routeDestination = null;
                for (var i in routeOrigin) {
                    if (routeOrigin[i].code == dataObj['destination']) {
                        routeDestination = routeOrigin[i];
                        break;
                    }
                }

                this.clearResults();

                var urlTpl = "search/origin/{origin}/destination/{destination}/departureDate/{departureDate}?sourceId={sourceId}";
                for (var i in routeDestination.sources) {
                    var source = routeDestination.sources[i];
                    dataObj['sourceId'] = source;
                    $.get(urlTpl.apply(dataObj), function (data) {
                        this.updateResults(data);
                    }.bind(this));
                }
            }, this));
        },
        updateDestination: function () {
            this.destinationEl.find('option').remove();
            var originCode = this.originEl.val();
            var route = this.routes[originCode];
            for (var i in route) {
                var row = route[i];
                var airport = this.airports[row['code']];
                this.destinationEl.append('<option value=' + airport.code + '>' + airport.name + '</option>');
            }
            this.destinationEl.find('option').sort(NASort).appendTo(this.destinationEl);
        },
        updateResults: function (data) {
            var rowTemplate = '<div class="jumbotron result-row"><div class="text-center col-md-3"><h3><span class="price">{price}</span> THB</h3><button type="button" class="btn btn-lg btn-success" id="submit">BOOK NOW</button></div> <div class="col-md-1"></div><div class="col-md-8"><div class="col-md-12"><h4>{origin_name} <span class="glyphicon glyphicon-arrow-right"></span> {destination_name}</h4></div> <p></p> <div class="col-md-6"><h3 class="departTime">{departTime}</h3></div> <div class="col-md-6"><h3>{arrivalTime}</h3></div> </div> <div class="clearfix"></div> </div>';

            for (var i in data) {
                var row = data[i];
                row['origin_name'] = Router.airports[row.origin].name;
                row['destination_name'] = Router.airports[row.destination].name;
                this.results.append(rowTemplate.apply(row));
            }

            this.results.find('.result-row').sort(ResultSort).appendTo(this.results);
        },
        clearResults: function () {
            this.results.find('.result-row').remove();
        }
    };
    $.getJSON('/bundles/galmiairways/js/airports.json', function (data) {
        Router.airports = data;
        Router.init($("#origin"), $("#destination"));
    });
    $.getJSON('/bundles/galmiairways/js/routes.json', function (data) {
        Router.routes = data;
    });
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
        var pattern = new RegExp('\{' + key + '\}');
        string = string.replace(pattern, data[key]);
    }
    return string;
};