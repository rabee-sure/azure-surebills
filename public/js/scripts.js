/* Dore Theme Select & Initializer Script 

Table of Contents

01. Css Loading Util
02. Theme Selector And Initializer
*/

/* 01. Css Loading Util */
function loadStyle(href, callback) {
  for (var i = 0; i < document.styleSheets.length; i++) {
    if (document.styleSheets[i].href == href) {
      return;
    }
  }
  var head = document.getElementsByTagName("head")[0];
  var link = document.createElement("link");
  link.rel = "stylesheet";
  link.type = "text/css";
  link.href = href;
  if (callback) {
    link.onload = function () {
      callback();
    };
  }
  var mainCss = $(head).find('[href$="main.css"]');
  if (mainCss.length !== 0) {
    mainCss[0].before(link);
  } else {
    head.appendChild(link);
  }
}

/* 02. Theme Selector, Layout Direction And Initializer */
(function ($) {
  if ($().dropzone) {
    Dropzone.autoDiscover = false;
  }

  var themeColorsDom = '<div class="theme-colors"><div class="p-4"><p class="text-muted mb-2">Light Theme</p><div class="d-flex flex-row justify-content-between mb-4"><a href="#" data-theme="dore.light.blue.min.css" class="theme-color theme-color-blue"></a><a href="#" data-theme="dore.light.purple.min.css" class="theme-color theme-color-purple"></a><a href="#" data-theme="dore.light.green.min.css" class="theme-color theme-color-green"></a><a href="#" data-theme="dore.light.orange.min.css" class="theme-color theme-color-orange"></a><a href="#" data-theme="dore.light.red.min.css" class="theme-color theme-color-red"></a></div><p class="text-muted mb-2">Dark Theme</p><div class="d-flex flex-row justify-content-between"><a href="#" data-theme="dore.dark.blue.min.css" class="theme-color theme-color-blue"></a><a href="#" data-theme="dore.dark.purple.min.css" class="theme-color theme-color-purple"></a><a href="#" data-theme="dore.dark.green.min.css" class="theme-color theme-color-green"></a><a href="#" data-theme="dore.dark.orange.min.css" class="theme-color theme-color-orange"></a><a href="#" data-theme="dore.dark.red.min.css" class="theme-color theme-color-red"></a></div></div><div class="p-4"><p class="text-muted mb-2">Border Radius</p><div class="custom-control custom-radio custom-control-inline"><input type="radio" id="roundedRadio" name="radiusRadio" class="custom-control-input radius-radio" data-radius="rounded"><label class="custom-control-label" for="roundedRadio">Rounded</label></div><div class="custom-control custom-radio custom-control-inline"><input type="radio" id="flatRadio" name="radiusRadio" class="custom-control-input radius-radio" data-radius="flat"><label class="custom-control-label" for="flatRadio">Flat</label></div></div><div class="p-4"><p class="text-muted mb-2">Direction</p><div class="custom-control custom-radio custom-control-inline"><input type="radio" id="ltrRadio" name="directionRadio" class="custom-control-input direction-radio" data-direction="ltr"><label class="custom-control-label" for="ltrRadio">Ltr</label></div><div class="custom-control custom-radio custom-control-inline"><input type="radio" id="rtlRadio" name="directionRadio" class="custom-control-input direction-radio" data-direction="rtl"><label class="custom-control-label" for="rtlRadio">Rtl</label></div></div><a href="#" class="theme-button"> <i class="simple-icon-magic-wand"></i> </a></div>';

  /* $("body").append(themeColorsDom); */


  /* Default Theme Color, Border Radius and  Direction */
  var theme = "dore.light.green.min.css";
  var direction = "ltr";
  var radius = "rounded";

  if (typeof Storage !== "undefined") {
    if (localStorage.getItem("dore-theme")) {
      theme = localStorage.getItem("dore-theme");
    } else {
      localStorage.setItem("dore-theme", theme);
    }
    if (localStorage.getItem("dore-direction")) {
      direction = localStorage.getItem("dore-direction");
    } else {
      localStorage.setItem("dore-direction", direction);
    }
    if (localStorage.getItem("dore-radius")) {
      radius = localStorage.getItem("dore-radius");
    } else {
      localStorage.setItem("dore-radius", radius);
    }
  }

  $(".theme-color[data-theme='" + theme + "']").addClass("active");
  $(".direction-radio[data-direction='" + direction + "']").attr("checked", true);
  $(".radius-radio[data-radius='" + radius + "']").attr("checked", true);
  $("#switchDark").attr("checked", theme.indexOf("dark") > 0 ? true : false);

  loadStyle("/css/" + theme, onStyleComplete);
  function onStyleComplete() {
    setTimeout(onStyleCompleteDelayed, 300);
  }

  function onStyleCompleteDelayed() {
    $("body").addClass(direction);
    $("html").attr("dir", direction);
    $("body").addClass(radius);
    $("body").dore();
  }

  $("body").on("click", ".theme-color", function (event) {
    event.preventDefault();
    var dataTheme = $(this).data("theme");
    if (typeof Storage !== "undefined") {
      localStorage.setItem("dore-theme", dataTheme);
      window.location.reload();
    }
  });

  $("input[name='directionRadio']").on("change", function (event) {
    var direction = $(event.currentTarget).data("direction");
    if (typeof Storage !== "undefined") {
      localStorage.setItem("dore-direction", direction);
      window.location.reload();
    }
  });

  $("input[name='radiusRadio']").on("change", function (event) {
    var radius = $(event.currentTarget).data("radius");
    if (typeof Storage !== "undefined") {
      localStorage.setItem("dore-radius", radius);
      window.location.reload();
    }
  });

  $("#switchDark").on("change", function (event) {
    var mode = $(event.currentTarget)[0].checked ? "dark" : "light";
    if (mode == "dark") {
      theme = theme.replace("light", "dark");
    } else if (mode == "light") {
      theme = theme.replace("dark", "light");
    }
    if (typeof Storage !== "undefined") {
      localStorage.setItem("dore-theme", theme);
      window.location.reload();
    }
  });

  $(".theme-button").on("click", function (event) {
    event.preventDefault();
    $(this)
      .parents(".theme-colors")
      .toggleClass("shown");
  });

  $(document).on("click", function (event) {
    if (
      !(
        $(event.target)
          .parents()
          .hasClass("theme-colors") ||
        $(event.target)
          .parents()
          .hasClass("theme-button") ||
        $(event.target).hasClass("theme-button") ||
        $(event.target).hasClass("theme-colors")
      )
    ) {
      if ($(".theme-colors").hasClass("shown")) {
        $(".theme-colors").removeClass("shown");
      }
    }
  });
})(jQuery);



$(window).scroll(function() {
  var header = $(document).scrollTop();
  var headerHeight = $("#header_fixed").outerHeight();
  if (header > headerHeight) {
    $("#header_fixed").addClass("sticky");
  } else {
    $("#header_fixed").removeClass("sticky");
  }
});
 

$('input[type="email"]').keypress(function (e) {
  var regex = new RegExp("^[A-Za-z0-9\s!@#$%^&*()_+=-`~\\\]\[{}|';:/.,?><]*$");
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (regex.test(str)) {
    return true;
  }
  e.preventDefault();
  return false;
});
String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  var regex;
  for (var i = 0; i < find.length; i++) {
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }
  return replaceString;
};
String.prototype.toArabicDigits = function() {
  var find = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩',' ','-','/','|','~','٫','@'];
  var replace = ['0','1','2','3','4','5','6','7','8','9','','','','','','.','@'];
  return this.replaceArray(find, replace);
}
String.prototype.toArabic = function() {
  var find = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','@'];
  var replace = ['0','1','2','3','4','5','6','7','8','9','@'];
  return this.replaceArray(find, replace);
}
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
        document.getElementById(id).value = document.getElementById(id).value;
        //.replace(/^0+/, '');
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
$(document).on('keyup', 'input._parseArabicNumbers', function() {
  parseArabicNumbers($(this).attr('id'))
});
$(document).on('keyup', 'input._parseQuantityArabicNumbers', function() {
  parseArabicNumbers($(this).attr('id'), 1)
});
$(document).on('keyup', 'input._parseArabic', function() {
  parseArabic($(this).attr('id'))
});
function replaceToArabicNumber(str_input) {
  var replace = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','@'];
  var find = ['0','1','2','3','4','5','6','7','8','9','@'];
  for (var i = 0; i < find.length; i++) {
    str_input = str_input.replace(find[i], replace[i]);
  }
  return str_input;
}