
/*
'use strict';

var exampleSocket = new WebSocket("ws://localhost:4432");
exampleSocket.onopen = function (event) {
    exampleSocket.send("Can you hear me?");
}
exampleSocket.onmessage = function (event) {
    console.log(event.data);
}

'use strict';

var dom = {
	xhr: {},
	load: function() {
		try {
			if (!this.dom_xhr()) throw 'dom_xhr';
			if (!this.dom_get()) throw 'dom_get';
			return true;
		} catch (e) {
			console.error(e);
		}
    },
	dom_get: function() {
		this.xhr.open('POST', 'ide', true);
		this.xhr.responseType = 'json';
		this.xhr.send();
		this.xhr.addEventListener('load', this.prices_load);
		this.xhr.addEventListener('progress', this.prices_progress);
		this.xhr.addEventListener('error', this.prices_error);
		this.xhr.addEventListener('timeout', this.prices_timeout);
		return true;
    },
	dom_xhr: function () {
		if (window.XMLHttpRequest) {
			// moderner Browser - IE ab version 7
			this.xhr = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			// IE 6
			this.xhr = new ActiveXObject('Microsoft.XMLHTTP');
		}
		return true;
	}
};

document.addEventListener('DOMContentLoaded', () => dom.load());





var prices = {
	xhr: {},
	list: {},
	constructor: function(sDivId) {
		try {
			// get data
			if (!this.prices_xhr()) throw 'prices_xhr';
			if (!this.prices_get()) throw 'prices_get';
			return true;
		} catch (e) {
			console.error(e);
		}
    },
	prices_event: function() {
		return new CustomEvent('PricesLoaded');
	},
	get: function() {
		return this.list;
	},
	prices_set: function(prices) {
		this.list = prices;
		return true;
	},
	prices_load: function(event) {
		if (event.target.status !== 200) return console.log(event.target.status);
		if (event.target.readyState !== 4) return console.log(event.target.readyState);
		if (!window.prices.prices_set(event.target.response)) throw 'prices_set'; 
		document.dispatchEvent(window.prices.prices_event());
		console.log('Laden der Daten abgeschlossen');
	},
	prices_progress: function(event) {
		// TO DO IT!
	},
	prices_error: function(event) {
		console.error(event);
		throw 'Fehler beim Laden der Daten aufgetretn';
	},
	prices_timeout: function(event) {
		console.error(event);
		throw 'Timeout beim Laden der Daten aufgetreten';
	},
	prices_get: function(data = null) {
		this.xhr.open('POST', 'script', true);
		this.xhr.responseType = 'json';
		this.xhr.send(data);
		this.xhr.addEventListener('load', this.prices_load);
		this.xhr.addEventListener('progress', this.prices_progress);
		this.xhr.addEventListener('error', this.prices_error);
		this.xhr.addEventListener('timeout', this.prices_timeout);
		return true;
    },
	prices_xhr: function () {
		if (window.XMLHttpRequest) {
			// moderner Browser - IE ab version 7
			this.xhr = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			// IE 6
			this.xhr = new ActiveXObject('Microsoft.XMLHTTP');
		}
		return true;
	}
};
*/

/*
document.addEventListener('DOMContentLoaded', () => prices.constructor());

const button = document.querySelector("#ide-files-new");

button.addEventListener("click", (event) => {
    prices.prices_get('new');
});
*/