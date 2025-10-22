const productSection = document.getElementById('product-selection');
const productCards = document.getElementById('productCards');
const productCardTemplate = document.getElementById('productCardTemplate');
const formSection = document.getElementById('form-section');
const stepperContainer = document.getElementById('stepper');
const stepContainer = document.getElementById('stepContainer');
const btnPrev = document.getElementById('btnPrev');
const btnNext = document.getElementById('btnNext');

const state = {
    productId: null,
    productName: '',
    steps: [],
    currentStep: 0
};

async function fetchProducts() {
    try {
        const response = await fetch('api/products.php');
        const data = await response.json();
        if (data.success) {
            renderProducts(data.products);
        } else {
            throw new Error('Sigorta ürünleri yüklenemedi');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Hata', 'Ürün listesi alınırken bir hata oluştu.', 'error');
    }
}

function renderProducts(products) {
    productCards.innerHTML = '';
    products.forEach(product => {
        const card = productCardTemplate.content.firstElementChild.cloneNode(true);
        const icon = card.querySelector('.card__icon');
        if (product.image_path) {
            icon.innerHTML = `<img src="${product.image_path}" alt="${product.name}">`;
        } else {
            icon.textContent = product.name.charAt(0).toUpperCase();
        }
        card.querySelector('.card__title').textContent = product.name;
        card.querySelector('.card__description').textContent = product.description;
        card.addEventListener('click', () => selectProduct(product));
        productCards.appendChild(card);
    });
}

async function selectProduct(product) {
    state.productId = product.id;
    state.productName = product.name;
    try {
        const response = await fetch(`api/form.php?product_id=${product.id}`);
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Form yüklenemedi');
        }
        if (!data.steps || data.steps.length === 0) {
            Swal.fire('Uyarı', 'Bu sigorta türü için henüz form adımları tanımlanmamış.', 'warning');
            return;
        }
        state.steps = data.steps;
        state.currentStep = 0;
        renderForm();
        productSection.hidden = true;
        formSection.hidden = false;
        updateButtons();
    } catch (error) {
        console.error(error);
        Swal.fire('Hata', 'Form adımları yüklenirken bir hata oluştu.', 'error');
    }
}

function renderForm() {
    stepperContainer.innerHTML = '';
    stepContainer.innerHTML = '';

    state.steps.forEach((step, index) => {
        const stepperItem = document.createElement('div');
        stepperItem.className = 'stepper__item';
        stepperItem.innerHTML = `<span class="stepper__index">${index + 1}</span><span>${step.step_title}</span>`;
        stepperContainer.appendChild(stepperItem);

        const stepElement = document.createElement('div');
        stepElement.className = 'form-step';
        stepElement.dataset.index = index;
        stepElement.hidden = index !== state.currentStep;

        step.fields.forEach(field => {
            stepElement.appendChild(renderField(field));
        });

        stepContainer.appendChild(stepElement);
    });

    updateStepper();
}

function renderField(field) {
    const wrapper = document.createElement('div');
    wrapper.className = 'form-group';
    wrapper.dataset.fieldId = field.id;

    const label = document.createElement('label');
    label.textContent = field.label + (field.is_required ? ' *' : '');
    wrapper.appendChild(label);

    let input;

    switch (field.field_type) {
        case 'textarea':
            input = document.createElement('textarea');
            input.rows = 3;
            input.className = 'form-control';
            break;
        case 'dropdown':
            input = document.createElement('select');
            input.className = 'form-control';
            const options = field.options ? field.options.split(',').map(opt => opt.trim()) : [];
            input.innerHTML = '<option value="">Seçiniz</option>' + options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
            break;
        case 'checkbox':
            input = document.createElement('input');
            input.type = 'checkbox';
            input.className = 'form-control';
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.className = 'checkbox-group';
            checkboxWrapper.appendChild(input);
            const checkboxLabel = document.createElement('span');
            checkboxLabel.textContent = 'Evet';
            checkboxWrapper.appendChild(checkboxLabel);
            wrapper.appendChild(checkboxWrapper);
            break;
        default:
            input = document.createElement('input');
            input.type = mapFieldType(field.field_type);
            input.className = 'form-control';
            break;
    }

    if (field.field_type !== 'checkbox') {
        input.placeholder = field.placeholder || '';
        wrapper.appendChild(input);
    }

    input.name = `field_${field.id}`;
    input.dataset.required = field.is_required ? '1' : '0';
    input.dataset.label = field.label;
    input.dataset.type = field.field_type;

    const error = document.createElement('div');
    error.className = 'error-message hidden';
    wrapper.appendChild(error);

    return wrapper;
}

function mapFieldType(fieldType) {
    switch (fieldType) {
        case 'email':
            return 'email';
        case 'phone':
            return 'tel';
        case 'number':
            return 'number';
        case 'date':
            return 'date';
        default:
            return 'text';
    }
}

function updateStepper() {
    const items = stepperContainer.querySelectorAll('.stepper__item');
    items.forEach((item, index) => {
        item.classList.toggle('active', index === state.currentStep);
    });
}

function showStep(index) {
    const steps = stepContainer.querySelectorAll('.form-step');
    steps.forEach((step, idx) => {
        step.hidden = idx !== index;
    });
    state.currentStep = index;
    updateStepper();
    updateButtons();
}

function updateButtons() {
    btnPrev.disabled = state.currentStep === 0;
    btnPrev.classList.toggle('hidden', state.currentStep === 0);

    if (state.currentStep === state.steps.length - 1) {
        btnNext.textContent = 'Teklif Al';
    } else {
        btnNext.textContent = 'İleri';
    }
}

function validateStep(index) {
    const step = stepContainer.querySelector(`.form-step[data-index="${index}"]`);
    const inputs = step.querySelectorAll('.form-control');
    let isValid = true;

    inputs.forEach(input => {
        const required = input.dataset.required === '1';
        const error = input.parentElement.querySelector('.error-message') || input.closest('.form-group').querySelector('.error-message');
        if (!error) return;
        error.classList.add('hidden');
        input.classList.remove('error');

        if (required) {
            let value = '';
            if (input.type === 'checkbox') {
                value = input.checked ? '1' : '';
            } else {
                value = input.value.trim();
            }
            if (!value) {
                error.textContent = 'Lütfen bu alanı doldurun.';
                error.classList.remove('hidden');
                input.classList.add('error');
                isValid = false;
            }
        }
    });

    return isValid;
}

function collectFormData() {
    const payload = {
        product_id: state.productId,
        responses: []
    };

    state.steps.forEach((step, index) => {
        const stepElement = stepContainer.querySelector(`.form-step[data-index="${index}"]`);
        const answers = [];

        step.fields.forEach(field => {
            const input = stepElement.querySelector(`[name="field_${field.id}"]`);
            let value = '';
            if (!input) return;
            if (input.type === 'checkbox') {
                value = input.checked ? 'Evet' : 'Hayır';
            } else {
                value = input.value.trim();
            }
            answers.push({
                field_id: field.id,
                label: field.label,
                value
            });
        });

        payload.responses.push({
            step_id: step.id,
            step_title: step.step_title,
            answers
        });
    });

    return payload;
}

async function submitForm() {
    const payload = collectFormData();
    try {
        const response = await fetch('api/submit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Teklif kaydedilemedi');
        }
        Swal.fire({
            icon: 'success',
            title: 'Teklifiniz Başarıyla Alındı!',
            text: `Uzmanlarımız 5 dakika içinde ${window.MagnusSettings.successPhone} üzerinden sizlere dönüş sağlayacaktır.`,
            confirmButtonText: 'Ana Sayfa'
        }).then(() => {
            window.location.href = window.MagnusSettings.redirectUrl;
        });
    } catch (error) {
        console.error(error);
        Swal.fire('Hata', error.message, 'error');
    }
}

btnPrev.addEventListener('click', () => {
    if (state.currentStep > 0) {
        showStep(state.currentStep - 1);
    }
});

btnNext.addEventListener('click', () => {
    if (!validateStep(state.currentStep)) {
        return;
    }

    if (state.currentStep === state.steps.length - 1) {
        submitForm();
    } else {
        showStep(state.currentStep + 1);
    }
});

fetchProducts();
