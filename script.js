(function () {
  'use strict';

  var form = document.getElementById('quote-form');
  if (!form) return;

  var status = document.getElementById('form-status');
  var btn = form.querySelector('button[type="submit"]');
  var MSG = {
    sending: 'Saadan…',
    ok: 'Aitäh! Päring on saadetud, helistame teile peagi tagasi.',
    rate: 'Liiga palju päringuid. Proovige mõne minuti pärast või helistage +372 565 1074.',
    fail: 'Saatmine ebaõnnestus. Palun helistage +372 565 1074.'
  };

  function show(text, cls) {
    status.textContent = text;
    status.className = 'form-status' + (cls ? ' ' + cls : '');
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    btn.disabled = true;
    show(MSG.sending);

    fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } })
      .then(function (r) {
        return r.json().catch(function () { return {}; }).then(function (j) { return { status: r.status, ok: r.ok && j.ok }; });
      })
      .then(function (res) {
        if (res.ok) {
          form.reset();
          show(MSG.ok, 'ok');
        } else {
          show(res.status === 429 ? MSG.rate : MSG.fail, 'err');
        }
      })
      .catch(function () { show(MSG.fail, 'err'); })
      .then(function () { btn.disabled = false; });
  });
})();
