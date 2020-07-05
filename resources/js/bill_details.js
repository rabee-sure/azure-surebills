 $(".bill_payment input[type='radio']").on("change", function() {
  // Regardless of WHICH radio was clicked, is the
  //  showSelect radio active?
   if ($("#visa_pay").is(':checked')) {
     $('.visa_pay_content').removeClass("d-none");
   } else {
     $('.visa_pay_content').addClass("d-none");
   }
 })




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