/* global Awesomplete */
var suggestions = window.MunicipioElasticsearchAutosuggestOptions.suggestions;

var AutoSuggest = AutoSuggest !== undefined ? AutoSuggest : {};
AutoSuggest.Front = AutoSuggest.Front || {};

AutoSuggest.Front.suggest = (function () {
  function Suggest() {
    this.init();
    this.cachedResponses = {}; // Query bases responses
    this.lastQuery = "";
  }

  Suggest.prototype.debounce = function (func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
        args = arguments;
      var later = function () {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  };

  Suggest.prototype.init = function () {
    this.awesomeLists = [];
    this.inputs = [];
    this.populateAutoComplete(suggestions);
  };

  Suggest.prototype.populateAutoComplete = function (words) {
    this.inputs = document.querySelectorAll("input.awesomplete");
    for (var i = 0; i < this.inputs.length; i++) {
      var input = this.inputs[i];
      var awesomeplete = new Awesomplete(input, { list: words });
      Awesomplete.$.bind(input, {
        "awesomplete-selectcomplete": this.handleAwesompleteSelection,
      });
      this.awesomeLists[i] = awesomeplete;
      input.addEventListener("keyup", this.handleEventInput(i));
    }
  };

  Suggest.prototype.handleAwesompleteSelection = function (e) {
    var input = e.target;
    var form = input.closest("form");
    if (form) {
      form.submit();
    }
  };

  Suggest.prototype.isArrowButtons = function (which) {
    switch (which) {
      case 37:
      case 38:
      case 39:
      case 40:
        return true;
      default:
        return false;
    }
  };

  Suggest.prototype.handleEventInput = function (index) {
    var self = this;
    return this.debounce(function (e) {
      var query = e.target.value;
      if (self.isArrowButtons(e.which) || self.lastQuery === query) {
        // No change made (dont re render)
        return;
      }
      self.lastQuery = query;
      self.autoSuggest(query, function (list) {
        for (var i = 0; i < self.awesomeLists.length; i++) {
          var awesomeList = self.awesomeLists[i];
          awesomeList.list = list;
        }
      });
    }, 150);
  };

  Suggest.prototype.arrayUnique = function (arr) {
    return arr.filter(function (item, index) {
      return arr.indexOf(item) >= index;
    });
  };

  Suggest.prototype.autoSuggest = function (q, onDone) {
    var self = this;
    if (typeof onDone === "function" && self.cachedResponses[q] !== undefined) {
      return onDone(self.cachedResponses[q]);
    }
    var list = suggestions.map(function (value) {
      return value.toLowerCase();
    });
    list = self.arrayUnique(list);
    self.cachedResponses[q] = list;
    if (typeof onDone === "function") {
      onDone(list);
    }
  };

  return new Suggest();
})();
