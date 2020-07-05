/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/bill_details.js":
/*!**************************************!*\
  !*** ./resources/js/bill_details.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(".bill_payment input[type='radio']").on("change", function () {
  // Regardless of WHICH radio was clicked, is the
  //  showSelect radio active?
  if ($("#visa_pay").is(':checked')) {
    $('.visa_pay_content').removeClass("d-none");
  } else {
    $('.visa_pay_content').addClass("d-none");
  }
});
var card = new Card({
  form: 'form',
  container: '.card-wrapper',
  placeholders: {
    number: '**** **** **** ****',
    name: 'Full Name',
    expiry: '**/****',
    cvc: '***'
  }
});
$('input').keypress(function (e) {
  var regex = new RegExp("^[A-Za-z0-9\s!@#$%^&*()_+=-`~\\\]\[{}|';:/.,?><]*$");
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);

  if (regex.test(str)) {
    return true;
  }

  e.preventDefault();
  return false;
});

String.prototype.replaceArray = function (find, replace) {
  var replaceString = this;
  var regex;

  for (var i = 0; i < find.length; i++) {
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }

  return replaceString;
};

String.prototype.toArabicDigits = function () {
  var find = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', ' ', '-', '/', '|', '~', '٫', '@'];
  var replace = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '', '', '', '', '', '.', '@'];
  return this.replaceArray(find, replace);
};

String.prototype.toArabic = function () {
  var find = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '@'];
  var replace = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '@'];
  return this.replaceArray(find, replace);
};

function parseArabicNumbers(id, dot) {
  document.getElementById(id).value = document.getElementById(id).value.toArabicDigits();
  var new_val = document.getElementById(id).value.toArabicDigits();

  if (new_val >= 0) {
    if (new_val != 0) {
      if (dot == 1) {
        document.getElementById(id).value = Number(new_val).toString();
        document.getElementById(id).value = document.getElementById(id).value.replace(/^0+/, '');
      } else {
        document.getElementById(id).value = new_val;
        document.getElementById(id).value = document.getElementById(id).value; //.replace(/^0+/, '');
      }
    } else {
      document.getElementById(id).value = new_val;
    }
  } else {
    document.getElementById(id).value = "";
  }

  return true;
}

function parseArabic(id) {
  document.getElementById(id).value = document.getElementById(id).value.toArabic();
  var new_val = document.getElementById(id).value.toArabic();

  if (new_val >= 0) {
    document.getElementById(id).value = new_val;
  }

  return true;
}

$(document).on('keyup', 'input._parseArabicNumbers', function () {
  parseArabicNumbers($(this).attr('id'));
});
$(document).on('keyup', 'input._parseQuantityArabicNumbers', function () {
  parseArabicNumbers($(this).attr('id'), 1);
});
$(document).on('keyup', 'input._parseArabic', function () {
  parseArabic($(this).attr('id'));
});

function replaceToArabicNumber(str_input) {
  var replace = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '@'];
  var find = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '@'];

  for (var i = 0; i < find.length; i++) {
    str_input = str_input.replace(find[i], replace[i]);
  }

  return str_input;
}

/***/ }),

/***/ 1:
/*!********************************************!*\
  !*** multi ./resources/js/bill_details.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\wamp64\www\Git_Projects\sure-bills\resources\js\bill_details.js */"./resources/js/bill_details.js");


/***/ })

/******/ });