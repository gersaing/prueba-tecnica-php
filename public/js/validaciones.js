document.addEventListener('DOMContentLoaded', () => {
  const banner = document.getElementById('campos-obligatorios');

  const form =
    document.getElementById('form-empleado') ||      // create
    document.getElementById('form-empleado-edit') || // update
    document.getElementById('form');          

  if (!form || !banner) return;

  const toInfo  = () => { banner.classList.remove('alert-danger'); banner.classList.add('alert','alert-info'); banner.textContent = 'Los campos con asteriscos (*) son obligatorios'; };
  const toError = msgs => { banner.classList.remove('alert-info'); banner.classList.add('alert','alert-danger'); banner.innerHTML = `<ul class="mb-0">${msgs.map(m=>`<li>${m}</li>`).join('')}</ul>`; };

  const hasServerErrors = banner.classList.contains('alert-danger');
  if (!hasServerErrors) toInfo();
  const ERR_ID = 'client-errors';

 
  const ensureErrorBox = () => {
    let box = document.getElementById(ERR_ID);
    if (!box) {
      box = document.createElement('div');
      box.id = ERR_ID;
      box.className = 'alert alert-danger';
      form.parentNode.insertBefore(box, form); 
    }
    box.innerHTML = '';
    return box;
  };

  
  const markInvalid = (el, msg) => {
    el.classList.add('is-invalid');
    el.setAttribute('aria-invalid', 'true');
    el.dataset.error = msg;
  };
  const clearInvalid = (el) => {
    el.classList.remove('is-invalid');
    el.removeAttribute('aria-invalid');
    el.removeAttribute('data-error');
    el.setCustomValidity('');
  };

  form.addEventListener('input', (e) => clearInvalid(e.target), true);
  form.addEventListener('change', (e) => clearInvalid(e.target), true);

  form.addEventListener('submit', (e) => {
    const errors = [];
    form.classList.add('was-validated');
    if (banner) banner.hidden = true;

 
    const nombre = form.querySelector('[name="nombre"]');
    const email  = form.querySelector('[name="email"]');
    const area   = form.querySelector('[name="area_id"]');
    const desc   = form.querySelector('[name="descripcion"]');
    const sexoChecked = form.querySelector('input[name="sexo"]:checked');
    const sexoRadios  = form.querySelectorAll('input[name="sexo"]');

    // validaciones
    if (!nombre || !nombre.value.trim()) {
      errors.push('El nombre es obligatorio.');
      markInvalid(nombre, 'Obligatorio');
    }

    const emailVal = (email?.value || '').trim();
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailVal || !emailRe.test(emailVal)) {
      errors.push('Correo electrónico inválido.');
      markInvalid(email, 'Inválido');
    }

    if (!sexoChecked) {
      errors.push('Debes seleccionar el sexo.');
      sexoRadios.forEach(r => markInvalid(r, 'Obligatorio'));
    }

    if (!area || !area.value) {
      errors.push('Debes seleccionar un área.');
      markInvalid(area, 'Obligatorio');
    }

    if (!desc || !desc.value.trim()) {
      errors.push('La descripción es obligatoria.');
      markInvalid(desc, 'Obligatorio');
    }

    if (errors.length) {
      e.preventDefault();
      e.stopPropagation();

      if (banner) banner.hidden = false;

      const box = ensureErrorBox();
      box.innerHTML = `<ul class="mb-0">${errors.map(m => `<li>${m}</li>`).join('')}</ul>`;

      const firstInvalid = form.querySelector('.is-invalid, :invalid');
      if (firstInvalid) {
        firstInvalid.focus({ preventScroll: true });
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
});
