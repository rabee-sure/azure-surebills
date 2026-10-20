{{-- Run immediately before the Vue bundle. Server HTML inside #app is compiled as a Vue template, so stored "{{ ... }}" would be executed or crash the page. --}}
@verbatim
<script>
(function () {
  var root = document.getElementById('app');
  if (!root) return;

  function hasMustache(value) {
    return value.indexOf('{{') !== -1 || value.indexOf('}}') !== -1;
  }

  function isDirective(name) {
    return name.indexOf('v-') === 0 || name.charAt(0) === ':' || name.charAt(0) === '@';
  }

  function hasVueBinding(el) {
    var attrs = el.attributes;
    for (var i = 0; i < attrs.length; i++) {
      if (isDirective(attrs[i].name)) return true;
    }
    return false;
  }

  Array.prototype.slice.call(root.querySelectorAll('*')).forEach(function (el) {
    if (el.hasAttribute('v-pre')) return;
    var tag = el.tagName;
    if (tag === 'SCRIPT' || tag === 'STYLE') return;
    var vueBound = hasVueBinding(el);
    var component = tag.indexOf('-') !== -1;
    if (!vueBound && (tag === 'TEXTAREA' || tag === 'OPTION') && hasMustache(el.textContent || '')) {
      el.setAttribute('v-pre', '');
      return;
    }

    var mustacheAttr = false;
    var i;
    for (i = 0; i < el.attributes.length; i++) {
      if (!isDirective(el.attributes[i].name) && hasMustache(el.attributes[i].value)) {
        mustacheAttr = true;
        break;
      }
    }
    if (!mustacheAttr) return;

    if (!vueBound && !component && !el.querySelector('*')) {
      el.setAttribute('v-pre', '');
      return;
    }

    for (i = 0; i < el.attributes.length; i++) {
      var attr = el.attributes[i];
      if (isDirective(attr.name) || !hasMustache(attr.value)) continue;
      el.setAttribute(attr.name, attr.value.split('{{').join('{\u200B{').split('}}').join('}\u200B}'));
    }
  });

  var textNodes = [];
  var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
  var node;
  while ((node = walker.nextNode())) textNodes.push(node);

  textNodes.forEach(function (textNode) {
    if (!hasMustache(textNode.nodeValue)) return;
    var parent = textNode.parentNode;
    if (!parent) return;
    var parentTag = parent.tagName;
    if (parentTag === 'SCRIPT' || parentTag === 'STYLE' || parentTag === 'TEXTAREA' || parentTag === 'OPTION') return;
    if (parent.closest && parent.closest('[v-pre]')) return;
    var span = document.createElement('span');
    span.setAttribute('v-pre', '');
    parent.insertBefore(span, textNode);
    span.appendChild(textNode);
  });
})();
</script>
@endverbatim
