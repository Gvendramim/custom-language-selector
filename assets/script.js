(function () {
  function setup(selector) {
    var root = selector;
    var button = root.querySelector('.cls-toggle');
    var menu = root.querySelector('.cls-menu');
    if (!button || !menu) return;

    function open() {
      button.setAttribute('aria-expanded', 'true');
      menu.hidden = false;
      var first = menu.querySelector('a.cls-item');
      if (first) first.focus({ preventScroll: true });
      document.addEventListener('click', onDocClick);
      document.addEventListener('keydown', onKey);
    }
    function close() {
      button.setAttribute('aria-expanded', 'false');
      menu.hidden = true;
      document.removeEventListener('click', onDocClick);
      document.removeEventListener('keydown', onKey);
    }
    function toggle() {
      var expanded = button.getAttribute('aria-expanded') === 'true';
      expanded ? close() : open();
    }
    function onDocClick(e) {
      if (!root.contains(e.target)) close();
    }
    function onKey(e) {
      if (e.key === 'Escape') { close(); button.focus(); }
    }

    button.addEventListener('click', toggle);
    button.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
        e.preventDefault(); open();
      }
    });

    menu.addEventListener('keydown', function (e) {
      var items = Array.prototype.slice.call(menu.querySelectorAll('a.cls-item'));
      var idx = items.indexOf(document.activeElement);
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        var next = items[idx + 1] || items[0];
        if (next) next.focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        var prev = items[idx - 1] || items[items.length - 1];
        if (prev) prev.focus();
      } else if (e.key === 'Home') {
        e.preventDefault();
        if (items[0]) items[0].focus();
      } else if (e.key === 'End') {
        e.preventDefault();
        if (items[items.length - 1]) items[items.length - 1].focus();
      }
    });

    var trocarBtn = Array.from(document.querySelectorAll("button, a"))
      .find(el => el.textContent.trim().toLowerCase().includes("trocar idioma"));

    if (trocarBtn) {
      trocarBtn.addEventListener("click", function () {
        if (window.innerWidth <= 768) {
          button.classList.add("pulse");
          setTimeout(() => button.classList.remove("pulse"), 4000);
        }
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cls-lang-selector').forEach(setup);
  });
})();
