'use strict';

let autocomplete;
let autocompleteInitialized = false;
let cityFallbackInitialized = false;

document.addEventListener('DOMContentLoaded', () => {
    injectSpinnerStyles();
    setupEstimationFormValidation();
    setupCountUpAnimations();
    setupScrollAnimations();
    setupSelectVisualState();
    setupSmoothScrollAnchors();
    setupGoogleAutocompleteFallbackWatcher();
});

function injectSpinnerStyles() {
    if (document.getElementById('loading-spinner-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'loading-spinner-styles';
    style.textContent = `
        .btn-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 999px;
            display: inline-block;
            animation: spin .7s linear infinite;
        }

        .field-check-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #16a34a;
            font-size: 14px;
            font-weight: 700;
            pointer-events: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;

    document.head.appendChild(style);
}

function setupEstimationFormValidation() {
    const form = document.getElementById('estimationForm');
    if (!form) {
        return;
    }

    const fields = form.querySelectorAll('input, select, textarea');

    fields.forEach((field) => {
        field.addEventListener('input', () => validateField(field, false));
        field.addEventListener('change', () => validateField(field, false));
        field.addEventListener('blur', () => validateField(field, true));

        if (field.tagName === 'SELECT') {
            updateSelectColor(field);
        }
    });

    form.addEventListener('submit', (event) => {
        let formIsValid = true;

        fields.forEach((field) => {
            const valid = validateField(field, true);
            if (!valid) {
                formIsValid = false;
            }
        });

        if (!formIsValid) {
            event.preventDefault();
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton) {
            return;
        }

        if (submitButton.dataset.loading === 'true') {
            event.preventDefault();
            return;
        }

        submitButton.dataset.loading = 'true';
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>Analyse en cours...</span>';
    });
}

function validateField(field, showErrorOnBlur) {
    if (!field || field.type === 'hidden') {
        return true;
    }

    const value = field.value.trim();
    const isRequired = field.required;
    const isEmptyRequired = isRequired && value === '';
    const isInvalid = !field.checkValidity() || isEmptyRequired;

    field.classList.remove('border-green-500', 'border-red-400');

    if (isInvalid) {
        removeCheckIcon(field);
        if (showErrorOnBlur || value !== '') {
            field.classList.add('border-red-400');
        }
        return false;
    }

    if (value !== '') {
        field.classList.add('border-green-500');
        addCheckIcon(field);
    } else {
        removeCheckIcon(field);
    }

    return true;
}

function addCheckIcon(field) {
    const wrapper = field.parentElement;
    if (!wrapper) {
        return;
    }

    let icon = wrapper.querySelector('.field-check-icon');
    if (!icon) {
        icon = document.createElement('span');
        icon.className = 'field-check-icon';
        icon.textContent = '✓';
        wrapper.appendChild(icon);
    }
}

function removeCheckIcon(field) {
    const wrapper = field.parentElement;
    if (!wrapper) {
        return;
    }

    const icon = wrapper.querySelector('.field-check-icon');
    if (icon) {
        icon.remove();
    }
}

function animateCountUp(element, target, duration = 1200) {
    const startTime = performance.now();

    const step = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const value = Math.floor(progress * target);

        element.textContent = value.toLocaleString('fr-FR');

        if (progress < 1) {
            requestAnimationFrame(step);
        } else {
            element.textContent = target.toLocaleString('fr-FR');
        }
    };

    requestAnimationFrame(step);
}

function setupCountUpAnimations() {
    const countElements = document.querySelectorAll('.count-up[data-target]');
    if (!countElements.length) {
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const element = entry.target;
            const target = Number.parseInt(element.dataset.target || '0', 10);
            const duration = Number.parseInt(element.dataset.duration || '1200', 10);

            if (!Number.isNaN(target)) {
                animateCountUp(element, target, Number.isNaN(duration) ? 1200 : duration);
            }

            obs.unobserve(element);
        });
    }, { threshold: 0.35 });

    countElements.forEach((element) => observer.observe(element));
}

function setupScrollAnimations() {
    const animated = document.querySelectorAll('.animate-fade-in-up');
    if (!animated.length) {
        return;
    }

    animated.forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity .6s ease-out, transform .6s ease-out';
    });

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('visible');
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            obs.unobserve(entry.target);
        });
    }, { threshold: 0.12 });

    animated.forEach((el) => observer.observe(el));
}

function setupGoogleAutocompleteFallbackWatcher() {
    const input = document.getElementById('adresseAutocomplete') || document.querySelector('input[name="adresse"]');
    if (!input) {
        return;
    }

    setTimeout(() => {
        if (!autocompleteInitialized) {
            setupAddressSuggestions();
        }
    }, 3000);
}

/**
 * Callback Google Maps Places Autocomplete.
 */
function initAutocomplete() {
    const input = document.getElementById('adresseAutocomplete');
    if (!input || !window.google || !google.maps || !google.maps.places) {
        setupAddressSuggestions();
        return;
    }

    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['address'],
        componentRestrictions: { country: 'fr' },
        fields: ['address_components', 'geometry', 'formatted_address', 'place_id'],
    });

    if (window.siteBounds) {
        const b = window.siteBounds;
        const bounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(b.south, b.west),
            new google.maps.LatLng(b.north, b.east),
        );
        autocomplete.setBounds(bounds);
        autocomplete.setOptions({ strictBounds: false });
    }

    autocompleteInitialized = true;

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place || !place.geometry) {
            return;
        }

        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) {
                el.value = value ?? '';
            }
        };

        setValue('adresseComplete', place.formatted_address || input.value);
        setValue('latitude', place.geometry.location?.lat ? place.geometry.location.lat() : '');
        setValue('longitude', place.geometry.location?.lng ? place.geometry.location.lng() : '');
        setValue('placeId', place.place_id || '');

        let ville = '';
        let codePostal = '';
        let departement = '';

        (place.address_components || []).forEach((component) => {
            const types = component.types || [];

            if (types.includes('locality') && !ville) {
                ville = component.long_name;
            }

            if (types.includes('administrative_area_level_2')) {
                if (!ville) {
                    ville = component.long_name;
                }
                departement = component.long_name;
            }

            if (types.includes('postal_code')) {
                codePostal = component.long_name;
            }
        });

        setValue('villeDetectee', ville);
        setValue('codePostal', codePostal);
        setValue('departement', departement);

        input.classList.add('validated', 'border-green-500');

        const confirmation = document.getElementById('adresseConfirmation');
        if (confirmation) {
            confirmation.textContent = `✓ Adresse identifiée : ${ville || 'Ville non détectée'}${codePostal ? `, ${codePostal}` : ''}`;
            confirmation.classList.remove('hidden');
        }
    });
}

window.initAutocomplete = initAutocomplete;

/**
 * Fallback : suggestions hardcodées si Google Maps indisponible.
 */
function setupAddressSuggestions() {
    if (cityFallbackInitialized) {
        return;
    }

    const adresseInput = document.getElementById('adresseAutocomplete') || document.querySelector('input[name="adresse"]');
    if (!adresseInput) {
        return;
    }

    cityFallbackInitialized = true;

    const cities = ['Bordeaux', 'Paris', 'Lyon', 'Nantes', 'Toulouse', 'Marseille', 'Lille', 'Nice', 'Strasbourg', 'Montpellier'];

    const dropdown = document.createElement('div');
    dropdown.className = 'absolute left-0 right-0 top-full z-50 mt-2 hidden rounded-xl border border-gray-200 bg-white shadow-lg';

    const parent = adresseInput.parentElement;
    if (!parent) {
        return;
    }

    if (!parent.classList.contains('relative')) {
        parent.classList.add('relative');
    }

    parent.appendChild(dropdown);

    const hideDropdown = () => {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
    };

    adresseInput.addEventListener('input', () => {
        const query = adresseInput.value.trim().toLowerCase();

        if (query.length < 2) {
            hideDropdown();
            return;
        }

        const matches = cities.filter((city) => city.toLowerCase().includes(query)).slice(0, 5);
        if (!matches.length) {
            hideDropdown();
            return;
        }

        dropdown.innerHTML = '';

        matches.forEach((city) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'block w-full cursor-pointer px-4 py-2.5 text-left hover:bg-gray-50';
            item.textContent = city;

            item.addEventListener('click', () => {
                adresseInput.value = city;
                const villeHidden = document.getElementById('villeDetectee');
                if (villeHidden) {
                    villeHidden.value = city;
                }

                hideDropdown();
                validateField(adresseInput, false);
            });

            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!parent.contains(event.target)) {
            hideDropdown();
        }
    });
}

function setupSelectVisualState() {
    const selects = document.querySelectorAll('select');
    selects.forEach((select) => {
        updateSelectColor(select);

        select.addEventListener('change', () => {
            updateSelectColor(select);
        });
    });
}

function updateSelectColor(select) {
    if (!select) {
        return;
    }

    if (select.value && !select.options[select.selectedIndex]?.disabled) {
        select.classList.remove('text-gray-400');
        select.classList.add('text-gray-900');
    } else {
        select.classList.remove('text-gray-900');
        select.classList.add('text-gray-400');
    }
}

function setupSmoothScrollAnchors() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const href = anchor.getAttribute('href');
            if (!href || href === '#') {
                return;
            }

            const target = document.querySelector(href);
            if (!target) {
                return;
            }

            event.preventDefault();
            const yOffset = -80;
            const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;

            window.scrollTo({ top: y, behavior: 'smooth' });
        });
    });
}
